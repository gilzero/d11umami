<?php

declare(strict_types=1);

namespace Drupal\ui_patterns\Plugin\UiPatterns\Source;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\Plugin\DataType\EntityReference;
use Drupal\Core\Entity\TranslatableInterface;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Logger\LoggerChannelTrait;
use Drupal\Core\TypedData\Plugin\DataType\Uri;
use Drupal\Core\Url;
use Drupal\text\TextProcessed;
use Drupal\ui_patterns\SourceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base class for source plugins derived from field properties.
 */
abstract class FieldValueSourceBase extends FieldSourceBase implements SourceInterface {

  use LoggerChannelTrait;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition,
  ) {
    // We keep the same constructor as SourcePluginBase.
    $instance = parent::create(
      $container,
      $configuration,
      $plugin_id,
      $plugin_definition
    );
    // Defined in parent class FieldSourceBase.
    $instance->entityFieldManager = $container->get('entity_field.manager');
    $instance->entityTypeManager = $container->get('entity_type.manager');
    return $instance;
  }

  /**
   * Returns the entity.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   The entity
   */
  protected function getEntity(): ?EntityInterface {
    $entity = parent::getEntity();
    if ($entity instanceof EntityInterface) {
      return $entity;
    }
    // Useful in the context of views.
    $field_items = $this->getContextValue("ui_patterns:field:items");
    if ($field_items instanceof FieldItemListInterface) {
      return $field_items->getEntity();
    }
    return $this->sampleEntityGenerator->get($this->getEntityTypeId(), $this->getBundle());
  }

  /**
   * Gets a field item list for the entity and field name in the context.
   *
   * @return \Drupal\Core\Field\FieldItemListInterface|mixed|null
   *   Return the field items of entity.
   */
  protected function getEntityFieldItemList():mixed {
    $field_name = $this->getCustomPluginMetadata('field_name');
    if (empty($field_name)) {
      return NULL;
    }
    /** @var  \Drupal\Core\Entity\ContentEntityBase $entity */
    $entity = $this->getEntity();
    if (!$entity) {
      $field_items = $this->getContextValue('ui_patterns:field:items');
      if ($field_items instanceof FieldItemListInterface) {
        if ($field_items->getFieldDefinition()->getName() == $field_name) {
          return $field_items;
        }
        $entity = $field_items->getEntity();
      }
    }
    if (!$entity) {
      $this->getLogger('ui_patterns')
        ->error('Entity not found in context');
      return NULL;
    }

    if (!$entity->hasField($field_name)) {
      $this->getLogger('ui_patterns')
        ->error('Entity %entity_type %bundle has no field %field_name', [
          '%entity_type' => $entity->getEntityTypeId(),
          '%bundle' => $entity->bundle() ?? "",
          '%field_name' => $field_name,
        ]);
      return NULL;
    }
    return $entity->get($field_name);
  }

  /**
   * Extract a property value from a field item.
   *
   * Entity references will be resolved to their label.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   The field item.
   * @param string $property_name
   *   The source property.
   * @param string $lang_code
   *   The language that should be used to render the field.
   *
   * @return mixed|null
   *   The extracted value or NULL if the field is referencing an entity that
   *   doesn't exist anymore.
   *
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  protected function extractPropertyValue(FieldItemInterface $item, string $property_name, string $lang_code): mixed {
    $property = $item->get($property_name);
    if ($property instanceof TextProcessed) {
      return $property->getValue();
    }
    if ($property instanceof EntityReference) {
      return $this->getEntityReferencedLabel($property, $lang_code);
    }

    $value = $property->getValue();
    if ($value && ($property instanceof Uri)) {
      // $value is a non-empty string.
      return $this->resolveInternalUri($item, $value);
    }
    return $value;
  }

  /**
   * Resolve Uri datatype instead of directly returning the raw value.
   *
   * Raw value may be internal://, public://, private://, entity://...
   */
  protected function resolveInternalUri(FieldItemInterface $item, string $value): string {
    // Most of the time, Uri datatype is met in a "link" field type.
    if ($item->getDataDefinition()->getDataType() === 'field_item:link') {
      $options = (array) $item->get('options')->getValue();
      return Url::fromUri($value, $options)->toString();
    }
    return Url::fromUri($value)->toString();
  }

  /**
   * Returns the label of an entity referenced by a field property.
   */
  protected function getEntityReferencedLabel(EntityReference $property, string $lang_code): mixed {
    // Ensure the referenced entity still exists.
    $referencedEntityTypeId = $property->getTargetDefinition()
      ->getEntityTypeId();
    $referencedEntityId = $property->getTargetIdentifier();
    if (!$referencedEntityId) {
      return NULL;
    }
    $entity = $this->entityTypeManager
      ->getStorage($referencedEntityTypeId)
      ->load($referencedEntityId);

    if (!$entity || !is_object($entity)) {
      return NULL;
    }
    // Drupal loads the entity in its default language and should load
    // the translated one if available.
    $value = $entity->label();
    /** @var \Drupal\Core\Entity\ContentEntityInterface $entity */
    if (($entity instanceof TranslatableInterface)
      && $lang_code
      && $entity->hasTranslation($lang_code)) {
      $translated_entity = $entity->getTranslation($lang_code);
      $value = $translated_entity->label();
    }
    return $value;
  }

  /**
   * Get the settings from the plugin configuration.
   *
   * @param array $parents
   *   An array of parent keys of the value, starting with the outermost key.
   *
   * @return mixed|null
   *   The requested nested value from configuration,
   */
  protected function getSettingsFromConfiguration(array $parents) {
    $configuration = $this->getConfiguration();
    return NestedArray::getValue($configuration, $parents);
  }

}
