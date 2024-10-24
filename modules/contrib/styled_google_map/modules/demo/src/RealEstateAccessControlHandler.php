<?php

namespace Drupal\styled_google_map_demo;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Real estate entity.
 *
 * @see \Drupal\styled_google_map_demo\Entity\RealEstate.
 */
class RealEstateAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\styled_google_map_demo\Entity\RealEstateInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished real estate entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published real estate entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit real estate entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete real estate entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add real estate entities');
  }

}
