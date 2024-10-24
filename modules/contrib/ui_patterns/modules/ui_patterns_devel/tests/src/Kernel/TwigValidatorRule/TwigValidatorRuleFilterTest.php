<?php

declare(strict_types=1);

namespace Drupal\Tests\ui_patterns_devel\Kernel\TwigValidatorRule;

use Drupal\Core\Logger\RfcLogLevel;
use Drupal\Tests\ui_patterns_devel\Kernel\TwigValidatorTestBase;

/**
 * @coversDefaultClass \Drupal\ui_patterns_devel\Plugin\TwigValidatorRule\TwigValidatorRuleFilter
 *
 * @group ui_patterns_devel
 * @internal
 *
 * phpcs:disable Drupal.Arrays.Array.LongLineDeclaration,Drupal.Commenting.InlineComment.SpacingBefore,Drupal.Files.LineLength.TooLong
 * cSpell:disable
 */
final class TwigValidatorRuleFilterTest extends TwigValidatorTestBase {

  /**
   * Provides tests data for testTwigValidatorFilter.
   *
   * @return array
   *   An array of test data each containing of a twig template source
   *   string, array of error messages and levels expected.
   */
  public static function providerTestTwigValidatorFilter() {
    return [
      [
        "{% set my_var = [] %}{% set foo = '' %}
        {{ my_var | set_attribute('quux', 'foo') }}
        {{ my_var | set_attribute('foo', 'bar') | set_attribute('bar', 'baz') }}
        {{ my_var | set_attribute('qux', ['foo', 'bar']) }}
        {{ my_var | add_class('foo') | add_class('bar') }}
        {{ 'foo' | t }}
        {{ 12.33 | abs }}
        {{ 'aZe!5$&é' | clean_id }}
        {{ my_var | default('foo') }}
        {{ my_var.foo | default('foo') }}
        {{ my_var['foo'] | default('foo') }}
        {{ '' | default('foo') }}
        {{ foo | default() }}
        {{ 'foo' | default() }}
        ",
        [],
      ],
      [
        "{{ 'foo' | join(',') | set_attribute('bar', 'baz') }}
        {{ 'foo' | set_attribute('bar', {'baz': 'qux'}) }}
        {{ 'foo' | set_attribute('bar', null) }}
        {{ 'foo' | add_class('bar') }}
        {{ '' | t }}
        {{ '' | trans }}
        {{ true | abs -}}
        {# Test warning #}
        {{- 'foo' | clean_unique_id }}
        {{ 'foo' | escape }}
        {{ [] | filter((v, k) => k) }}
        {{ [1, 2, 3] | reduce((c, v) => c + v) -}}
        {% set content = [] %}{{- content | without('links') -}}
        {{ 3 | clean_id }}
        {{ -5 | clean_id }}
        {{ 2.33 | clean_id }}
        {{ true | clean_id }}
        {{ {} | render }}
        {{ {} | without('test') }}
        {{ '' | date_modify('+1 day') | date('Y-M-d') }}
        {{ 1669324282 | format_date('html_date') }}
        {{ true | default('error') }}
        {{ false | default('error') }}
        {{ null | default('error') }}
        {% set foo = 'foo' %}{{ foo | default(false) }}
        {{ foo | default(true) }}
        {{ foo | default(foo) }}
        {% set bar = 'bar' %}{{ foo | default(bar) }}
        {% set my_var = 'foo' %}{{ my_var | t }}
        {% trans %}Submitted on {{ my_var | placeholder }}{% endtrans %}
        {{ 'foo'|convert_encoding('UTF-8', 'iso-2022-jp') }}
        ",
        [
          [1, 'Filter `set_attribute` do not allow previous filter: `join`!', RfcLogLevel::ERROR],
          [2, 'Filter `set_attribute` second argument can not be a mapping!', RfcLogLevel::ERROR],
          [3, 'Filter `set_attribute` second argument can not be null!', RfcLogLevel::ERROR],
          [4, 'Filter `add_class` can not be used on `string`, only `mapping`!', RfcLogLevel::ERROR],
          [5, 'Filter `trans` or `t` is applied on an empty string', RfcLogLevel::NOTICE],
          [6, 'Filter `trans` or `t` is applied on an empty string', RfcLogLevel::NOTICE],
          [7, 'Filter `abs` can only be applied on number, boolean found!', RfcLogLevel::ERROR],
          [9, 'Careful with Twig filter: `clean_unique_id`. ', RfcLogLevel::WARNING],
          [11, 'Careful with Twig filter: `filter`. Functional programming may be overkill.', RfcLogLevel::WARNING],
          [12, 'Careful with Twig filter: `reduce`. Functional programming may be overkill.', RfcLogLevel::WARNING],
          [13, 'Forbidden Twig filter: `without`. Avoid `without` filter on slots, which must stay opaque. Allowed with attributes objects until #3296456 is fixed.', RfcLogLevel::ERROR],
          [14, 'Filter `clean_id` can only be applied on string!', RfcLogLevel::ERROR],
          [15, 'Filter `clean_id` can only be applied on string!', RfcLogLevel::ERROR],
          [16, 'Filter `clean_id` can only be applied on string!', RfcLogLevel::ERROR],
          [17, 'Filter `clean_id` can only be applied on string!', RfcLogLevel::ERROR],
          [18, 'Forbidden Twig filter: `render`. Please ensure you are not rendering content too early.', RfcLogLevel::ERROR],
          [19, 'Forbidden Twig filter: `without`. Avoid `without` filter on slots, which must stay opaque. Allowed with attributes objects until #3296456 is fixed.', RfcLogLevel::ERROR],
          [20, 'Forbidden Twig filter: `date`. PHP object manipulation must be avoided.', RfcLogLevel::ERROR],
          [20, 'Forbidden Twig filter: `date_modify`. PHP object manipulation must be avoided.', RfcLogLevel::ERROR],
          [21, 'Forbidden Twig filter: `format_date`. Business related. Load config entities.', RfcLogLevel::ERROR],
          [22, 'Filter `default` is not for booleans or null!', RfcLogLevel::ERROR],
          [23, 'Filter `default` is not for booleans or null!', RfcLogLevel::ERROR],
          [24, 'Filter `default` is not for booleans or null!', RfcLogLevel::ERROR],
          [25, "Don't use `default` filter with boolean.", RfcLogLevel::WARNING],
          [26, "Don't use `default` filter with boolean.", RfcLogLevel::WARNING],
          [27, 'Filter `default` return the value itself!', RfcLogLevel::WARNING],
          [29, 'Filter `trans` or `t` unsafe translation, do not translate variables!', RfcLogLevel::NOTICE],
          [30, 'Forbidden Twig filter: `placeholder`. Forbidden Twig filter: `placeholder`. Keep components sandboxed by avoiding functions calling Drupal application.', RfcLogLevel::ERROR],
          [31, 'Careful with Twig filter: `convert_encoding`. Needs specific PHP extension.', RfcLogLevel::WARNING],
        ],
      ],
      [
        "{{ {'#theme': 'foo'} | add_suggestion('bar') }}",
        [
          [1, 'Forbidden Twig filter: `add_suggestion`. Keep components sandboxed by avoiding functions calling Drupal application.', RfcLogLevel::ERROR],
        ],
      ],
      [
        "{{ 'foo' | abs }}",
        [
          [1, 'Filter `abs` can only be applied on number, string found!', RfcLogLevel::ERROR],
        ],
      ],
      [
        "{{ ['foo'] | t }}
         {{ ['foo'] | trans }}",
        [
          [1, 'Filter `trans` or `t` can only be applied on string!', RfcLogLevel::ERROR],
          [2, 'Filter `trans` or `t` can only be applied on string!', RfcLogLevel::ERROR],
        ],
      ],
      // Can not run for now.
      // Reason: PHP deprecation notice, always fail for PHPunit 9+.
      // [
      //   "{{ null | abs }}",
      //   [
      //     [1, 'Filter `abs` can only be applied on number, string found!', RfcLogLevel::ERROR],
      //     [1, 'An exception has been thrown during the rendering of a template', RfcLogLevel::CRITICAL],
      //   ],
      // ],
      // [
      //   "{{ true | abs }}",
      //     [1, 'Filter `abs` can only be applied on number, string found!', RfcLogLevel::ERROR],
      //     [1, 'An exception has been thrown during the rendering of a template', RfcLogLevel::CRITICAL],
      // ],.
    ];
  }

  /**
   * @covers ::processNode
   * @covers ::abs
   * @covers ::addClass
   * @covers ::cleanId
   * @covers ::default
   * @covers ::validateFilterExpression
   * @covers ::setAttribute
   * @covers ::t
   *
   * @dataProvider providerTestTwigValidatorFilter
   */
  public function testTwigValidatorFilter(string $source, array $expected): void {
    $this->runTestSourceTwigValidator($source, $expected);
  }

}
