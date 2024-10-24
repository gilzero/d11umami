<?php

declare(strict_types=1);

namespace Drupal\ui_patterns_devel\Plugin\TwigValidatorRule;

use Drupal\Core\Logger\RfcLogLevel;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ui_patterns_devel\Attribute\TwigValidatorRule;
use Drupal\ui_patterns_devel\TwigValidatorRulePluginBase;
use Drupal\ui_patterns_devel\ValidatorMessage;
use Twig\Node\Node;

/**
 * Plugin implementation of the twig_validator_rule.
 */
#[TwigValidatorRule(
  id: 'conditional',
  twig_node_type: 'Twig\Node\Expression\ConditionalExpression',
  rule_on_name: [],
  label: new TranslatableMarkup('Conditional rules'),
  description: new TranslatableMarkup('Rules around Twig Conditional.'),
)]
final class TwigValidatorRuleConditional extends TwigValidatorRulePluginBase {

  /**
   * {@inheritdoc}
   */
  public function processNode(string $id, Node $node, array $definition, array $variableSet): array {
    if (!$node->hasNode('expr3')) {
      return [];
    }

    $errors = [];

    $this->checkChainedTernary($id, $node, $errors);
    $this->checkShorthandTernary($id, $node, $errors);
    $this->checkBooleanResultTernary($id, $node, $errors);

    return $errors;
  }

  /**
   * Checks for chained ternary expressions.
   *
   * @param string $id
   *   The ID of the node.
   * @param \Twig\Node\Node $node
   *   The Twig node being processed.
   * @param array $errors
   *   The array to store errors.
   */
  private function checkChainedTernary(string $id, Node $node, array &$errors): void {
    $expr3 = $node->getNode('expr3');
    if (\is_a($expr3, 'Twig\Node\Expression\ConditionalExpression')) {
      $message = new TranslatableMarkup('No chained ternary');
      $errors[] = ValidatorMessage::createForNode($id, $node, $message);
    }
  }

  /**
   * Checks for shorthand ternary expressions.
   *
   * @param string $id
   *   The ID of the node.
   * @param \Twig\Node\Node $node
   *   The Twig node being processed.
   * @param array $errors
   *   The array to store errors.
   */
  private function checkShorthandTernary(string $id, Node $node, array &$errors): void {
    $expr1 = $node->getNode('expr1');
    $expr2 = $node->getNode('expr2');
    if ($expr1->hasAttribute('name') && $expr2->hasAttribute('name')) {
      if ($expr1->getAttribute('name') === $expr2->getAttribute('name')) {
        $message = new TranslatableMarkup('Use `|default(foo)` filter instead of shorthand ternary `?:`');
        $errors[] = ValidatorMessage::createForNode($id, $node, $message, RfcLogLevel::WARNING);
      }
    }
  }

  /**
   * Checks for ternary expressions with boolean results.
   *
   * @param string $id
   *   The ID of the node.
   * @param \Twig\Node\Node $node
   *   The Twig node being processed.
   * @param array $errors
   *   The array to store errors.
   */
  private function checkBooleanResultTernary(string $id, Node $node, array &$errors): void {
    $expr2 = $node->getNode('expr2');
    $expr3 = $node->getNode('expr3');
    if (\is_a($expr2, 'Twig\Node\Expression\ConstantExpression') && \is_a($expr3, 'Twig\Node\Expression\ConstantExpression')) {
      if ($expr2->hasAttribute('value') && $expr3->hasAttribute('value')) {
        if ($expr2->getAttribute('value') === TRUE && $expr3->getAttribute('value') === FALSE) {
          $message = new TranslatableMarkup('Ternary test with boolean result');
          $errors[] = ValidatorMessage::createForNode($id, $node, $message, RfcLogLevel::NOTICE);
        }
      }
    }
  }

}
