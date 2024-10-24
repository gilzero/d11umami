<?php

declare(strict_types=1);

namespace Drupal\ui_patterns\Plugin\UiPatterns\PropType;

use Drupal\Component\Render\MarkupInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Render\RenderableInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ui_patterns\Attribute\PropType;
use Drupal\ui_patterns\PropTypePluginBase;

/**
 * Provides a 'Slot' PropType.
 */
#[PropType(
  id: 'slot',
  label: new TranslatableMarkup('Slot'),
  description: new TranslatableMarkup('A placeholder inside a component that can be filled with renderables.'),
  default_source: 'component',
  convert_from: ['string'],
  schema: [],
  priority: 10
)]
class SlotPropType extends PropTypePluginBase {

  /**
   * {@inheritdoc}
   */
  public static function normalize(mixed $value): mixed {
    if (is_object($value)) {
      return self::convertObject($value);
    }
    if (is_string($value)) {
      return ['#markup' => $value];
    }
    if (is_array($value) && self::isMappingNotRenderable($value)) {
      // Twig `is sequence` and `is mapping `tests are not useful when a list
      // of renderables has mapping keys (non consecutive, strings) instead of
      // sequence (integer, consecutive) keys. For example a list of blocks
      // from page layout or layout builder: each block is keyed by its UUID.
      // So, transform this list of renderables to a proper Twig sequence.
      return array_values($value);
    }
    return $value;
  }

  /**
   * Is the array a mapping array but not a renderable array?
   */
  protected static function isMappingNotRenderable(array $value): bool {
    return count($value) > 1 && !array_is_list($value) && empty(Element::properties($value));
  }

  /**
   * Convert PHP objects to render array.
   */
  protected static function convertObject(object $value): array {
    if ($value instanceof RenderableInterface) {
      $value = $value->toRenderable();
    }
    if ($value instanceof MarkupInterface) {
      return [
        '#markup' => (string) $value,
      ];
    }
    elseif ($value instanceof \Stringable) {
      return [
        '#plain_text' => (string) $value,
      ];
    }
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public static function convertFrom(string $prop_type, mixed $value): mixed {
    return match ($prop_type) {
      'string' => ($value instanceof MarkupInterface) ? ["#children" => $value] : ["#plain_text" => $value],
    };
  }

}
