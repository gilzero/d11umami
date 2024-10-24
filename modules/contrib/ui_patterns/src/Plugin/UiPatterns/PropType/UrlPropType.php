<?php

declare(strict_types=1);

namespace Drupal\ui_patterns\Plugin\UiPatterns\PropType;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ui_patterns\Attribute\PropType;
use Drupal\ui_patterns\PropTypePluginBase;

/**
 * Provides a 'Url' PropType.
 */
#[PropType(
  id: 'url',
  label: new TranslatableMarkup('Url'),
  description: new TranslatableMarkup('Either a URI or a relative-reference. Can be internationalized.'),
  default_source: 'url',
  schema: ['type' => 'string', 'format' => 'iri-reference'],
  priority: 10,
  typed_data: ['uri']
)]
class UrlPropType extends PropTypePluginBase {

}
