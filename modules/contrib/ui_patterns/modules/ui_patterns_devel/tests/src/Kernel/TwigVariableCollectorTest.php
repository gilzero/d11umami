<?php

declare(strict_types=1);

namespace Drupal\Tests\ui_patterns_devel\Kernel;

/**
 * @coversDefaultClass \Drupal\ui_patterns_devel\src\TwigValidator\TwigVariableCollectorVisitor
 *
 * @group ui_patterns_devel
 * @internal
 *
 * @phpcs:disable Drupal.Commenting.FunctionComment.Missing
 */
final class TwigVariableCollectorTest extends TwigValidatorTestBase {

  public function testTwigValidatorCollector(): void {
    $component_id = 'ui_patterns_devel_theme_test:collector';
    $component = $this->componentPluginManager->find($component_id);

    $this->twigValidator->validateComponent($component_id, $component);
    $errors = $this->twigValidator->getMessages();

    $this->assertEquals(1, \count($errors), 'Error count do not match');

    $this->assertEquals('Unused variables: zoo_unused, item_unused, my_attributes, macro_unused_1, test_slot_string, test_slot_block, test_prop_bool', (string) $errors[0]->message());
  }

}
