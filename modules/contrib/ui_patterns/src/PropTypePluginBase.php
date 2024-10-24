<?php

namespace Drupal\ui_patterns;

use Drupal\Component\Plugin\Definition\PluginDefinitionInterface;
use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\DependencyInjection\DependencySerializationTrait;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Base class for prop_type plugins.
 */
abstract class PropTypePluginBase extends PluginBase implements PropTypeInterface {

  use DependencySerializationTrait;
  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function label(): string {
    // Cast the label to a string since it is a TranslatableMarkup object.
    return ($this->pluginDefinition instanceof PluginDefinitionInterface) ? $this->pluginDefinition->id() : (string) ($this->pluginDefinition["label"] ?? '');
  }

  /**
   * {@inheritdoc}
   */
  public static function convertFrom(string $prop_type, mixed $value): mixed {
    return $value;
  }

  /**
   * Get default source ID.
   */
  public function getDefaultSourceId(): string {
    return ($this->pluginDefinition instanceof PluginDefinitionInterface) ? '' : (string) ($this->pluginDefinition["default_source"] ?? '');
  }

  /**
   * {@inheritdoc}
   */
  public function getSchema(): array {
    return ($this->pluginDefinition instanceof PluginDefinitionInterface) ? [] : (array) ($this->pluginDefinition['schema'] ?? []);
  }

  /**
   * {@inheritdoc}
   */
  public function getSummary(array $definition): array {
    $summary = [];
    if (isset($definition['description'])) {
      $summary[] = $definition['description'];
    }
    if (isset($definition['default']) && is_scalar($definition['default'])) {
      $summary[] = $this->t("Default: @default", ["@default" => $definition['default']]);
    }
    if (isset($definition['default']) && is_array($definition['default'])) {
      $summary[] = $this->t("Default: @default", ["@default" => implode(", ", $definition['default'])]);
    }
    if (isset($definition['ui_patterns']['required']) && $definition['ui_patterns']['required']) {
      $summary[] = $this->t("Required");
    }
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public static function normalize(mixed $value): mixed {
    return $value;
  }

  /**
   * {@inheritdoc}
   */
  public static function preprocess(mixed $value): mixed {
    return $value;
  }

}
