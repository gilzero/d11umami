<?php

namespace Drupal\styled_google_map_demo\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Real estate entity.
 *
 * @ingroup styled_google_map_demo
 *
 * @ContentEntityType(
 *   id = "real_estate",
 *   label = @Translation("Real estate"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\styled_google_map_demo\RealEstateListBuilder",
 *     "views_data" = "Drupal\styled_google_map_demo\Entity\RealEstateViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\styled_google_map_demo\Form\RealEstateForm",
 *       "add" = "Drupal\styled_google_map_demo\Form\RealEstateForm",
 *       "edit" = "Drupal\styled_google_map_demo\Form\RealEstateForm",
 *       "delete" = "Drupal\styled_google_map_demo\Form\RealEstateDeleteForm",
 *     },
 *     "access" = "Drupal\styled_google_map_demo\RealEstateAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\styled_google_map_demo\RealEstateHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "real_estate",
 *   admin_permission = "administer real estate entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/real_estate/{real_estate}",
 *     "add-form" = "/real_estate/add",
 *     "edit-form" = "/real_estate/{real_estate}/edit",
 *     "delete-form" = "/real_estate/{real_estate}/delete",
 *     "collection" = "/admin/content/real_estate",
 *   },
 *   field_ui_base_route = "real_estate.settings"
 * )
 */
class RealEstate extends ContentEntityBase implements RealEstateInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getPrice() {
    return $this->get('price')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setPrice($price) {
    $this->set('price', $price);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getLocation() {
    return ['lat' => $this->get('location')->lat, 'lon' => $this->get('location')->lon];
  }

  /**
   * {@inheritdoc}
   */
  public function setLocation($location) {
    $this->set('location', $location);
    return $this;
  }

  /**
   * Gets the Real estate name.
   *
   * @return \Drupal\taxonomy\TermInterface
   *   Category of the Real estate.
   */
  public function getCategory() {
    return $this->get('category')->entity;
  }

  /**
   * Sets the Real estate category.
   *
   * @param \Drupal\taxonomy\TermInterface $category
   *   The Real estate category.
   *
   * @return \Drupal\styled_google_map_demo\Entity\RealEstateInterface
   *   The called Real estate entity.
   */
  public function setCategory($category) {
    $this->set('category', $category);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isPublished() {
    return (bool) $this->getEntityKey('status');
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished($published) {
    $this->set('status', $published ? TRUE : FALSE);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Real estate entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Real estate entity.'))
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['price'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Price'))
      ->setDescription(t('The price of the Real estate entity.'))
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'number_integer',
        'weight' => -3,
      ])
      ->setDisplayOptions('form', [
        'type' => 'number',
        'weight' => -3,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['location'] = BaseFieldDefinition::create('geofield')
      ->setLabel(t('Location'))
      ->setDescription(t('The location of the Real estate entity.'))
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'geofield_default',
        'weight' => -2,
      ])
      ->setDisplayOptions('form', [
        'type' => 'geofield_latlon',
        'weight' => -2,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['category'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Category'))
      ->setDescription(t('The category of the Real estate entity.'))
      ->setSetting('target_type', 'taxonomy_term')
      ->setSetting('handler', 'default')
      ->setSetting('display_description', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'entity_reference_label',
        'weight' => -2,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => -2,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Real estate is published.'))
      ->setDefaultValue(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => -1,
      ]);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
