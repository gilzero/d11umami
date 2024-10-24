<?php

namespace Drupal\ui_patterns_field_formatters\Plugin\Derivative;

use Drupal\Component\Plugin\PluginBase;
use Drupal\ui_patterns\Plugin\Derivative\EntityFieldSourceDeriverBase;

/**
 * Provides derivable context for every field.
 */
class FieldFormatterSourceDeriver extends EntityFieldSourceDeriverBase {

  /**
   * {@inheritdoc}
   */
  protected function getDerivativeDefinitionsForEntityBundleField(string $entity_type_id, string $bundle, string $field_name, array $base_plugin_derivative): void {
    $id = implode(PluginBase::DERIVATIVE_SEPARATOR, [
      $entity_type_id,
      $bundle,
      $field_name,
    ]);
    $this->derivatives[$id] = array_merge(
      $base_plugin_derivative,
      [
        "id" => $id,
        "label" => $this->t("Field formatter"),
        "description" => $this->t("Output of a field formatter."),
        "metadata" => array_merge($base_plugin_derivative["metadata"], [
          'field_formatter' => TRUE,
        ]),
        "tags" => array_merge($base_plugin_derivative["tags"], ["field_formatter"]),
        "prop_types" => ["slot"],
      ]);
  }

  /**
   * {@inheritdoc}
   */
  protected function getDerivativeDefinitionsForEntityStorageField(string $entity_type_id, string $field_name, array $base_plugin_derivative): void {
    $id = implode(PluginBase::DERIVATIVE_SEPARATOR, [
      $entity_type_id,
      "",
      $field_name,
    ]);
    $this->derivatives[$id] = array_merge(
      $base_plugin_derivative,
      [
        "id" => $id,
        "label" => $this->t("Field formatter"),
        "description" => $this->t("Output of a field formatter."),
        "metadata" => array_merge($base_plugin_derivative["metadata"], [
          'field_formatter' => TRUE,
        ]),
        "tags" => array_merge($base_plugin_derivative["tags"], ["field_formatter"]),
        "prop_types" => ["slot"],
      ]);
  }

}
