<?php

namespace Drupal\styled_google_map_demo\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Real estate entities.
 *
 * @ingroup styled_google_map_demo
 */
interface RealEstateInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Real estate name.
   *
   * @return string
   *   Name of the Real estate.
   */
  public function getName();

  /**
   * Sets the Real estate name.
   *
   * @param string $name
   *   The Real estate name.
   *
   * @return \Drupal\styled_google_map_demo\Entity\RealEstateInterface
   *   The called Real estate entity.
   */
  public function setName($name);

  /**
   * Gets the Real estate price.
   *
   * @return string
   *   Price of the Real estate.
   */
  public function getPrice();

  /**
   * Sets the Real estate price.
   *
   * @param integer $price
   *   The Real estate price.
   *
   * @return \Drupal\styled_google_map_demo\Entity\RealEstateInterface
   *   The called Real estate entity.
   */
  public function setPrice($price);

  /**
   * Gets the Real estate name.
   *
   * @return \Drupal\taxonomy\TermInterface
   *   Category of the Real estate.
   */
  public function getCategory();

  /**
   * Sets the Real estate category.
   *
   * @param \Drupal\taxonomy\TermInterface $category
   *   The Real estate category.
   *
   * @return \Drupal\styled_google_map_demo\Entity\RealEstateInterface
   *   The called Real estate entity.
   */
  public function setCategory($category);

  /**
   * Gets the Real estate location.
   *
   * @return array
   *   Location of the Real estate as array('lat' => ..., 'lon' => ...).
   */
  public function getLocation();

  /**
   * Sets the Real estate location.
   *
   * @param array $location
   *   The Real estate location as array('lat' => ..., 'lon' => ...).
   *
   * @return \Drupal\styled_google_map_demo\Entity\RealEstateInterface
   *   The called Real estate entity.
   */
  public function setLocation($location);

  /**
   * Gets the Real estate creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Real estate.
   */
  public function getCreatedTime();

  /**
   * Sets the Real estate creation timestamp.
   *
   * @param int $timestamp
   *   The Real estate creation timestamp.
   *
   * @return \Drupal\styled_google_map_demo\Entity\RealEstateInterface
   *   The called Real estate entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Real estate published status indicator.
   *
   * Unpublished Real estate are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Real estate is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Real estate.
   *
   * @param bool $published
   *   TRUE to set this Real estate to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\styled_google_map_demo\Entity\RealEstateInterface
   *   The called Real estate entity.
   */
  public function setPublished($published);

}
