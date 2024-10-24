<?php

declare(strict_types=1);

namespace Drupal\ui_patterns_devel\Plugin\TwigValidatorRule;

use Drupal\Core\Logger\RfcLogLevel;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ui_patterns_devel\Attribute\TwigValidatorRule;
use Drupal\ui_patterns_devel\TwigValidator\NodeAttribute;
use Drupal\ui_patterns_devel\TwigValidator\TwigVariableCollectorVisitor;
use Drupal\ui_patterns_devel\TwigValidatorRulePluginBase;
use Drupal\ui_patterns_devel\ValidatorMessage;
use Twig\Node\Node;

/**
 * Plugin implementation of the twig_validator_rule.
 */
#[TwigValidatorRule(
  id: 'name',
  twig_node_type: 'Twig\Node\Expression\NameExpression',
  rule_on_name: [
    self::RULE_NAME_FORBID => [
      'componentMetadata' => 'This is an internal SDC variable specific to Drupal.',
    ],
    // Attributes is always injected.
    self::RULE_NAME_IGNORE => [
      'attributes',
    ],
  ],
  label: new TranslatableMarkup('Name rules'),
  description: new TranslatableMarkup('Rules around Twig name.'),
)]
final class TwigValidatorRuleName extends TwigValidatorRulePluginBase {

  /**
   * {@inheritdoc}
   */
  public function processNode(string $id, Node $node, array $definition, array $variableSet): array {
    if ($this->isAssignNameExpression($node)) {
      return [];
    }

    $errors = $this->checkLoopVariable($id, $node);
    $errors = array_merge($errors, $this->checkForbiddenNames($id, $node));

    if (!empty($errors)) {
      return $errors;
    }

    if ($this->isKnownVariable($node, $definition, $variableSet)) {
      return [];
    }

    return $this->createUnknownVariableError($id, $node);
  }

  /**
   * Checks if the node is AssignNameExpression.
   *
   * @param \Twig\Node\Node $node
   *   The node to be checked.
   *
   * @return bool
   *   Is or is not.
   */
  private function isAssignNameExpression(Node $node): bool {
    return \is_a($node, 'Twig\Node\Expression\AssignNameExpression');
  }

  /**
   * Checks if the variable is known.
   *
   * @param \Twig\Node\Node $node
   *   The node to be checked.
   * @param array $definition
   *   The component definition.
   * @param array $variableSet
   *   The collected variables in the template.
   *
   * @return bool
   *   Is or is not.
   */
  private function isKnownVariable(Node $node, array $definition, array $variableSet): bool {
    if (!$node->hasAttribute('name')) {
      return TRUE;
    }

    $name = $node->getAttribute('name');

    if (isset($definition[$name])) {
      return TRUE;
    }

    if (isset($variableSet[$name])) {
      return TRUE;
    }

    if (isset(TwigVariableCollectorVisitor::ALLOW_NOT_SET_VARIABLE[$name])) {
      return TRUE;
    }

    if (in_array($name, $this->getNameIgnore(), TRUE)) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Checks if the node is a loop.parent call.
   *
   * @param string $id
   *   The identifier of the node.
   * @param \Twig\Node\Node $node
   *   The node to be checked.
   *
   * @return array
   *   An array of ValidatorMessage objects if any errors are found.
   */
  private function checkLoopVariable(string $id, Node $node): array {
    $name = $node->getAttribute('name');

    if ('loop' !== $name) {
      return [];
    }

    $parent = $node->getAttribute(NodeAttribute::PARENT);
    if (!\is_a($parent, 'Twig\Node\Expression\GetAttrExpression') || !$parent->hasNode('attribute')) {
      return [];
    }

    $value = $parent->getNode('attribute')->getAttribute('value');
    if ('parent' === $value) {
      return [ValidatorMessage::createForNode($id, $node, new TranslatableMarkup('Breaking the flow. Bad performance.'))];
    }

    return [];
  }

  /**
   * Checks if the node's name is in the list of forbidden names.
   *
   * @param string $id
   *   The identifier of the node.
   * @param \Twig\Node\Node $node
   *   The node to be checked.
   *
   * @return array
   *   An array of ValidatorMessage objects if any forbidden names are found.
   */
  private function checkForbiddenNames(string $id, Node $node): array {
    $errors = [];
    $name = $node->getAttribute('name');

    if (\in_array($name, \array_keys($this->getNameForbid()), TRUE)) {
      $errors[] = $this->handleNameCase($id, $node, $name, 'variable', $this->getNameForbid(), new TranslatableMarkup('Forbidden'));
    }

    return $errors;
  }

  /**
   * Creates an error for an unknown variable in a Twig template.
   *
   * @param string $id
   *   The identifier of the node.
   * @param \Twig\Node\Node $node
   *   The node where the unknown variable was found.
   *
   * @return array
   *   An array containing the error.
   */
  private function createUnknownVariableError(string $id, Node $node): array {
    $name = $node->getAttribute('name');
    $message = new TranslatableMarkup('Unknown variable: `@name`.', ['@name' => $name]);
    $tip = new TranslatableMarkup('The variable is not declared in the component definition.');
    return [ValidatorMessage::createForNode($id, $node, $message, RfcLogLevel::ERROR, $tip)];
  }

}
