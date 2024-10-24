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
  id: 'node',
  twig_node_type: 'Twig\Node\Node',
  rule_on_name: [],
  label: new TranslatableMarkup('Node rules'),
  description: new TranslatableMarkup('Rules around Twig node.'),
)]
final class TwigValidatorRuleNode extends TwigValidatorRulePluginBase {

  /**
   * {@inheritdoc}
   */
  public function processNode(string $id, Node $node, array $definition, array $variableSet): array {
    $class = \get_class($node);
    switch ($class) {
      case 'Twig\Node\SandboxNode':
        $message = new TranslatableMarkup('Bad architecture for sandbox: Component calling components.');
        return [ValidatorMessage::createForNode($id, $node, $message)];

      case 'Twig\Node\DoNode':
        // Avoid false positive: {{ my_bool ? my_string }}.
        if ($node->hasNode('expr')) {
          $subNode = $node->getNode('expr');
          if ($subNode->hasNode('expr3')) {
            break;
          }
        }
        $message = new TranslatableMarkup('Careful with do usage.');
        return [ValidatorMessage::createForNode($id, $node, $message, RfcLogLevel::WARNING)];

      case 'Twig\Node\FlushNode':
        $message = new TranslatableMarkup('Cache management outside of Drupal.');
        return [ValidatorMessage::createForNode($id, $node, $message)];
    }

    return [];
  }

}
