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
 *
 * @see https://www.drupal.org/docs/develop/theming-drupal/twig-in-drupal/filters-modifying-variables-in-twig-templates
 * @see web/core/lib/Drupal/Core/Template/TwigExtension.php
 *
 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
#[TwigValidatorRule(
  id: 'filter',
  twig_node_type: 'Twig\Node\Expression\FilterExpression',
  rule_on_name: [
    self::RULE_NAME_IGNORE => [
      // Internal drupal escape filter that run on all variables.
      'drupal_escape',
    ],
    self::RULE_NAME_ALLOW => [
      'abs',
      'add_class',
      'append',
      'batch',
      'capitalize',
      'clean_id',
      'default',
      'escape',
      'first',
      'has_attribute',
      'has_class',
      'items',
      'join',
      'keys',
      'last',
      'length',
      'lower',
      'map',
      'merge',
      'prepend',
      'remove_attribute',
      'remove_class',
      'replace',
      'reverse',
      'round',
      'safe',
      'set_attribute',
      'slice',
      'split',
      'striptags',
      't',
      'trans',
      'title',
      'trim',
      'upper',
    ],
    self::RULE_NAME_WARN => [
      'clean_unique_id' => '',
      'convert_encoding' => 'Needs specific PHP extension.',
      'e' => 'Useless, Drupal already escape all variables.',
      'escape' => 'Useless, Drupal already escape all variables.',
      'filter' => 'Functional programming may be overkill.',
      'map' => 'Functional programming may be overkill.',
      // Can not work because replaced by render_var.
      // 'raw' => 'May be dangerous, data must be escaped.',.
      'reduce' => 'Functional programming may be overkill.',
    ],
    self::RULE_NAME_FORBID => [
      'placeholder' => 'Forbidden Twig filter: `placeholder`. Keep components sandboxed by avoiding functions calling Drupal application.',
      'render' => 'Please ensure you are not rendering content too early.',
      'without' => 'Avoid `without` filter on slots, which must stay opaque. Allowed with attributes objects until #3296456 is fixed.',
      'add_suggestion' => 'Forbidden Twig filter: `add_suggestion`. Keep components sandboxed by avoiding functions calling Drupal application.',
      'date' => 'PHP object manipulation must be avoided.',
      'date_modify' => 'PHP object manipulation must be avoided.',
      'format_date' => 'Business related. Load config entities.',
      // Contrib modules filters.
      'country_name' => 'Needs specific Twig extension. Business usage, not compatible with Design System principles.',
      'currency_name' => 'Needs specific Twig extension. Business usage, not compatible with Design System principles.',
      'currency_symbol' => 'Needs specific Twig extension. Business usage, not compatible with Design System principles.',
      'data_uri' => 'Needs specific Twig extension. Business usage, not compatible with Design System principles.',
      'format_currency' => 'Needs specific Twig extension. Business usage, not compatible with Design System principles.',
      'format_datetime' => 'Needs specific Twig extension. Business usage, not compatible with Design System principles.',
      'format_number' => 'Needs specific Twig extension. Business usage, not compatible with Design System principles.',
      'format_time' => 'Needs specific Twig extension. Business usage, not compatible with Design System principles.',
      'html_to_markdown' => 'Needs specific Twig extension. Business usage, not compatible with Design System principles.',
      'inky_to_html' => 'Needs specific Twig extension. Business usage, not compatible with Design System principles.',
      'inline_css' => 'Needs specific Twig extension. Business usage, not compatible with Design System principles.',
      'language_name' => 'Needs specific Twig extension. Business usage, not compatible with Design System principles.',
      'locale_name' => 'Needs specific Twig extension. Business usage, not compatible with Design System principles.',
      'markdown_to_html' => 'Needs specific Twig extension. Business usage, not compatible with Design System principles.',
      'slug' => 'Needs specific Twig extension. Business usage, not compatible with Design System principles.',
      'timezone_name' => 'Needs specific Twig extension. Business usage, not compatible with Design System principles.',
      'u' => 'Needs specific Twig extension. Business usage, not compatible with Design System principles.',
      // Debug.
      'test_filter' => 'Development only.',
    ],
  ],
  label: new TranslatableMarkup('Filter rules'),
  description: new TranslatableMarkup('Rules around Twig filters.'),
)]
final class TwigValidatorRuleFilter extends TwigValidatorRulePluginBase {

