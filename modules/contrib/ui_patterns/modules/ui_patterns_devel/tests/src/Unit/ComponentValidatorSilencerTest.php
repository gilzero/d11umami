<?php

declare(strict_types=1);

namespace Drupal\Tests\ui_patterns_devel\Unit;

use Drupal\Core\Plugin\Component;
use Drupal\Tests\Core\Theme\Component\ComponentValidatorTest;
use Drupal\ui_patterns_devel\Component\ComponentValidatorSilencer;

/**
 * Simple test for ComponentValidator throw interception.
 *
 * This is a copy of ComponentValidatorTest.php without throw.
 *
 * @coversDefaultClass \Drupal\ui_patterns_devel\Component\ComponentValidatorSilencer
 *
 * @group ui_patterns_devel
 * @internal
 */
class ComponentValidatorSilencerTest extends ComponentValidatorTest {

  /**
   * Tests invalid component definitions intercept.
   *
   * @dataProvider dataProviderValidateDefinitionInvalid
   */
  public function testValidateDefinitionInvalid(array $definition): void {

    $component_validator = new ComponentValidatorSilencer();
    $component_validator->setValidator();
    $result = $component_validator->validateDefinition($definition, TRUE);
    $this->assertTrue($result);
  }

  /**
   * Tests that invalid props are intercepted.
   *
   * @dataProvider dataProviderValidatePropsInvalid
   */
  public function testValidatePropsInvalid(array $context, string $component_id, array $definition): void {
    $component = new Component(
      ['app_root' => '/fake/path/root'],
      'sdc_test:' . $component_id,
      $definition
    );
    $component_validator = new ComponentValidatorSilencer();
    $component_validator->setValidator();
    $result = $component_validator->validateProps($context, $component);
    $this->assertTrue($result);
  }

}
