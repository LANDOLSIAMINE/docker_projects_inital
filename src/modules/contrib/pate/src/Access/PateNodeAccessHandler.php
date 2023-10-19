<?php

namespace Drupal\pate\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\NodeAccessControlHandler;

/**
 * Extends the Node access handler to be able to account for our custom logic.
 */
class PateNodeAccessHandler extends NodeAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  public function access(EntityInterface $entity, $operation, AccountInterface $account = NULL, $return_as_object = FALSE) {
    /** @var \Drupal\node\NodeInterface $entity */
    // Nodes converted into page templates can't be edited.
    if ($operation === 'update' && !empty($entity->pate_is_template->value)) {
      $result = AccessResult::forbidden('Page template nodes cannot be modified.')
        ->addCacheableDependency($entity);
      return $return_as_object ? $result : $result->isAllowed();
    }

    // Nodes converted into page templates can only be deleted by pate admins.
    if ($operation === 'delete' && !empty($entity->pate_is_template->value)) {
      $user = empty($account) ? \Drupal::currentUser() : $account;
      if (!$user->hasPermission('manage page templates')) {
        $result = AccessResult::forbidden('Page template nodes can only be deleted by users with the appropriate permission.')
          ->addCacheableDependency($entity);
        return $return_as_object ? $result : $result->isAllowed();
      }
    }

    return parent::access($entity, $operation, $account, $return_as_object);
  }

}
