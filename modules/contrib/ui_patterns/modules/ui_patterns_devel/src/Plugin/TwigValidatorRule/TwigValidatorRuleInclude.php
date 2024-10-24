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
  id: 'include',
  twig_node_type: 'Twig\Node\IncludeNode',
  rule_on_name: [],
  label: new TranslatableMarkup('Include rules'),
  description: new TranslatableMarkup('Rules around Twig include.'),
)]
final class TwigValidatorRuleInclude extends TwigValidatorRulePluginBase {

  /**
   * {@inheritdoc}
   */
  public function processNode(string $id, Node $node, array $definition, array $variableSet): array {
    $message = new TranslatableMarkup('Use slots instead of hard embedding a component in the template with `@name`.', ['@name' => $node->getNodeTag()]);
    $level = RfcLogLevel::ERROR;
    if ('include' === $node->getNodeTag() || 'embed' === $node->getNodeTag()) {
      $level = RfcLogLevel::WARNING;
    }
    return [ValidatorMessage::createForNode($id, $node, $message, $level)];
  }

}
