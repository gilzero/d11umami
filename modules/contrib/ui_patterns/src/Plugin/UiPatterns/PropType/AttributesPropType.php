<?php

declare(strict_types=1);

namespace Drupal\ui_patterns\Plugin\UiPatterns\PropType;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Template\Attribute;
use Drupal\ui_patterns\Attribute\PropType;
use Drupal\ui_patterns\PropTypePluginBase;

/**
 * Provides a 'Attributes' PropType.
 */
#[PropType(
  id: 'attributes',
  label: new TranslatableMarkup('Attributes'),
  description: new TranslatableMarkup('HTML attributes as a a mapping.'),
  default_source: 'attributes',
  schema: [
    'type' => 'object',
    'patternProperties' => [
      '.+' => [
        'anyOf' => [
          ['type' => ['string', 'number']],
          [
            'type' => 'array',
            'items' => [
              'anyOf' => [
              ['type' => 'number'],
              ['type' => 'string'],
              ],
            ],
          ],
        ],
      ],
    ],
  ],
  priority: 10
)]
class AttributesPropType extends PropTypePluginBase {

  /**
   * {@inheritdoc}
   */
  public static function normalize(mixed $value): mixed {
    /*
    Attributes are defined as a mapping ('object' in JSON schema). So, source
    plugins are expected to return a mapping to not break SDC prop validation
    against the prop type schema.
     */
    if (is_a($value, '\Drupal\Core\Template\Attribute')) {
      return $value->toArray();
    }
    return $value;
  }

  /**
   * {@inheritdoc}
   */
  public static function preprocess(mixed $value): mixed {
    /*
    However, when they land in the template, it is safer to have them as
    Attribute objects:
    - if the template use create_attribute(), it will not break thanks to
    "#3403331: Prevent TypeError when using create_attribute Twig function"
    - if the template directly calls object methods, it will work because it
    is already an object
    - ArrayAccess interface allows manipulation as an array.
     */
    if (is_a($value, '\Drupal\Core\Template\Attribute')) {
      return $value;
    }
    if (is_array($value)) {
      return new Attribute($value);
    }
    return new Attribute();
  }

}
