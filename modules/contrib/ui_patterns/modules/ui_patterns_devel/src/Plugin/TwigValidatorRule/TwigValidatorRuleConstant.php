<?php

declare(strict_types=1);

namespace Drupal\ui_patterns_devel\Plugin\TwigValidatorRule;

use Drupal\Core\Logger\RfcLogLevel;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ui_patterns_devel\Attribute\TwigValidatorRule;
use Drupal\ui_patterns_devel\TwigValidator\NodeAttribute;
use Drupal\ui_patterns_devel\TwigValidatorRulePluginBase;
use Drupal\ui_patterns_devel\ValidatorMessage;
use Twig\Node\Node;

/**
 * Plugin implementation of the twig_validator_rule.
 */
#[TwigValidatorRule(
  id: 'constant',
  twig_node_type: 'Twig\Node\Expression\ConstantExpression',
  rule_on_name: [],
  label: new TranslatableMarkup('Constant rules'),
  description: new TranslatableMarkup('Rules around Twig constant.'),
)]
final class TwigValidatorRuleConstant extends TwigValidatorRulePluginBase {

  /**
   * {@inheritdoc}
   */
  public function processNode(string $id, Node $node, array $definition, array $variableSet): array {

    if (!$node->hasAttribute('value')) {
      return [];
    }

    $value = $node->getAttribute('value');

    if (!\is_string($value)) {
      return [];
    }

    if ($node->hasAttribute(NodeAttribute::PARENT)) {

      $parent = $node->getAttribute(NodeAttribute::PARENT);

      if (\is_a($parent, 'Twig\Node\IncludeNode')) {
        // Covered by Function rule, just avoid repetition.
        return [];
      }

      if (\is_a($parent, 'Twig\Node\Expression\BlockReferenceExpression')) {
        $message = new TranslatableMarkup('Forbidden Twig function: `block`. Use slots instead of hard embedding a component in the template.');
        return [ValidatorMessage::createForNode($id, $node, $message, RfcLogLevel::WARNING)];
      }
    }

    if (\str_ends_with($value, '.twig')) {
      $message = new TranslatableMarkup('Use slots instead of hard embedding a component in the template.');
      return [ValidatorMessage::createForNode($id, $node, $message)];
    }

    return [];
  }

}
