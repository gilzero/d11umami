<?php

declare(strict_types=1);

namespace Drupal\Tests\ui_patterns_devel\Kernel\TwigValidatorRule;

use Drupal\Core\Logger\RfcLogLevel;
use Drupal\Tests\ui_patterns_devel\Kernel\TwigValidatorTestBase;

/**
 * @coversDefaultClass \Drupal\ui_patterns_devel\Plugin\TwigValidatorRule\TwigValidatorRuleGetAttr
 *
 * @group ui_patterns_devel
 * @internal
 *
 * phpcs:disable Drupal.Arrays.Array.LongLineDeclaration
 * cSpell:disable
 */
final class TwigValidatorRuleGetAttrTest extends TwigValidatorTestBase {

  /**
   * Provides tests data for testTwigValidatorFilter.
   *
   * @return array
   *   An array of test data each containing of a twig template source
   *   string, array of error messages and levels expected.
   */
  public static function providerTestTwigValidatorFilter() {
    return [
      // No errors or warning, correct usage.
      [
        "{% set foo = ['foo', 'bar'] %}
        {{ foo['bar'] }}",
        [],
      ],
      [
        "{% set foo = ['foo', '#bar'] %} {{ foo['#bar'] }}",
        [
          [1, 'Keep slots opaque by not manipulating renderables in the template.', RfcLogLevel::WARNING],
        ],
      ],
      [
        "{% set test_prop_object = {} %} {{ test_prop_object.bundle() }}",
        [
          [1, 'Direct method call are forbidden.', RfcLogLevel::ERROR],
        ],
      ],
    ];
  }

  /**
   * @covers ::processNode
   *
   * @dataProvider providerTestTwigValidatorFilter
   */
  public function testTwigValidatorFilter(string $source, array $expected): void {
    $this->runTestSourceTwigValidator($source, $expected);
  }

}
