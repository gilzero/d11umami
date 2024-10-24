<?php

declare(strict_types=1);

namespace Drupal\ui_patterns\Plugin\UiPatterns\PropType;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ui_patterns\Attribute\PropType;
use Drupal\ui_patterns\PropTypePluginBase;

/**
 * Provides a 'enum' PropType.
 */
#[PropType(
  id: 'enum',
  label: new TranslatableMarkup('Enum'),
  description: new TranslatableMarkup('A single value restricted to a fixed set of values.'),
  default_source: 'select',
  schema: ['type' => ['string', 'number', 'integer'], 'enum' => []],
  priority: 10
)]
class EnumPropType extends PropTypePluginBase {

  /**
   * {@inheritdoc}
   */
  public function getSummary(array $definition): array {
    $summary = parent::getSummary($definition);
    if (isset($definition['enum']) && !isset($definition['meta:enum'])) {
      $summary[] = $this->t("Values: @values", ["@values" => implode(", ", $definition['enum'])]);
    }
    if (isset($definition['enum']) && isset($definition['meta:enum'])) {
      $summary[] = $this->t("Values: @values", ["@values" => implode(", ", $definition['meta:enum'])]);
    }
    return $summary;
  }

}
