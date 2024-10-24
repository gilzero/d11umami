<?php

declare(strict_types=1);

namespace Drupal\Tests\ui_patterns_devel\Kernel\TwigValidatorRule;

use Drupal\Core\Logger\RfcLogLevel;
use Drupal\Tests\ui_patterns_devel\Kernel\TwigValidatorTestBase;

/**
 * @coversDefaultClass \Drupal\ui_patterns_devel\Plugin\TwigValidatorRule\TwigValidatorRuleParent
 *
 * @group ui_patterns_devel
 * @internal
 *
 * cSpell:disable
 */
final class TwigValidatorRuleParentTest extends TwigValidatorTestBase {

  /**
   * Provides tests data for testTwigValidatorParent.
   *
   * @return array
   *   An array of test data each containing of a twig template source
   *   string, array of error messages and levels expected.
   */
  public static function providerTestTwigValidatorParent() {
    return [
      [
        "{% extends 'links.html.twig' %}
        {% block foo %}
        {{ parent('bar') }}
        {% endblock %}",
        [
          [1, 'Use slots instead of hard embedding a component in the template.', RfcLogLevel::ERROR],
          [3, 'Bad architecture for parent: Component calling components with `parent`.', RfcLogLevel::ERROR],
        ],
      ],
    ];
  }

  /**
   * @covers ::processNode
   *
   * @dataProvider providerTestTwigValidatorParent
   */
  public function testTwigValidatorParent(string $source, array $expected): void {
    $this->runTestSourceTwigValidator($source, $expected);
  }

}
