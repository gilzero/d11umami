<?php

declare(strict_types=1);

namespace Drupal\ui_patterns\Plugin\UiPatterns\PropType;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ui_patterns\Attribute\PropType;
use Drupal\ui_patterns\PropTypePluginBase;

/**
 * Provides a 'Variant' PropType.
 */
#[PropType(
  id: 'variant',
  label: new TranslatableMarkup('Variant'),
  description: new TranslatableMarkup('Prop type for component variants.'),
  default_source: 'select',
  schema: ['type' => ['string'], 'enum' => []],
  priority: 1
)]
class VariantPropType extends PropTypePluginBase {

}
