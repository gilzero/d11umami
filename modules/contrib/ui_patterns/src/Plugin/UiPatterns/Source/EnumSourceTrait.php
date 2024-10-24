<?php

declare(strict_types=1);

namespace Drupal\ui_patterns\Plugin\UiPatterns\Source;

/**
 * Trait for sources handling enum values.
 */
trait EnumSourceTrait {

  /**
   * Converts a source value type to enum data type.
   *
   * @param string $value
   *   The stored.
   * @param array $enum
   *   The defined enums.
   *
   * @return float|int|mixed
   *   The converted value.
   */
  protected function convertValueToEnumType(string $value, array $enum) {
    return match (TRUE) {
      in_array($value, $enum, TRUE) => $value,
      in_array((int) $value, $enum, TRUE)  => (int) $value,
      in_array((float) $value, $enum, TRUE) => (float) $value,
      default => $value,
    };
  }

}
