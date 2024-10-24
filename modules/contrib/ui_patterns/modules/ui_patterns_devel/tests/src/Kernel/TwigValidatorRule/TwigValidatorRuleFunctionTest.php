<?php

declare(strict_types=1);

namespace Drupal\Tests\ui_patterns_devel\Kernel;

use Drupal\Core\Logger\RfcLogLevel;

/**
 * @coversDefaultClass \Drupal\ui_patterns_devel\Plugin\TwigValidatorRule\TwigValidatorRuleFunction
 *
 * @group ui_patterns_devel
 * @internal
 *
 * phpcs:disable Drupal.Arrays.Array.LongLineDeclaration,Drupal.Commenting.InlineComment.SpacingBefore
 *
 * cSpell:disable
 */
final class TwigValidatorRuleFunctionTest extends TwigValidatorTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'help',
    'ui_patterns_library',
    'ui_patterns_devel_module_test',
  ];

  /**
   * Provides tests data for testTwigValidatorFunction.
   *
   * @return array
   *   An array of test data each containing of a twig template source
   *   string, array of error messages and levels expected.
   */
  public static function providerTestTwigValidatorFunction() {
    return [
      [
        "{% set quux = 'quux' %}
        {% set foo = 'foo-' ~ quux|default(random()) %}
        {% set foo = quux|default(random()) %}
        {% set foo = quux|default(foo ~ '-' ~ random()) %}",
        [],
      ],
      [
        "{{ component_story('ui_patterns_devel_theme_test:foo', 'preview') }}",
        [
          [1, 'Careful with Twig function: `component_story`. Not expected in real usage components.', RfcLogLevel::WARNING],
        ],
      ],
      [
        "{{ pattern('ui_patterns_devel_theme_test:foo') }}",
        [
          [1, 'Deprecated Twig function: `pattern`. Replace with Twig function component().', RfcLogLevel::WARNING],
        ],
      ],
      [
        "{{ source('test.html', true) }}
        {{ active_theme() }}
        {{ active_theme_path() }}
        {{ constant('PHP_VERSION') }}
        {% set date = date('-2days', 'Europe/Paris') %}
        {{ file_url('public://foo.txt') }}
        {{ path('<front>') }}
        {{ source('test.html', true) }}
        {{ validate_component_props('ui_patterns_devel_theme_test:foo') }}
        {{ add_component_context('ui_patterns_devel_theme_test:foo') }}
        ",
        [
          [0, 'Unused variables: date', RfcLogLevel::ERROR],
          [1, 'Careful with Twig function: `source`. Bad architecture, but sometimes needed for shared static files.', RfcLogLevel::WARNING],
          [2, 'Forbidden Twig function: `active_theme`. Keep components sandboxed by avoiding functions calling Drupal application.', RfcLogLevel::ERROR],
          [3, 'Forbidden Twig function: `active_theme_path`. Keep components sandboxed by avoiding functions calling Drupal application.', RfcLogLevel::ERROR],
          [4, 'Forbidden Twig function: `constant`. Keep components sandboxed by avoiding functions calling Drupal application.', RfcLogLevel::ERROR],
          [5, 'Forbidden Twig function: `date`. Too business &amp; l10n related.', RfcLogLevel::ERROR],
          [6, 'Forbidden Twig function: `file_url`. Should avoid using.', RfcLogLevel::ERROR],
          [7, 'Forbidden Twig function: `path`. Keep components sandboxed by avoiding functions calling Drupal application.', RfcLogLevel::ERROR],
          [8, 'Careful with Twig function: `source`. Bad architecture, but sometimes needed for shared static files.', RfcLogLevel::WARNING],
          [9, 'Forbidden Twig function: `validate_component_props`. Development only.', RfcLogLevel::ERROR],
          [10, 'Forbidden Twig function: `add_component_context`. Development only.', RfcLogLevel::ERROR],
        ],
      ],
      [
        "{{ help_topic_link('foo') }}",
        [
          [1, 'Forbidden Twig function: `help_topic_link`. Bad architecture: Help Drupal module only.', RfcLogLevel::ERROR],
        ],
      ],
      [
        "{{ attach_library('system/maintenance') }}",
        [
          [1, 'The asset library attachment would be more discoverable if declared in the component definition.', RfcLogLevel::ERROR],
        ],
      ],
      [
        "{{ pattern_preview('ui_patterns_devel_theme_test:foo', 'preview') }}",
        [
          [1, 'Forbidden Twig function: `pattern_preview`. Legacy UI Patterns 1, not expected in real usage components.', RfcLogLevel::ERROR],
        ],
      ],
      [
        "{{ url('<front>') }}",
        [
          [1, 'Forbidden Twig function: `url`. Keep components sandboxed by avoiding functions calling Drupal application.', RfcLogLevel::ERROR],
        ],
      ],
      [
        "{{ link('foo', 'http://foo.bar') }}",
        [
          [1, 'Forbidden Twig function: `link`. PHP URL object, or useless if URL string.', RfcLogLevel::ERROR],
        ],
      ],
      [
        "{% set quux = 'foo' %}{% set baz = 'baz' %}
        {% set foo = random() %}
        {{ foo ~ random() }}
        {% set bar = baz ~ '--' ~ random() %}
        {{ random() }}
        {% set qux = 'foo-' ~ quux ~ random() %}
        {# valid #}
        {% set qux = 'foo-' ~ quux|default(random()) %}
        {% set qux = quux|default(random()) %}
        {% set qux = quux|default(foo ~ '-' ~ random()) %}
        {{ bar }}{{ qux }}
         ",
        [
          [2, 'Function `random()` must be used in a `default()` filter!', RfcLogLevel::ERROR],
          [3, 'Function `random()` must be used in a `default()` filter!', RfcLogLevel::ERROR],
          [4, 'Function `random()` must be used in a `default()` filter!', RfcLogLevel::ERROR],
          [5, 'Function `random()` must be used in a `default()` filter!', RfcLogLevel::ERROR],
          [6, 'Function `random()` must be used in a `default()` filter!', RfcLogLevel::ERROR],
        ],
      ],
    ];
  }

  /**
   * @covers ::processNode
   * @covers ::random
   *
   * @dataProvider providerTestTwigValidatorFunction
   */
  public function testTwigValidatorFunction(string $source, array $expected): void {
    $this->runTestSourceTwigValidator($source, $expected);
  }

}
