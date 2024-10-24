<?php

declare(strict_types=1);

namespace Drupal\ui_patterns_devel\Component;

use Drupal\Core\Plugin\Component;
use Drupal\Core\Render\Component\Exception\InvalidComponentException;
use Drupal\Core\Theme\Component\ComponentValidator;

/**
 * Override validation of a component.
 */
final class ComponentValidatorSilencer extends ComponentValidator {

  /**
   * Override the component metadata validation to always pass.
   *
   * @param array $definition
   *   The definition to validate.
   * @param bool $enforce_schemas
   *   TRUE if schema definitions are mandatory.
   *
   * @return bool
   *   Always TRUE to bypass the Exception.
   */
  public function validateDefinition(array $definition, bool $enforce_schemas): bool {
    try {
      parent::validateDefinition($definition, $enforce_schemas);
    }
    catch (InvalidComponentException $error) {
      return TRUE;
    }

    return TRUE;
  }

  /**
   * Override the props provided to the component to always pass.
   *
   * @param array $context
   *   The Twig context that contains the prop data.
   * @param \Drupal\Core\Plugin\Component $component
   *   The component to validate the props against.
   *
   * @return bool
   *   Always TRUE to bypass the Exception.
   */
  public function validateProps(array $context, Component $component): bool {
    try {
      parent::validateProps($context, $component);
    }
    catch (InvalidComponentException $error) {
      return TRUE;
    }

    return TRUE;
  }

}
