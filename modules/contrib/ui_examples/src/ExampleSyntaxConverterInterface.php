<?php

declare(strict_types=1);

namespace Drupal\ui_examples;

/**
 * Example syntax converter.
 *
 * Examples may skip the "#" prefix in render arrays for readability.
 * Let's put them back.
 *
 * Before: ["type" => "component", "component" => "example:card"]
 * After:  ["#type" => "component", "#component" => "example:card"]
 */
interface ExampleSyntaxConverterInterface {

  /**
   * Convert an array.
   *
   * @param array $array
   *   The array to process.
   *
   * @return array
   *   The processed array.
   */
  public function convertArray(array $array): array;

}
