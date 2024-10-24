<?php

declare(strict_types=1);

namespace Drupal\ui_patterns_devel;

use Drupal\Core\Plugin\Component;
use Drupal\ui_patterns_devel\TwigValidator\TwigValidator;

/**
 * The Validator service.
 */
final class Validator extends ValidatorBase {

  /**
   * Constructs a new Validator object.
   *
   * @param \Drupal\ui_patterns_devel\TwigValidator\TwigValidator $twigValidator
   *   The Twig validator service.
   * @param \Drupal\ui_patterns_devel\DefinitionValidator $definitionValidator
   *   The component definition validator service.
   */
  public function __construct(
    private readonly TwigValidator $twigValidator,
    private readonly DefinitionValidator $definitionValidator,
  ) {}

  /**
   * Validate a component.
   *
   * @param string $id
   *   The ID of the component.
   * @param \Drupal\Core\Plugin\Component $component
   *   The component to validate.
   */
  public function validate(string $id, Component $component): void {
    $this->validateComponent($id, $component);
  }

  /**
   * {@inheritdoc}
   */
  public function validateComponent(string $id, Component $component): void {
    $this->twigValidator->resetMessages();
    $this->twigValidator->validateComponent($id, $component);
    $this->addMessages($this->twigValidator->getMessages());
    $this->twigValidator->resetMessages();

    $this->definitionValidator->resetMessages();
    $this->definitionValidator->validateComponent($id, $component);
    $this->addMessages($this->definitionValidator->getMessages());
    $this->definitionValidator->resetMessages();
  }

}
