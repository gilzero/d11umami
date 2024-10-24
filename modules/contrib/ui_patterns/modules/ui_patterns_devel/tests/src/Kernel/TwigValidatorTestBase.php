<?php

declare(strict_types=1);

namespace Drupal\Tests\ui_patterns_devel\Kernel;

use Drupal\Tests\Core\Theme\Component\ComponentKernelTestBase;
use Drupal\ui_patterns\ComponentPluginManager;
use Drupal\ui_patterns_devel\TwigValidator\TwigValidator;

/**
 * Base class to ease testing of Twig validator rules.
 *
 * @internal
 *
 * phpcs:disable Drupal.Commenting.VariableComment.Missing,
 */
abstract class TwigValidatorTestBase extends ComponentKernelTestBase {

  protected static $modules = [
    'system',
    'ui_patterns',
    'ui_patterns_devel',
  ];

  protected static $themes = ['ui_patterns_devel_theme_test'];

  protected TwigValidator $twigValidator;

  protected ComponentPluginManager $componentPluginManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installConfig('system');
    $this->twigValidator = \Drupal::service('ui_patterns_devel.twig_validator');
    $this->componentPluginManager = \Drupal::service('plugin.manager.sdc');
  }

  /**
   * Helper to run the twig validator rules tests.
   *
   * @param string $source
   *   The component source.
   * @param array $expected
   *   The errors expected.
   * @param bool $debug
   *   Debug the test itself, print the generated errors.
   */
  public function runTestSourceTwigValidator(string $source, array $expected, bool $debug = FALSE): void {

    $this->twigValidator->validateSource($source);
    $errors = $this->twigValidator->getMessagesSortedByGroupAndLine();

    foreach ($errors as $key => $error) {
      if ($debug) {
        $tmp_error = ['EMERGENCY', 'ALERT', 'CRITICAL', 'ERROR', 'WARNING', 'NOTICE', 'INFO', 'DEBUG'];
        print("\n[" . $error->line() . ', \'' . $error->message() . '\', RfcLogLevel::' . $tmp_error[$error->level()] . "],");
      }

      if ($debug) {
        continue;
      }

      self::assertEquals($expected[$key][0] ?? 0, $error->line(), \sprintf('Error line do not match for case: %s', $key));
      self::assertStringContainsString($expected[$key][1] ?? '', (string) $error->message(), \sprintf('Error message do not match for case: %s', $key));
      self::assertEquals($expected[$key][2] ?? 0, $error->level(), \sprintf('Error level do not match for case: %s', $key));
    }

    if (!$debug) {
      self::assertEquals(\count($expected), \count($errors), 'Error count do not match');
      return;
    }

    print("\n");
  }

}