  /**
   * {@inheritdoc}
   */
  public function processNode(string $id, Node $node, array $definition, array $variableSet): array {
    if (!$node->hasNode('node')) {
      return [];
    }

    $name = $this->getValue($node, 'filter', 'value');

    if (!\is_string($name)) {
      return [];
    }

    $errors = $this->ruleAllowedForbiddenDeprecated($id, $node, $name, 'filter');

    if ($func = $this->getRuleMethodToCall($name)) {
      $parent = $node->getNode('node');
      $errors = \array_merge($errors, $this::$func($id, $node, $parent, $definition));
    }

    return $errors;
  }

  /**
   * Check filter abs.
   *
   * @param string $id
   *   The ID of the node.
   * @param \Twig\Node\Node $node
   *   The Twig node being processed.
   * @param \Twig\Node\Node $parent
   *   The parent node of the Twig node.
   *
   * @return \Drupal\ui_patterns_devel\ValidatorMessage[]
   *   An array of errors encountered during the validation process.
   */
  private static function abs(string $id, Node $node, Node $parent): array {
    $errors = [];

    if (\is_a($parent, 'Twig\Node\Expression\ConstantExpression')) {
      if ($parent->hasAttribute('value')) {
        $value = $parent->getAttribute('value');

        if (\is_string($value)) {
          $errors[] = ValidatorMessage::createForNode($id, $node, new TranslatableMarkup('Filter `abs` can only be applied on number, @type found!', ['@type' => 'string']));
        }
        elseif (\is_bool($value)) {
          $errors[] = ValidatorMessage::createForNode($id, $node, new TranslatableMarkup('Filter `abs` can only be applied on number, @type found!', ['@type' => 'boolean']));
        }
        elseif (NULL === $value) {
          $errors[] = ValidatorMessage::createForNode($id, $node, new TranslatableMarkup('Filter `abs` can only be applied on number, @type found!', ['@type' => 'null']));
        }
      }
    }

    return $errors;
  }

  /**
   * Check filter add_class.
   *
   * @param string $id
   *   The ID of the node.
   * @param \Twig\Node\Node $node
   *   The Twig node being processed.
   * @param \Twig\Node\Node $parent
   *   The parent node of the Twig node.
   *
   * @return \Drupal\ui_patterns_devel\ValidatorMessage[]
   *   An array of errors encountered during the validation process.
   */
  private static function addClass(string $id, Node $node, Node $parent): array {
    $errors = [];

    if (\is_a($parent, 'Twig\Node\Expression\ConstantExpression')) {
      if ($parent->hasAttribute('value')) {
        $value = $parent->getAttribute('value');

        if (\is_string($value)) {
          $errors[] = ValidatorMessage::createForNode($id, $node, new TranslatableMarkup('Filter `add_class` can not be used on `string`, only `mapping`!'));
        }
      }
    }

    return $errors;
  }

  /**
   * Check filter clean_id.
   *
   * @param string $id
   *   The ID of the node.
   * @param \Twig\Node\Node $node
   *   The Twig node being processed.
   * @param \Twig\Node\Node $parent
   *   The parent node of the Twig node.
   *
   * @return \Drupal\ui_patterns_devel\ValidatorMessage[]
   *   An array of errors encountered during the validation process.
   */
  private static function cleanId(string $id, Node $node, Node $parent): array {
    $errors = [];

    if (\is_a($parent, 'Twig\Node\Expression\ConstantExpression')) {
      if ($parent->hasAttribute('value')) {
        $value = $parent->getAttribute('value');

        if (!\is_string($value)) {
          $errors[] = ValidatorMessage::createForNode($id, $node, new TranslatableMarkup('Filter `clean_id` can only be applied on string!'));
        }
      }
    }

    return $errors;
  }

