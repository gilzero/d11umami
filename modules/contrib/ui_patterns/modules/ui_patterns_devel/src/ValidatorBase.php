<?php

declare(strict_types=1);

namespace Drupal\ui_patterns_devel;

use Drupal\Core\Plugin\Component;

/**
 * The Validator base class.
 */
abstract class ValidatorBase {

  /**
   * List of messages.
   *
   * @var \Drupal\ui_patterns_devel\ValidatorMessage[]
   */
  private array $messages = [];

  /**
   * Validates a component.
   *
   * @param string $id
   *   The ID of the component.
   * @param \Drupal\Core\Plugin\Component $component
   *   The component to validate.
   */
  abstract public function validateComponent(string $id, Component $component): void;

  /**
   * Adds a ValidatorMessage to the list of messages.
   *
   * @param \Drupal\ui_patterns_devel\ValidatorMessage $message
   *   The ValidatorMessage to add to the list.
   */
  public function addMessage(ValidatorMessage $message): void {
    $this->messages[] = $message;
  }

  /**
   * Adds additional messages to the list.
   *
   * @param \Drupal\ui_patterns_devel\ValidatorMessage[] $messages
   *   The array of additional messages to add to the list.
   */
  public function addMessages(array $messages): void {
    $this->messages = \array_merge($this->messages, $messages);
  }

  /**
   * Retrieves the list of messages stored in the Validator object.
   *
   * @return \Drupal\ui_patterns_devel\ValidatorMessage[]
   *   An array containing the messages stored in the Validator object.
   */
  public function getMessages(): array {
    return $this->messages;
  }

  /**
   * Retrieves the list of messages sorted by type and line.
   *
   * @return \Drupal\ui_patterns_devel\ValidatorMessage[]
   *   An array containing the messages sorted.
   */
  public function getMessagesSortedByGroupAndLine(): array {
    \usort($this->messages, static function ($a, $b) {
      if ($a->type() === $b->type()) {
        return $a->line() <=> $b->line();
      }
      return $a->type() <=> $b->type();
    });
    return $this->messages;
  }

  /**
   * Reset the list of messages stored in the Validator object.
   */
  public function resetMessages(): void {
    $this->messages = [];
  }

}
