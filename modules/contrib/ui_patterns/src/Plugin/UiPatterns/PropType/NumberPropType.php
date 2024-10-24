<?php

declare(strict_types=1);

namespace Drupal\ui_patterns\Plugin\UiPatterns\PropType;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ui_patterns\Attribute\PropType;
use Drupal\ui_patterns\PropTypePluginBase;

/**
 * Provides a 'number' PropType.
 */
#[PropType(
  id: 'number',
  label: new TranslatableMarkup('Number'),
  description: new TranslatableMarkup('Either integers or floating point numbers.'),
  default_source: 'number',
  schema: ['type' => ['number', 'integer']],
  priority: 1,
  typed_data: ['decimal', 'float', 'integer', 'timestamp']
)]
class NumberPropType extends PropTypePluginBase {

  /**
   * {@inheritdoc}
   */
  public function getSummary(array $definition): array {
    $summary = parent::getSummary($definition);
    if (isset($definition['type']) && $definition['type'] === "integer") {
      $summary[] = $this->t("Integers only");
    }
    if (isset($definition['minimum'])) {
      $summary[] = $this->t("Minimum: @length", ["@length" => $definition['minimum']]);
    }
    if (isset($definition['maximum'])) {
      $summary[] = $this->t("Maximum: @length", ["@length" => $definition['minimum']]);
    }
    return $summary;
  }

}
