<?php

declare(strict_types=1);

namespace Drupal\ui_patterns\Plugin\UiPatterns\PropType;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ui_patterns\Attribute\PropType;
use Drupal\ui_patterns\PropTypePluginBase;

/**
 * Provides a 'boolean' PropType.
 */
#[PropType(
  id: 'boolean',
  label: new TranslatableMarkup('Boolean'),
  description: new TranslatableMarkup('Matches only two special values: true and false.'),
  default_source: 'checkbox',
  schema: ['type' => 'boolean'],
  priority: 1,
  typed_data: ['boolean']
)]
class BooleanPropType extends PropTypePluginBase {

  /**
   * {@inheritdoc}
   */
  public static function normalize(mixed $value): mixed {
    return (bool) $value;
  }

}
