<?php

namespace Drupal\styled_google_map_demo\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Real estate entities.
 */
class RealEstateViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.

    return $data;
  }

}
