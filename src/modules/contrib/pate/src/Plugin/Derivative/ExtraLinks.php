<?php

namespace Drupal\pate\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Extension\ThemeHandlerInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\Core\Routing\RouteProviderInterface;
use Drupal\Core\State\StateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\system\Entity\Menu;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a default implementation for menu link plugins.
 */
class ExtraLinks extends DeriverBase implements ContainerDeriverInterface {

  const MAX_TEMPLATE_LINKS = 10;

  use StringTranslationTrait;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The route provider.
   *
   * @var \Drupal\Core\Routing\RouteProviderInterface
   */
  protected $routeProvider;

  /**
   * The DB connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * The Drupal State.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * {@inheritdoc}
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, ModuleHandlerInterface $module_handler, RouteProviderInterface $route_provider, Connection $database, StateInterface $state) {
    $this->entityTypeManager = $entity_type_manager;
    $this->moduleHandler = $module_handler;
    $this->routeProvider = $route_provider;
    $this->database = $database;
    $this->state = $state;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('module_handler'),
      $container->get('router.route_provider'),
      $container->get('database'),
      $container->get('state')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $links = [];
    // Nothing to do here if there are no templates in the system.
    $templates_bundles = $this->database
      ->query("SELECT DISTINCT type FROM {node_field_data} WHERE pate_is_template=1")
      ->fetchCol();
    if (empty($templates_bundles)) {
      return $links;
    }
    if ($this->moduleHandler->moduleExists('admin_toolbar_tools')) {
      foreach ($templates_bundles as $type) {
        $content_type = $this->entityTypeManager->getStorage('node_type')->load($type);
        if (!$content_type) {
          continue;
        }
        // Add a "Create from Blank" link.
        $weight = -100;
        $links["node.add_blank.{$type}"] = [
          'title' => $this->t('New blank @type', ['@type' => $content_type->label()]),
          'route_name' => 'node.add',
          'route_parameters' => ['node_type' => $type],
          'parent' => "admin_toolbar_tools.extra_links:node.add.{$type}",
          'weight' => $weight,
        ] + $base_plugin_definition;
        // For each template, add a new link, up to the max defined here (or
        // overridden in Drupal state).
        $max =  $this->state->get('pate.max_template_links_override') ?? static::MAX_TEMPLATE_LINKS;
        $node_storage = $this->entityTypeManager->getStorage('node');
        $results = $node_storage
          ->getQuery()
          ->condition('pate_is_template', TRUE)
          ->condition('type', $type)
          ->accessCheck(TRUE)
          ->range(0, $max)
          ->sort('created', 'DESC')
          ->execute();
        foreach ($results as $nid) {
          $weight++;
          $node = $node_storage->load($nid);
          $links["node.add_from_template.{$nid}"] = [
            'title' => $node->label(),
            'route_name' => 'pate.create_from_template',
            'route_parameters' => ['node' => $nid],
            'parent' => "admin_toolbar_tools.extra_links:node.add.{$type}",
            'weight' => $weight,
          ] + $base_plugin_definition;
        }
      }
    }

    return $links;
  }

}