  /**
   * Check filter default.
   *
   * @param string $id
   *   The ID of the node.
   * @param \Twig\Node\Node $node
   *   The Twig node being processed.
   * @param \Twig\Node\Node $parent
   *   The parent node of the Twig node.
   * @param array $definition
   *   The component flatten slot and props as name => type.
   *
   * @return \Drupal\ui_patterns_devel\ValidatorMessage[]
   *   An array of errors encountered during the validation process.
   *
   * @SuppressWarnings(PHPMD.CyclomaticComplexity)
   * @SuppressWarnings(PHPMD.NPathComplexity)
   */
  private static function default(string $id, Node $node, Node $parent, array $definition): array {
    $errors = [];

    if (\is_a($parent, 'Twig\Node\Expression\ConstantExpression') && $parent->hasAttribute('value')) {
      $value = $parent->getAttribute('value');
      if (\is_bool($value) || NULL === $value) {
        $errors[] = ValidatorMessage::createForNode($id, $node, new TranslatableMarkup('Filter `default` is not for booleans or null!'));
      }
    }

    if (\is_a($parent, 'Twig\Node\Expression\ConditionalExpression')) {
      foreach ($parent->getIterator() as $expr) {
        if (!$expr instanceof Node) {
          continue;
        }

        if (!self::validateFilterExpression($expr)) {
          continue;
        }

        $variable_name = $expr->getNode('node')->getNode('expr')->getAttribute('name');

        if (!isset($definition[$variable_name]) || 'boolean' !== $definition[$variable_name]) {
          continue;
        }

        $errors[] = ValidatorMessage::createForNode($id, $node, new TranslatableMarkup("Don't use `default` filter on boolean."));
      }
    }

    if ('Twig\Node\Expression\Filter\DefaultFilter' === get_class($node)) {
      $target = NULL;
      if ($parent->hasNode('expr1')) {
        $expr1 = $parent->getNode('expr1')->getNode('node');
        if ($expr1->hasAttribute('name')) {
          $target = $expr1->getAttribute('name');
        }
      }
      $args = $node->getNode('arguments');
      foreach ($args->getIterator() as $arg) {
        // Detect {{ foo | default(foo) }} case.
        if (!$arg instanceof Node) {
          continue;
        }
        if ($arg->hasNode('expr') && $node->hasNode('node')) {
          $expr = $arg->getNode('expr');
          if (\is_a($expr, 'Twig\Node\Expression\NameExpression') && $expr->hasAttribute('name')) {
            $name = $expr->getAttribute('name');
            if ($target === $name) {
              $errors[] = ValidatorMessage::createForNode($id, $node, new TranslatableMarkup('Filter `default` return the value itself!'), RfcLogLevel::WARNING);
              break;
            }
          }
          break;
        }
        // Detect {{ foo | default(false) }} or {{ foo | default(true) }} case.
        if (\is_a($arg, 'Twig\Node\Expression\ConstantExpression')) {
          $value = $arg->getAttribute('value');
          if (is_bool($value)) {
            $errors[] = ValidatorMessage::createForNode($id, $node, new TranslatableMarkup("Don't use `default` filter with boolean."), RfcLogLevel::WARNING);
            break;
          }
        }
      }
    }

    return $errors;
  }

  /**
   * Get filter expression name.
   *
   * @param \Twig\Node\Node $expr
   *   The Twig node being processed.
   *
   * @return bool
   *   If we can have a name.
   */
  private static function validateFilterExpression(Node $expr): bool {
    return \is_a($expr, 'Twig\Node\Expression\FilterExpression') &&
        $expr->hasNode('node') &&
        $expr->getNode('node')->hasNode('expr') &&
        $expr->getNode('node')->getNode('expr')->hasAttribute('name');
  }

