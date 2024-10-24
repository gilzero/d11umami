<?php

declare(strict_types=1);

namespace Drupal\Tests\ui_patterns_devel\Kernel\TwigValidatorRule;

use Drupal\Core\Logger\RfcLogLevel;
use Drupal\Tests\ui_patterns_devel\Kernel\TwigValidatorTestBase;

/**
 * @coversDefaultClass \Drupal\ui_patterns_devel\Plugin\TwigValidatorRule\TwigValidatorRuleNode
 *
 * @group ui_patterns_devel
 * @internal
 *
 * cSpell:disable
 */
final class TwigValidatorRuleNodeTest extends TwigValidatorTestBase {

  /**
   * Provides tests data for testTwigValidatorNode.
   *
   * @return array
   *   An array of test data each containing of a twig template source
   *   string, array of error messages and levels expected.
   */
  public static function providerTestTwigValidatorNode() {
    return [
      [
        "{% sandbox %}Foo{% endsandbox %}
        {% do 1 + 2 %}
        {% flush %}
        {% set foo = false %}{% set bar = 'bar' %}{{ foo ? bar }} {# do false positive #}
        ",
        [
          [1, 'Bad architecture for sandbox: Component calling components.', RfcLogLevel::ERROR],
          [2, 'Careful with do usage.', RfcLogLevel::WARNING],
          [3, 'Cache management outside of Drupal.', RfcLogLevel::ERROR],
        ],
      ],
    ];
  }

  /**
   * @covers ::processNode
   *
   * @dataProvider providerTestTwigValidatorNode
   */
  public function testTwigValidatorNode(string $source, array $expected): void {
    $this->runTestSourceTwigValidator($source, $expected);
  }

}
