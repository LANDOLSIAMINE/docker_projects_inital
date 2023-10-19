<?php

namespace Drupal\pate\Form;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\ContentEntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;

/**
 * Confirmation form prior to templatizing a node and vice-versa.
 */
class PateTemplatizeForm extends ContentEntityConfirmFormBase {

  /**
   * The route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, RouteMatchInterface $route_match = NULL) {
    $this->routeMatch = $route_match;
    $node = $this->routeMatch->getParameter('node');
    assert($node instanceof NodeInterface);
    $this->setEntity($node);

    $published = FALSE;
    foreach ($node->getTranslationLanguages() as $translation_language) {
      /** @var \Drupal\node\NodeInterface $translation */
      $translation = $node->getTranslation($translation_language->getId());
      if ($translation->isPublished()) {
        $published = TRUE;
        break;
      }
    }
    if ($published) {
      return [
        '#markup' => $this->t('This node is published! Because templates cannot be modified, you can only convert into a template nodes that are unpublished. Please clone or recreate this content as unpublished version and try again.'),
      ];
    }

    // If this node has paragraph fields (ERR), offer to mark this template as
    // "structure-only".
    $has_paragraphs = FALSE;
    foreach ($node->getFieldDefinitions() as $name => $definition) {
      if ($definition->getType() === 'entity_reference_revisions') {
        $has_paragraphs = TRUE;
        break;
      }
    }
    if ($has_paragraphs && empty($node->pate_is_template->value)) {
      $form['pate_structure_only'] = [
        '#type' => 'radios',
        '#title' => $this->t('Cloning method'),
        '#description' => $this->t('Choosing "all content" will make content created from this template to be an exact copy of the template (including sample content). Choosing "structure only" will reset all field values to their default values (or empty), and only copy structural elements (paragraph fields).'),
        '#options' => [
          "0" => $this->t('Copy all content'),
          "1" => $this->t('Copy the structure only'),
        ],
        '#default_value' => !empty($node->pate_structure_only->value) ? 1 : 0,
      ];
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    /** @var \Drupal\node\NodeInterface $node */
    $node = $this->getEntity();
    $published = FALSE;
    foreach ($node->getTranslationLanguages() as $translation_language) {
      /** @var \Drupal\node\NodeInterface $translation */
      $translation = $node->getTranslation($translation_language->getId());
      if ($translation->isPublished()) {
        $published = TRUE;
        break;
      }
    }
    if ($published) {
      $form_state->setErrorByName('', 'You cannot convert a published node (or a node with a published translation) into a template.');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    /** @var \Drupal\node\NodeInterface $node */
    $node = $this->getEntity();
    // We just want to toggle whatever the flag value currently is.
    $node->pate_is_template->value = empty($node->pate_is_template->value) ? TRUE : FALSE;
    // When converting into a template, set the structure-only flag as well.
    if (!empty($node->pate_is_template->value)) {
      $node->pate_structure_only->value = (bool) $form_state->getValue('pate_structure_only', FALSE);
    }
    $node->setValidationRequired(FALSE);
    $node->save();

    $message = !empty($node->pate_is_template->value) ?
      $this->t('Node %title has been converted into a template. It can no longer be modified, but you can switch this operation back and convert it into a normal node at any point.', ['%title' => $node->getTitle()]) :
      $this->t('Node %title has been converted into a normal node', ['%title' => $node->getTitle()]);
    $this->messenger()->addStatus($message);
    $form_state->setRedirectUrl($this->getCancelUrl());
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    /** @var \Drupal\node\NodeInterface $node */
    $node = $this->getEntity();
    $message = !empty($node->pate_is_template->value) ?
      $this->t('Convert %title back into a normal node', ['%title' => $node->getTitle()]) :
      $this->t('Convert %title into a page template', ['%title' => $node->getTitle()]);
    return $message;
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    /** @var \Drupal\node\NodeInterface $node */
    $node = $this->getEntity();
    $message = !empty($node->pate_is_template->value) ?
      $this->t('This operation will convert this template back into a normal node. Proceed?') :
      $this->t('A template can no longer be modified. Proceed with converting this page into a template?');
    return $message;
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    /** @var \Drupal\node\NodeInterface $node */
    $node = $this->getEntity();
    return !empty($node->pate_is_template->value) ?
      $this->t('Convert into normal node') :
      $this->t('Convert into template');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return Url::fromRoute('entity.node.canonical', ['node' => $this->getEntity()->id()]);
  }

  /**
   * Access handler for this form.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   A RouteMatch object.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public static function access(RouteMatchInterface $route_match) {
    $node = $route_match->getParameter('node');
    if (empty($node)) {
      return AccessResult::forbidden('You cannot templatize something that does not exist.');
    }
    assert($node instanceof NodeInterface);

    $type = \Drupal::entityTypeManager()->getStorage('node_type')->load($node->getType());
    $settings = $type->getThirdPartySettings('pate');
    if (empty($settings['is_templatable'])) {
      return AccessResult::forbidden('This content type does not allow being converted into a page template.')
        ->addCacheableDependency($type);
    }

    $user = \Drupal::currentUser();
    if (!$user->hasPermission('manage page templates')) {
      return AccessResult::forbidden('You do not have permission to templatize this content.')
        ->addCacheableDependency($type)
        ->addCacheableDependency($user);
    }

    return AccessResult::allowed()
      ->addCacheableDependency($type)
      ->addCacheableDependency($user);
  }

}
