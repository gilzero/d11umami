<?php

declare(strict_types=1);

namespace Drupal\Tests\ui_patterns_devel\Kernel\TwigValidatorRule;

use Drupal\Core\Logger\RfcLogLevel;
use Drupal\Tests\ui_patterns_devel\Kernel\TwigValidatorTestBase;

/**
 * @coversDefaultClass \Drupal\ui_patterns_devel\Plugin\TwigValidatorRule\TwigValidatorRuleInclude
 *
 * @group ui_patterns_devel
 * @internal
 *
 * cSpell:disable
 */
final class TwigValidatorRuleIncludeTest extends TwigValidatorTestBase {

  /**
   * Provides tests data for testTwigValidatorInclude.
   *
   * @return array
   *   An array of test data each containing of a twig template source
   *   string, array of error messages and levels expected.
   */
  public static function providerTestTwigValidatorInclude() {
    return [
      [
        "{% include 'links.html.twig' %}
        {% include('links.html.twig') %}
        {% embed 'links.html.twig' %}{% endembed %}
        ",
        [
          [1, 'Use slots instead of hard embedding a component in the template with `include`.', RfcLogLevel::WARNING],
          [2, 'Use slots instead of hard embedding a component in the template with `include`.', RfcLogLevel::WARNING],
          [3, 'Use slots instead of hard embedding a component in the template with `embed`.', RfcLogLevel::WARNING],
        ],
      ],
    ];
  }

  /**
   * @covers ::processNode
   *
   * @dataProvider providerTestTwigValidatorInclude
   */
  public function testTwigValidatorInclude(string $source, array $expected): void {
    $this->runTestSourceTwigValidator($source, $expected);
  }

}
