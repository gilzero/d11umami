<?php

namespace Drupal\styled_google_map_demo;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Real estate entities.
 *
 * @ingroup styled_google_map_demo
 */
class RealEstateListBuilder extends EntityListBuilder {


  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Real estate ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\styled_google_map_demo\Entity\RealEstate */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.real_estate.canonical',
      ['real_estate' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
