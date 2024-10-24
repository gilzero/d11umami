<?php

declare(strict_types=1);

namespace Drupal\ui_patterns_devel\Plugin\TwigValidatorRule;

use Drupal\Core\Logger\RfcLogLevel;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ui_patterns_devel\Attribute\TwigValidatorRule;
use Drupal\ui_patterns_devel\TwigValidator\TwigNodeFinder;
use Drupal\ui_patterns_devel\TwigValidatorRulePluginBase;
use Drupal\ui_patterns_devel\ValidatorMessage;
use Twig\Node\Node;

/**
 * Plugin implementation of the twig_validator_rule.
 */
#[TwigValidatorRule(
  id: 'test',
  twig_node_type: 'Twig\Node\Expression\TestExpression',
  rule_on_name: [],
  label: new TranslatableMarkup('Tests rules'),
  description: new TranslatableMarkup('Rules around Twig tests.'),
)]
final class TwigValidatorRuleTest extends TwigValidatorRulePluginBase {

  /**
   * Process Twig\Node\Expression\Test\NullTest.
   *
   * @param \Twig\Node\Node $node
   *   The node to process.
   * @param string $message
   *   The message to set.
   *
   * @return int|null
   *   The log level.
   */
  protected static function processNullTest(Node $node, string &$message) : ?int {
    if ($node->hasAttribute('parent')) {
      $parent = $node->getAttribute('parent');
      if (\is_a($parent, 'Twig\Node\Expression\Unary\NotUnary')) {
        $message = 'Use `|default(foo)` filter instead of null ternary `??`.';
        return RfcLogLevel::WARNING;
      }
      else {
        $message = 'Not needed in Drupal because strict_variables=false.';
        return RfcLogLevel::WARNING;
      }
    }
    return NULL;
  }

  /**
   * Process Twig\Node\Expression\Test\DefinedTest.
   *
   * @param \Twig\Node\Node $node
   *   The node to process.
   * @param string $message
   *   The message to set.
   *
   * @return int|null
   *   The log level.
   */
  protected static function processDefinedTest(Node $node, string &$message) : ?int {
    $inDefaultFilter = TwigNodeFinder::findParentIs(
      $node,
      'Twig\Node\Expression\Filter\DefaultFilter'
    );

    if (!$inDefaultFilter) {
      if ($node->hasAttribute('parent')) {
        $parent = $node->getAttribute('parent');
        if (!\is_a($parent, 'Twig\Node\Expression\Binary\AndBinary')) {
          $message = 'Not needed in Drupal because strict_variables=false.';
          return RfcLogLevel::WARNING;
        }
      }
    }
    return NULL;
  }

  /**
   * Process Twig\Node\Expression\TestExpression.
   *
   * @param \Twig\Node\Node $node
   *   The node to process.
   * @param string $message
   *   The message to set.
   *
   * @return int|null
   *   The log level.
   */
  protected static function processTestExpression(Node $node, string &$message) : ?int {
    if (!$node->hasAttribute('name')) {
      return NULL;
    }
    $name = $node->getAttribute('name');
    switch ($name) {
      case 'empty':
        $message = 'The exact same as just testing the variable, empty is not needed.';
      // @phpcs:disable
      // @todo enable when Twig 3.11 with better iterable.
      // case 'iterable':
      //   $message = new TranslatableMarkup('Return true for mapping and sequence.');
      //   return [ValidatorMessage::createForNode($id, $node, $message, RfcLogLevel::WARNING)];
      // @phpcs:enable
    }
    return RfcLogLevel::WARNING;
  }

  /**
   * {@inheritdoc}
   */
  public function processNode(string $id, Node $node, array $definition, array $variableSet): array {
    $class = \get_class($node);
    $nodeClassToMessage = [
      'Twig\Node\Expression\Test\ConstantTest' => [
        'message' => 'Keep components sandboxed by avoiding functions calling Drupal application.',
      ],
      'Twig\Node\Expression\Test\SameasTest' => [
        'message' => 'Equivalent to strict comparison in PHP, often too strict.',
      ],
      'Twig\Node\Expression\Test\NullTest' => [
        'process' => 'processNullTest',
      ],
      'Twig\Node\Expression\Test\DefinedTest' => [
        'process' => 'processDefinedTest',
      ],
      'Twig\Node\Expression\TestExpression' => [
        'process' => 'processTestExpression',
      ],
    ];

    if (isset($nodeClassToMessage[$class])) {
      $message = "";
      $level = NULL;
      $processData = $nodeClassToMessage[$class];
      if (isset($processData['message'])) {
        $message = $processData['message'];
      }
      if (isset($processData['process']) && method_exists(self::class, $processData['process'])) {
        $method_name = $processData['process'];
        $level = self::$method_name($node, $message);
      }
      if (!empty($message)) {
        // @phpcs:disable Drupal.Semantics.FunctionT.NotLiteralString
        $message = new TranslatableMarkup($message);
        return ($level === NULL) ? [ValidatorMessage::createForNode($id, $node, $message)] : [ValidatorMessage::createForNode($id, $node, $message, $level)];
      }
    }
    return [];
  }

}
