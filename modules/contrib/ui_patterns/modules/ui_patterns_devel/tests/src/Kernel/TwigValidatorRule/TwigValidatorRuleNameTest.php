<?php

declare(strict_types=1);

namespace Drupal\Tests\ui_patterns_devel\Kernel\TwigValidatorRule;

use Drupal\Core\Logger\RfcLogLevel;
use Drupal\Tests\ui_patterns_devel\Kernel\TwigValidatorTestBase;

/**
 * @coversDefaultClass \Drupal\ui_patterns_devel\Plugin\TwigValidatorRule\TwigValidatorRuleName
 *
 * @group ui_patterns_devel
 * @internal
 *
 * phpcs:disable Drupal.Arrays.Array.LongLineDeclaration,Drupal.Commenting.DocComment.MissingShort
 * cSpell:disable.
 */
final class TwigValidatorRuleNameTest extends TwigValidatorTestBase {

  /**
   * Provides tests data for testTwigValidatorName.
   *
   * @return array
   *   An array of test data each containing of a twig template source
   *   string, array of error messages and levels expected.
   */
  public static function providerTestTwigValidatorName() {
    return [
      [
        "{% set foo = 'foo' %}
        {{ foo }}
        {% set bar = ['foo', 'bar'] %}
        {% for key, item in bar %}
          {{ key }}{{ item }}
        {% endfor %}
        {% for item_2 in bar %}
          {{ item_2 }}
        {% endfor %}
        {% set my_attributes = create_attribute() %}
        {{ my_attributes }}
        {# Test injected #}
        {{ attributes }}
        {{ variant }}
        {{ _self }}
        {{ _key }}
        {% set users = [{name: 'foo'},{name: 'bar'}] %}
        {% for user in users %}
          {{ loop.index }} - {{ user.name }}
        {% endfor %}
        ",
        [],
      ],
      [
        "{{ not_set_1 }}
        {% for key, item in not_set_2 %}
          {{ key }}{{ item }}{{ not_set_3 }}
        {% endfor %}
        {% for item in not_set_4 %}
          {{ item_not_set }}
        {% endfor %}
        {% for item in not_set_5 %}
          {{ item }}
        {% endfor %}
        {{ componentMetadata.path }}
        {% for item in ['foo'] %}{{ loop.parent.variant }}{% endfor %}
        ",
        [
          [1, 'Unknown variable: `not_set_1`.', RfcLogLevel::ERROR],
          [2, 'Unknown variable: `not_set_2`.', RfcLogLevel::ERROR],
          [3, 'Unknown variable: `not_set_3`.', RfcLogLevel::ERROR],
          [5, 'Unknown variable: `not_set_4`.', RfcLogLevel::ERROR],
          [6, 'Unknown variable: `item_not_set`.', RfcLogLevel::ERROR],
          [8, 'Unknown variable: `not_set_5`.', RfcLogLevel::ERROR],
          [11, 'Forbidden Twig variable: `componentMetadata`.', RfcLogLevel::ERROR],
          [12, 'Breaking the flow. Bad performance.', RfcLogLevel::ERROR],
        ],
      ],
    ];
  }

  /**
   * @dataProvider providerTestTwigValidatorName
   */
  public function testTwigValidatorName(string $source, array $expected): void {
    $this->runTestSourceTwigValidator($source, $expected);
  }

}
