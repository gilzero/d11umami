<?php

declare(strict_types=1);

namespace Drupal\ui_patterns_devel;

use Drupal\Core\Logger\RfcLogLevel;
use Drupal\Core\Plugin\Component;
use Drupal\Core\Render\Component\Exception\InvalidComponentException;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Theme\Component\ComponentValidator;

/**
 * The Definition Validator service.
 */
final class DefinitionValidator extends ValidatorBase {

  public function __construct(
    private readonly ComponentValidator $componentValidator,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function validateComponent(string $id, Component $component): void {
    $definition = (array) $component->getPluginDefinition();
    $source = \file_get_contents($definition['_discovered_file_path']);
    if (FALSE === $source) {
      $source = '';
    }
    $this->validateDefinition($id, $definition, $source);
    $this->validatePropsFromStories($id, $component, $definition, $source);
  }

  /**
   * Validate definition of a component.
   *
   * @param string $id
   *   The ID of the component.
   * @param array $definition
   *   The component definition.
   * @param string $source
   *   The component definition source (yaml file).
   *
   * @todo validate with ComponentValidator to get hidden messages.
   */
  private function validateDefinition(string $id, array $definition, string $source): void {
    $source = \explode("\n", $source);

    // Rule only one variant, then not needed.
    if (isset($definition['variants']) && 1 === count($definition['variants'])) {
      $this->addValidationMessage($id, 'variants:', $source, new TranslatableMarkup('A single variant do not need to be declared.'));
    }

    $this->validateRequiredFields($id, $definition['slots'] ?? [], $source, new TranslatableMarkup('Required slots are not recommended.'));

    if (isset($definition['props']['properties'])) {
      $this->validateRequiredFields($id, $definition['props']['properties'], $source, new TranslatableMarkup('Required props are not recommended. Use default values instead.'));
      $this->checkEnumDefault($id, $definition['props']['properties'], $source);
      $this->checkEmptyArrayObject($id, $definition['props']['properties'], $source);
    }
  }

  /**
   * Validates required fields in the component definition.
   *
   * @param string $id
   *   The ID of the component.
   * @param array $fields
   *   The fields to validate (e.g., slots or props).
   * @param array $source
   *   The source array of the component definition.
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup $message
   *   The message text to display if a required field is found.
   */
  private function validateRequiredFields(string $id, array $fields, array $source, TranslatableMarkup $message): void {
    foreach ($fields as $name => $field) {
      if (isset($field['required'])) {
        $this->addValidationMessage($id, $name . ':', $source, $message, RfcLogLevel::WARNING);
      }
    }
  }

  /**
   * Validates enum default in the component definition.
   *
   * @param string $id
   *   The ID of the component.
   * @param array $properties
   *   The properties to validate (e.g., ['props']['properties']).
   * @param array $source
   *   The source array of the component definition.
   */
  private function checkEnumDefault(string $id, array $properties, array $source): void {
    foreach ($properties as $name => $prop) {
      if (isset($prop['enum']) && isset($prop['default']) && !\in_array($prop['default'], $prop['enum'])) {
        $this->addValidationMessage($id, $name . ':', $source, new TranslatableMarkup('Default value must be in the enum.'));
      }
    }
  }

  /**
   * Validate props of a template based on stories.
   *
   * @param string $id
   *   The ID of the component.
   * @param \Drupal\Core\Plugin\Component $component
   *   The component to validate.
   * @param array $definition
   *   The component definition.
   * @param string $source
   *   The component definition source (yaml file).
   */
  private function validatePropsFromStories(string $id, Component $component, array $definition, string $source): void {
    $preview = $definition['stories'] ?? NULL;

    if (!$preview) {
      return;
    }

    $preview = \array_shift($preview);
    unset($preview['title'], $preview['description']);
    $context = \array_merge($preview['slots'] ?? [], $preview['props'] ?? []);

    try {
      $this->componentValidator->validateProps($context, $component);
    }
    catch (InvalidComponentException $error) {
      $this->addValidationMessage($id, 'stories:', \explode("\n", $source), new TranslatableMarkup('@message', ['@message' => $error->getMessage()]));
    }
  }

  /**
   * Check for empty array, object or array of object in properties.
   *
   * @param string $id
   *   The ID of the component.
   * @param array $properties
   *   The properties to validate (e.g., ['props']['properties']).
   * @param array $source
   *   The source array of the component definition.
   *
   * @SuppressWarnings(PHPMD.CyclomaticComplexity)
   * @SuppressWarnings(PHPMD.NPathComplexity)
   */
  private function checkEmptyArrayObject(string $id, array $properties, array $source): void {
    foreach ($properties as $name => $prop) {
      if (!isset($prop['type']) && !isset($prop['$ref']) && !isset($prop['patternProperties'])) {
        $this->addValidationMessage($id, $name . ':', $source, new TranslatableMarkup('Missing type for this property.'));
        continue;
      }

      if (!isset($prop['type'])) {
        continue;
      }

      if ('object' === $prop['type'] && !isset($prop['properties']) && !isset($prop['patternProperties'])) {
        $this->addValidationMessage($id, $name . ':', $source, new TranslatableMarkup('Empty object.'));
        continue;
      }

      if ('array' !== $prop['type']) {
        continue;
      }

      if (!isset($prop['items'])) {
        $this->addValidationMessage($id, $name . ':', $source, new TranslatableMarkup('Empty array.'));
        continue;
      }

      if (!isset($prop['items']['type']) && !isset($prop['items']['enum'])) {
        $this->addValidationMessage($id, $name . ':', $source, new TranslatableMarkup('Missing type for this property.'));
        continue;
      }

      if (!isset($prop['items']['type'])) {
        continue;
      }

      if ('object' === $prop['items']['type'] && !isset($prop['items']['properties']) && !isset($prop['items']['patternProperties'])) {
        $this->addValidationMessage($id, $name . ':', $source, new TranslatableMarkup('Array of empty object.'));
      }
    }
  }

  /**
   * Adds a validation message.
   *
   * @param string $id
   *   The ID of the component.
   * @param string $searchTerm
   *   The term to search for in the source array.
   * @param array $source
   *   The source array of the component definition.
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup $message
   *   The message text to display.
   * @param int $logLevel
   *   The log level of the message, default to error.
   */
  private function addValidationMessage(string $id, string $searchTerm, array $source, TranslatableMarkup $message, int $logLevel = RfcLogLevel::ERROR): void {
    $line = $this->findLineNumber($source, $searchTerm);
    $this->addMessage(ValidatorMessage::createForString($id, $message, $logLevel, $line + 1, 1, \implode("\n", $source)));
  }

  /**
   * Finds the line number of a search term in the source array.
   *
   * @param array $source
   *   The source array of the component definition.
   * @param string $searchTerm
   *   The term to search for.
   *
   * @return int
   *   The line number of the search term.
   */
  private function findLineNumber(array $source, string $searchTerm): int {
    foreach ($source as $lineNumber => $value) {
      if ($searchTerm === \trim($value)) {
        return $lineNumber;
      }
    }
    return 0;
  }

}
