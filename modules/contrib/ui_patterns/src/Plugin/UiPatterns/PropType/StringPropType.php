<?php

declare(strict_types=1);

namespace Drupal\ui_patterns\Plugin\UiPatterns\PropType;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ui_patterns\Attribute\PropType;
use Drupal\ui_patterns\PropTypePluginBase;

/**
 * Provides a 'string' PropType.
 */
#[PropType(
  id: 'string',
  label: new TranslatableMarkup('String'),
  description: new TranslatableMarkup('Strings of text. May contain Unicode characters.'),
  default_source: 'textfield',
  convert_from: ['number', 'url', 'machine_name'],
  schema: ['type' => 'string'],
  priority: 1,
  typed_data: ['datetime_iso8601', 'email', 'string']
)]
class StringPropType extends PropTypePluginBase {

  /**
   * {@inheritdoc}
   */
  public static function convertFrom(string $prop_type, mixed $value): mixed {
    return match ($prop_type) {
      'boolean' => (string) $value,
      'number' => (string) $value,
      'url' => $value,
      'machine_name' => $value,
      'color' => $value,
      'string' => $value,
    };
  }

  /**
   * {@inheritdoc}
   */
  public function getSummary(array $definition): array {
    $summary = parent::getSummary($definition);
    if (isset($definition['maxLength'])) {
      $summary[] = $this->t("Max length: @length", ["@length" => $definition['maxLength']]);
    }
    if (isset($definition['minLength'])) {
      $summary[] = $this->t("Min length: @length", ["@length" => $definition['minLength']]);
    }
    return $summary;
  }

}
