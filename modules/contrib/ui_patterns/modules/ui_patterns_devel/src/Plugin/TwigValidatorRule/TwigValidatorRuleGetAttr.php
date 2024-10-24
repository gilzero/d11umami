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
  id: 'get_attribute',
  twig_node_type: 'Twig\Node\Expression\GetAttrExpression',
  rule_on_name: [],
  label: new TranslatableMarkup('Get Attribute rules'),
  description: new TranslatableMarkup('Rules around Twig get attributes.'),
)]
final class TwigValidatorRuleGetAttr extends TwigValidatorRulePluginBase {

  /**
   * Allow methods from Attribute.php.
   */
  private const ALLOWED_METHOD = [
    'offsetGet',
    'offsetSet',
    'offsetUnset',
    'offsetExists',
    'addClass',
    'setAttribute',
    'hasAttribute',
    'removeAttribute',
    'removeClass',
    'getClass',
    'hasClass',
    'toArray',
    'getIterator',
    'storage',
    'storage',
    'jsonSerialize',
    'merge',
  ];

  /**
   * {@inheritdoc}
   */
  public function processNode(string $id, Node $node, array $definition, array $variableSet): array {
    if (!$node->hasNode('attribute')) {
      return [];
    }

    $errors = [];
    $attribute = $node->getNode('attribute');
    $parent = $attribute->getAttribute(NodeAttribute::PARENT);

    if ($parent->hasAttribute('type')) {
      if ('method' === $parent->getAttribute('type')) {
        $methodName = $attribute->getAttribute('value');
        if (!\in_array($methodName, self::ALLOWED_METHOD)) {
          $errors[] = ValidatorMessage::createForNode($id, $node, new TranslatableMarkup('Direct method call are forbidden.'));
        }
      }
    }

    if (!$attribute->hasAttribute('value')) {
      return $errors;
    }

    $name = $attribute->getAttribute('value');
    if (TRUE === \str_starts_with((string) $name, '#')) {
      $errors[] = ValidatorMessage::createForNode($id, $node, new TranslatableMarkup('Keep slots opaque by not manipulating renderables in the template.'), RfcLogLevel::WARNING);
    }

    return $errors;
  }

}
