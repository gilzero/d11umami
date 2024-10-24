<?php

declare(strict_types=1);

namespace Drupal\ui_patterns_legacy;

/**
 * Convert UI Patterns settings to JSON schema.
 */
class PropConverter {

  /**
   * Convert prop.
   */
  public function convert(array $setting): array {
    return match ($setting["type"]) {
      'attributes' => [
        '$ref' => 'ui-patterns://attributes',
      ],
      'boolean' => [
        '$ref' => 'ui-patterns://boolean',
      ],
      'checkboxes' => [
        '$ref' => 'ui-patterns://enum_list',
        'items' => [
          'enum' => \array_keys($setting['options']),
          'meta:enum' => $setting['options'],
        ],
      ],
      'links' => [
        '$ref' => 'ui-patterns://links',
      ],
      'machine_name' => [
        '$ref' => 'ui-patterns://machine_name',
      ],
      'number' => $this->convertNumber($setting),
      'radios' => [
        '$ref' => 'ui-patterns://enum',
        'enum' => \array_keys($setting['options']),
        'meta:enum' => $setting['options'],
      ],
      'select' => [
        '$ref' => 'ui-patterns://enum',
        'enum' => \array_keys($setting['options']),
        'meta:enum' => $setting['options'],
      ],
      'textfield' => [
        '$ref' => 'ui-patterns://string',
      ],
      'token' => [
        '$ref' => 'ui-patterns://string',
      ],
      'url' => [
        '$ref' => 'ui-patterns://url',
      ],
      default => [],
    };
  }

  /**
   * Convert number.
   */
  private function convertNumber(array $setting): array {
    $prop = [
      '$ref' => 'ui-patterns://number',
    ];
    if (isset($setting['min'])) {
      $prop['min'] = $setting['min'];
    }
    if (isset($setting['max'])) {
      $prop['max'] = $setting['max'];
    }
    return $prop;
  }

}
