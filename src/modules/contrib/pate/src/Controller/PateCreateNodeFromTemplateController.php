<?php

namespace Drupal\pate\Controller;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\NodeInterface;
use Drupal\paragraphs\ParagraphInterface;
use Drupal\replicate\Replicator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Handles the operation of creating a node from an existing template.
 */
class PateCreateNodeFromTemplateController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * The replicator service.
   *
   * @var \Drupal\replicate\Replicator
   */
  protected $replicator;

  /**
   * The time service.
   *
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected $time;

  /**
   * The Date Formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * Constructs a new controller object.
   *
   * @param \Drupal\replicate\Replicator $replicator
   *   The replicator service.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The time service.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   */
  public function __construct(Replicator $replicator, TimeInterface $time, DateFormatterInterface $date_formatter) {
    $this->replicator = $replicator;
    $this->time = $time;
    $this->dateFormatter = $date_formatter;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('replicate.replicator'),
      $container->get('datetime.time'),
      $container->get('date.formatter')
    );
  }

  /**
   * Page callback for creating a new node from template.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The template node.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   */
  public function createNode(NodeInterface $node, Request $request) {
    // Make sure we only go ahead if this user can do what they are trying
    // to do, and this is really a template.
    $user = $this->currentUser();
    if (!$user->hasPermission('use page templates')) {
      throw new AccessDeniedHttpException('You do not have permission to use page templates.');
    }
    if (empty($node->pate_is_template->value)) {
      throw new NotFoundHttpException('This content is not the template you are looking for.');
    }

    $content_type = $this->entityTypeManager()->getStorage('node_type')->load($node->bundle());
    $timezone = date_default_timezone_get();

    // We use ::cloneEntity() instead of ::replicateEntity() because we don't
    // want to trigger the AFTER_SAVE event (which contrib or custom modules
    // could be listening to). Since we are confident enough at this point
    // that all translations of the cloned entity are already unpublished, we
    // can just set our flag and save it now.
    $new_node = $this->replicator->cloneEntity($node);
    $new_node->pate_is_template->value = FALSE;
    $new_node->pate_structure_only->value = FALSE;
    $new_node->title->value = $this->t('New @type (@template_title) - @timestamp', [
      '@type' => $content_type->label(),
      '@template_title' => $new_node->title->value,
      '@timestamp' => $this->dateFormatter->format($this->time->getRequestTime(), 'custom', 'm/d - H:i', $timezone),
    ]);
    // Reset non-structural field values if this template was marked as
    // structure-only.
    if (!empty($node->pate_structure_only->value)) {
      $this->resetNonStructuralFieldValues($new_node);
    }
    $new_node->save();

    // Display a confirmation message.
    $this->messenger()->addStatus($this->t('Editing node created from template.'));

    // Redirect to the edit form of the new node.
    $route_parameters = ['node' => $new_node->id()];
    $options = ['query' => ['pate_template' => $node->id()]];
    return $this->redirect('entity.node.edit_form', $route_parameters, $options);
  }


  /**
   * Unset all configurable field values, except for paragraph relationships.
   *
   * This will loop over all configurable fields on the entity passed in
   * (ignoring base fields and computed fields). If the field is a reference to
   * a paragraph entity, this will be called recursively on that entity. In all
   * other fields, the field value will be unset.
   *
   * As a result, the entity passed in will have all its configurable field
   * values removed, except for references to paragraph items, which will be
   * there, but will also have their field values removed (except for nested
   * paragraphs, which will also be there, but empty. And so on...).
   *
   * @param \Drupal\Core\Entity\FieldableEntityInterface $entity
   *   The entity we want to delete fields on. Will be modified by reference.
   */
  protected function resetNonStructuralFieldValues(FieldableEntityInterface $entity) {
    foreach ($entity->getFieldDefinitions() as $name => $definition) {
      // Leave base fields and computed fields alone.
      if ($definition->getFieldStorageDefinition()->isBaseField() || $definition->isComputed()) {
        continue;
      }
      // If this is an ERR (paragraphs) field, recurse through it.
      if ($definition->getType() === 'entity_reference_revisions'
        && !empty($entity->get($name)->entity)) {
        foreach ($entity->get($name) as $item) {
          if ($item->entity instanceof ParagraphInterface) {
            $this->resetNonStructuralFieldValues($item->entity);
          }
        }
      }
      else {
        // In all other configurable fields, unset the field value, but allow
        // modules to have a say in this first.
        $context = [
          'field_definition' => $definition,
          'entity' => $entity,
        ];
        $skip = $this->moduleHandler()->invokeAll('pate_skip_field_cleanup', $context);
        // If at least one module wants to skip this field, leave it alone.
        if (in_array(TRUE, $skip, TRUE)) {
          continue;
        }
        else {
          // Clean it up! Or better actually, reset to its default value.
          $entity->set($name, $definition->getDefaultValue($entity), FALSE);
        }
      }
    }
    // If this is a paragraph, save it before leaving.
    if ($entity instanceof ParagraphInterface) {
      $entity->save();
    }
  }

  /**
   * Access handler for this route.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   * @param \Drupal\node\NodeInterface $node
   *   The template node.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(AccountInterface $account, NodeInterface $node) {
    if ($node && $node->access('create', $account) && $account->hasPermission('use page templates')) {
      return AccessResult::allowed()
        ->addCacheableDependency($node)
        ->addCacheableDependency($account);
    }
    return AccessResult::forbidden('You cannot create from this template.')
      ->addCacheableDependency($node)
      ->addCacheableDependency($account);
  }

}