  /**
   * Check filter set_attribute.
   *
   * @param string $id
   *   The ID of the node.
   * @param \Twig\Node\Node $node
   *   The Twig node being processed.
   * @param \Twig\Node\Node $parent
   *   The parent node of the Twig node.
   *
   * @return \Drupal\ui_patterns_devel\ValidatorMessage[]
   *   An array of errors encountered during the validation process.
   *
   * @SuppressWarnings(PHPMD.CyclomaticComplexity)
   * @SuppressWarnings(PHPMD.NPathComplexity)
   */
  private static function setAttribute(string $id, Node $node, Node $parent): array {
    $errors = [];

    if (\is_a($parent, 'Twig\Node\Expression\FilterExpression')) {
      $filter_name = $parent->getNode('filter')->getAttribute('value');
      $allowed_previous_filters = ['map', 'reverse', 'split', 'first', 'last', 'default', 'set_attribute'];

      if (!\in_array($filter_name, $allowed_previous_filters, TRUE)) {
        $errors[] = ValidatorMessage::createForNode($id, $node, new TranslatableMarkup(
          'Filter `set_attribute` do not allow previous filter: `@filter`!',
          [
            '@filter' => $filter_name,
          ]
        ));
      }
    }

    if (!$node->hasNode('arguments')) {
      return [];
    }

    $target = $node->getNode('arguments');
    foreach ($target->getIterator() as $index => $arg) {
      if (1 !== $index) {
        continue;
      }
      if (!\is_object($arg)) {
        continue;
      }

      if (\is_a($arg, 'Twig\Node\Expression\ArrayExpression')) {
        foreach ($arg->getIterator() as $key => $value) {
          if (!$value instanceof Node) {
            continue;
          }

          if (0 !== $key || 0 === $value->getAttribute('value')) {
            continue;
          }

          $errors[] = ValidatorMessage::createForNode($id, $node, new TranslatableMarkup('Filter `set_attribute` second argument can not be a mapping!'));
        }
      }

      if (!\is_a($arg, 'Twig\Node\Expression\ConstantExpression')) {
        continue;
      }

      if (!$arg->hasAttribute('value')) {
        continue;
      }

      $value = $arg->getAttribute('value');

      if (NULL !== $value) {
        continue;
      }

      $errors[] = ValidatorMessage::createForNode($id, $node, new TranslatableMarkup('Filter `set_attribute` second argument can not be null!'));
    }

    return $errors;
  }

  /**
   * Check filter t.
   *
   * @param string $id
   *   The ID of the node.
   * @param \Twig\Node\Node $node
   *   The Twig node being processed.
   * @param \Twig\Node\Node $parent
   *   The parent node of the Twig node.
   *
   * @return \Drupal\ui_patterns_devel\ValidatorMessage[]
   *   An array of errors encountered during the validation process.
   */
  private static function t(string $id, Node $node, Node $parent): array {
    $errors = [];

    if (\is_a($parent, 'Twig\Node\CheckToStringNode')) {
      if ($parent->hasNode('expr')) {
        $errors[] = ValidatorMessage::createForNode($id, $node, new TranslatableMarkup('Filter `trans` or `t` unsafe translation, do not translate variables!'), RfcLogLevel::NOTICE);
      }
    }

    if (\is_a($parent, 'Twig\Node\Expression\ConstantExpression')) {
      if ($parent->hasAttribute('value')) {
        $value = $parent->getAttribute('value');

        if (empty($value)) {
          $errors[] = ValidatorMessage::createForNode($id, $node, new TranslatableMarkup('Filter `trans` or `t` is applied on an empty string'), RfcLogLevel::NOTICE);
        }
      }
    }

    if (\is_a($parent, 'Twig\Node\Expression\ArrayExpression')) {
      $errors[] = ValidatorMessage::createForNode($id, $node, new TranslatableMarkup('Filter `trans` or `t` can only be applied on string!'));
    }

    return $errors;
  }

  /**
   * Check filter trans. Same as t.
   *
   * @param string $id
   *   The ID of the node.
   * @param \Twig\Node\Node $node
   *   The Twig node being processed.
   * @param \Twig\Node\Node $parent
   *   The parent node of the Twig node.
   *
   * @return \Drupal\ui_patterns_devel\ValidatorMessage[]
   *   An array of errors encountered during the validation process.
   */
  private static function trans(string $id, Node $node, Node $parent): array {
    // @phpcs:disable Drupal.Semantics.FunctionT.NotLiteralString
    return self::t($id, $node, $parent);
  }

}
