<?php

declare(strict_types=1);

namespace Drupal\Tests\ui_patterns_devel\Kernel\TwigValidatorRule;

use Drupal\Core\Logger\RfcLogLevel;
use Drupal\Tests\ui_patterns_devel\Kernel\TwigValidatorTestBase;

/**
 * @coversDefaultClass \Drupal\ui_patterns_devel\Plugin\TwigValidatorRule\TwigValidatorRuleTest
 *
 * @group ui_patterns_devel
 * @internal
 *
 * cSpell:disable
 */
final class TwigValidatorRuleTestTest extends TwigValidatorTestBase {

  /**
   * Provides tests data for testTwigValidatorTest.
   *
   * @return array
   *   An array of test data each containing of a twig template source
   *   string, array of error messages and levels expected.
   */
  public static function providerTestTwigValidatorTest() {
    return [
      [
        "{% set foo = 'foo' %}
        {% if foo is defined %}{% endif %}
        {% if foo is empty %}{% endif %}
        {{ foo is null }}
        {{ foo ?? 'bar' }}
        {% if foo is same as(false) %}{% endif %}
        ",
        [
          [2, 'Not needed in Drupal because strict_variables=false.', RfcLogLevel::WARNING],
          [3, 'The exact same as just testing the variable, empty is not needed.', RfcLogLevel::WARNING],
          [4, 'Not needed in Drupal because strict_variables=false.', RfcLogLevel::WARNING],
          [5, 'Use `|default(foo)` filter instead of null ternary `??`.', RfcLogLevel::WARNING],
          [6, 'Equivalent to strict comparison in PHP, often too strict.', RfcLogLevel::ERROR],
        ],
      ],
    ];
  }

  /**
   * @covers ::processNode
   *
   * @dataProvider providerTestTwigValidatorTest
   */
  public function testTwigValidatorTest(string $source, array $expected): void {
    $this->runTestSourceTwigValidator($source, $expected);
  }

}
