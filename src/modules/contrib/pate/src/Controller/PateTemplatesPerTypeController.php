<?php

namespace Drupal\pate\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\node\NodeTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Lists available templates.
 */
class PateTemplatesPerTypeController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Page callback for listing available templates for a given node type.
   *
   * @param string $node_type
   *   The node type we want to query templates for.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   */
  public function list($node_type, Request $request) {
    $type = $this->entityTypeManager()->getStorage('node_type')->load($node_type);
    if (empty($type)) {
      throw new NotFoundHttpException('Could not found the desired content type.');
    }
    assert($type instanceof NodeTypeInterface);

    $build = [
      '#theme' => 'pate_templates_list',
      '#cache' => [
        'tags' => ['node_list:' . $type->id()],
      ],
      '#add_new_url' => Url::fromRoute('node.add', ['node_type' => $type->id()]),
      '#add_new_label' => $this->t('New blank @type', ['@type' => $type->label()]),
      '#templates' => [],
      '#attached' => [
        'library' => ['pate/templates_list'],
      ],
    ];

    $node_storage = $this->entityTypeManager()->getStorage('node');
    $results = $node_storage
      ->getQuery()
      ->condition('type', $type->id())
      ->condition('pate_is_template', TRUE)
      ->accessCheck(TRUE)
      ->sort('created', 'DESC')
      ->execute();
    if (!empty($results)) {
      $nodes = $node_storage->loadMultiple($results);
    }
    else {
      $nodes = [];
    }
    /** @var \Drupal\node\NodeInterface[] $nodes */
    foreach ($nodes as $node) {
      $build['#templates'][] = [
        '#theme' => 'pate_templates_template',
        '#title' => $node->getTitle(),
        '#create_from_template_label' => $this->t('Use this template'),
        '#create_from_template_url' => Url::fromRoute('pate.create_from_template', [
          'node' => $node->id(),
        ])->toString(),
        '#preview_label' => $this->t('Preview'),
        '#preview_url' => Url::fromRoute('pate.template_preview', [
          'node' => $node->id(),
        ])->toString(),
        '#attached' => [
          'library' => ['core/drupal.dialog.ajax'],
        ],
      ];
    }

    return $build;
  }

  /**
   * Title callback for the templates per type page.
   *
   * @param string $node_type
   *   The node type we want to query templates for.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   */
  public function title($node_type, Request $request) {
    $type = $this->entityTypeManager()->getStorage('node_type')->load($node_type);
    if (empty($type)) {
      throw new NotFoundHttpException('Could not found the desired content type.');
    }
    assert($type instanceof NodeTypeInterface);
    return $this->t('Available %type templates', ['%type' => $type->label()]);
  }

}
