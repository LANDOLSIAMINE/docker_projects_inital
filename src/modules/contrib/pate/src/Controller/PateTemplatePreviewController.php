<?php

namespace Drupal\pate\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Displays a template in preview mode.
 */
class PateTemplatePreviewController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Page callback for displaying a preview of a template.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The template node.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   */
  public function preview(NodeInterface $node, Request $request) {
    return [
      '#theme' => 'pate_template_preview',
      '#attached' => [
        'library' => ['pate/template_preview'],
      ],
      '#node' => $node,
      '#node_view_url' => Url::fromRoute('entity.node.canonical', [
        'node' => $node->id(),
      ], [
        'query' => ['pate-template-id' => $node->id()],
      ]),
      '#create_from_template_label' => $this->t('Use this template'),
      '#create_from_template_url' => Url::fromRoute('pate.create_from_template', [
        'node' => $node->id(),
      ])->toString(),
    ];
  }

}
