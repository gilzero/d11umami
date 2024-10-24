<?php

declare(strict_types=1);

namespace Drupal\ui_patterns_devel\Plugin\TwigValidatorRule;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ui_patterns_devel\Attribute\TwigValidatorRule;
use Drupal\ui_patterns_devel\TwigValidatorRulePluginBase;
use Drupal\ui_patterns_devel\ValidatorMessage;
use Twig\Node\Node;

/**
 * Plugin implementation of the twig_validator_rule.
 */
#[TwigValidatorRule(
  id: 'parent',
  twig_node_type: 'Twig\Node\Expression\ParentExpression',
  rule_on_name: [],
  label: new TranslatableMarkup('Parent rule'),
  description: new TranslatableMarkup('Rule around Twig parent.'),
)]
final class TwigValidatorRuleParent extends TwigValidatorRulePluginBase {

  /**
   * {@inheritdoc}
   */
  public function processNode(string $id, Node $node, array $definition, array $variableSet): array {
    $message = new TranslatableMarkup('Bad architecture for parent: Component calling components with `parent`.');
    return [ValidatorMessage::createForNode($id, $node, $message)];
  }

}
