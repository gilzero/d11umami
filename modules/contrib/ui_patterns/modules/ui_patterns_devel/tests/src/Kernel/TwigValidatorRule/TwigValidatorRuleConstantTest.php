<?php

declare(strict_types=1);

namespace Drupal\Tests\ui_patterns_devel\Kernel\TwigValidatorRule;

use Drupal\Core\Logger\RfcLogLevel;
use Drupal\Tests\ui_patterns_devel\Kernel\TwigValidatorTestBase;

/**
 * @coversDefaultClass \Drupal\ui_patterns_devel\Plugin\TwigValidatorRule\TwigValidatorRuleConstant
 *
 * @group ui_patterns_devel
 * @internal
 *
 * cSpell:disable
 */
final class TwigValidatorRuleConstantTest extends TwigValidatorTestBase {

  /**
   * Provides tests data for testTwigValidatorConstant.
   *
   * @return array
   *   An array of test data each containing of a twig template source
   *   string, array of error messages and levels expected.
   */
  public static function providerTestTwigValidatorConstant() {
    return [
      [
        "{% extends 'links.html.twig' %}",
        [
          [1, 'Use slots instead of hard embedding a component in the template.', RfcLogLevel::ERROR],
        ],
      ],
    ];
  }

  /**
   * @covers ::processNode
   *
   * @dataProvider providerTestTwigValidatorConstant
   */
  public function testTwigValidatorConstant(string $source, array $expected): void {
    $this->runTestSourceTwigValidator($source, $expected);
  }

}
