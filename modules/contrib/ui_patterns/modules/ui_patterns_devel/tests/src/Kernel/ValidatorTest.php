<?php

declare(strict_types=1);

namespace Drupal\Tests\ui_patterns_devel\Kernel;

use Drupal\Tests\Core\Theme\Component\ComponentKernelTestBase;
use Drupal\ui_patterns\ComponentPluginManager;
use Drupal\ui_patterns_devel\DefinitionValidator;
use Drupal\ui_patterns_devel\TwigValidator\TwigValidator;
use Drupal\ui_patterns_devel\Validator;

/**
 * @coversDefaultClass \Drupal\ui_patterns_devel\Validator
 *
 * @group ui_patterns_devel
 * @internal
 *
 * phpcs:disable Drupal.Commenting.VariableComment.Missing
 */
class ValidatorTest extends ComponentKernelTestBase {

  protected static $modules = [
    'system',
    'ui_patterns',
    'ui_patterns_devel',
  ];

  protected static $themes = ['ui_patterns_devel_theme_test'];

  protected TwigValidator $twigValidator;

  protected DefinitionValidator $definitionValidator;

  protected Validator $validator;

  protected ComponentPluginManager $componentPluginManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installConfig('system');

    $this->componentPluginManager = $this->container->get('plugin.manager.sdc');

    $this->twigValidator = $this->container->get('ui_patterns_devel.twig_validator');
    $this->definitionValidator = $this->container->get('ui_patterns_devel.definition_validator');

    $this->validator = new Validator(
      $this->twigValidator,
      $this->definitionValidator
    );
  }

  /**
   * @covers ::validate
   * @covers ::validateComponent
   * @covers ::checkEnumDefault
   * @covers ::validatePropsFromStories
   * @covers ::checkEmptyArrayObject
   * @covers \Drupal\ui_patterns_devel\DefinitionValidator::validateComponent
   * @covers \Drupal\ui_patterns_devel\TwigValidator\TwigValidator::validateComponent
   */
  public function testValidate(): void {
    $component_id = 'ui_patterns_devel_theme_test:foo';
    $component = $this->componentPluginManager->find($component_id);

    $this->validator->validate($component_id, $component);
    $errors = $this->validator->getMessages();

    $this->assertEmpty($errors, \sprintf('Found errors in the component: %s', $component_id));
  }

  /**
   * @covers ::validate
   * @covers ::validateComponent
   * @covers ::checkEnumDefault
   * @covers ::validatePropsFromStories
   * @covers ::checkEmptyArrayObject
   * @covers \Drupal\ui_patterns_devel\DefinitionValidator::validateComponent
   * @covers \Drupal\ui_patterns_devel\TwigValidator\TwigValidator::validateComponent
   */
  public function testValidateError(): void {
    $component_id = 'ui_patterns_devel_theme_test:error';
    $component = $this->componentPluginManager->find($component_id);

    $this->validator->validate($component_id, $component);
    $errors = $this->validator->getMessages();

    $expected = [
      'Unused variables: attributes, test_slot_string, nothing, object, array, array_object, test_prop_string_error, test_enum_string, test_prop_links, test_enum_items',
      'Filter `trans` or `t` unsafe translation, do not translate variables!',
      "Don't use `default` filter on boolean.",
      'A single variant do not need to be declared.',
      'Required slots are not recommended.',
      'Required props are not recommended. Use default values instead.',
      'Default value must be in the enum.',
      'Missing type for this property.',
      'Empty object.',
      'Empty array.',
      'Array of empty object.',
      '[variant] Does not have a value in the enumeration [&quot;default&quot;]',
    ];
    $this->assertCount(\count($expected), $errors);

    foreach ($expected as $key => $message) {
      $this->assertSame($message, (string) $errors[$key]->message(), 'Message do not match.');
    }
  }

  /**
   * @covers ::validate
   * @covers ::validateComponent
   * @covers ::checkEnumDefault
   * @covers ::validatePropsFromStories
   * @covers ::checkEmptyArrayObject
   * @covers \Drupal\ui_patterns_devel\DefinitionValidator::validateComponent
   * @covers \Drupal\ui_patterns_devel\TwigValidator\TwigValidator::validateComponent
   */
  public function testValidateFail(): void {
    $component_id = 'ui_patterns_devel_theme_test:fail';
    $component = $this->componentPluginManager->find($component_id);

    $this->validator->validate($component_id, $component);
    $errors = $this->validator->getMessages();

    $expected = [
      'An exception has been thrown during the rendering of a template',
      'Unexpected "}".',
    ];

    $this->assertCount(2, $errors);
    $this->assertStringContainsString($expected[0], (string) $errors[0]->message());
    $this->assertSame($expected[1], (string) $errors[1]->message());
  }

}
