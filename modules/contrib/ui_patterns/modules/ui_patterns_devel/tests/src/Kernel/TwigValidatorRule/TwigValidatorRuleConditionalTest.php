<?php

declare(strict_types=1);

namespace Drupal\Tests\ui_patterns_devel\Kernel\TwigValidatorRule;

use Drupal\Core\Logger\RfcLogLevel;
use Drupal\Tests\ui_patterns_devel\Kernel\TwigValidatorTestBase;

/**
 * @coversDefaultClass \Drupal\ui_patterns_devel\Plugin\TwigValidatorRule\TwigValidatorRuleConditional
 *
 * @group ui_patterns_devel
 * @internal
 *
 * cSpell:disable
 */
final class TwigValidatorRuleConditionalTest extends TwigValidatorTestBase {

  /**
   * Provides tests data for testTwigValidatorConditional.
   *
   * @return array
   *   An array of test data each containing of a twig template source
   *   string, array of error messages and levels expected.
   */
  public static function providerTestTwigValidatorConditional() {
    return [
      [
        "{% set foo = false %}{% set bar = false %}{% set baz = false %}{% set qux = 'qux' %}{% set quux = 'quux' %}
        {{ foo ? baz : bar ? qux : baz ? qux : quux }}
        {{ foo ? baz : bar ? qux : baz }}
        {{ foo ? baz : bar }}
        {{ foo ? baz }}
        {% set foo = false %}{% set bar = 'bar' %}
        {{ foo ? foo : bar }}
        {{ foo ?: bar }}
        {{ qux == 'bar' ? true : false }}
        ",
        [
          [2, 'No chained ternary', RfcLogLevel::ERROR],
          [2, 'No chained ternary', RfcLogLevel::ERROR],
          [3, 'No chained ternary', RfcLogLevel::ERROR],
          [7, 'Use `|default(foo)` filter instead of shorthand ternary `?:`', RfcLogLevel::WARNING],
          [8, 'Use `|default(foo)` filter instead of shorthand ternary `?:`', RfcLogLevel::WARNING],
          [9, 'Ternary test with boolean result', RfcLogLevel::NOTICE],
        ],
      ],
    ];
  }

  /**
   * @covers ::processNode
   *
   * @dataProvider providerTestTwigValidatorConditional
   */
  public function testTwigValidatorConditional(string $source, array $expected): void {
    $this->runTestSourceTwigValidator($source, $expected);
  }

}
