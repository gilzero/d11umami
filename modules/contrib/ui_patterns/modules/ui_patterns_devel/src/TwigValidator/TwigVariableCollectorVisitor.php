<?php

declare(strict_types=1);

namespace Drupal\ui_patterns_devel\TwigValidator;

use Twig\Environment;
use Twig\Node\BlockReferenceNode;
use Twig\Node\Expression\AssignNameExpression;
use Twig\Node\Expression\NameExpression;
use Twig\Node\MacroNode;
use Twig\Node\Node;
use Twig\NodeVisitor\NodeVisitorInterface;

/**
 * The Collector to get variables printed and set in Twig.
 */
final class TwigVariableCollectorVisitor implements NodeVisitorInterface {

  /**
   * Allow list of variables injected or internal.
   *
   * @var array
   */
  public const ALLOW_NOT_SET_VARIABLE = [
    'variant' => 'string',
    'loop' => '',
    '_self' => '',
    '_key' => '',
    '__internal_parse_0' => '',
    '__internal_parse_1' => '',
    '__internal_parse_2' => '',
    '__internal_parse_3' => '',
    '__internal_parse_4' => '',
    '__internal_parse_5' => '',
  ];

  /**
   * List of errors.
   *
   * @var \Drupal\ui_patterns_devel\ValidatorMessage[]
   */
  private array $errors = [];

  /**
   * List of printed variables.
   */
  private array $variablePrintList = [];

  /**
   * List of variables set.
   */
  private array $variableSetList = [];

  /**
   * Get use variables, mostly NameExpression.
   */
  public function getVariablePrintList(): array {
    return $this->variablePrintList;
  }

  /**
   * Get set variables, mostly AssignNameExpression.
   */
  public function getVariableSetList(): array {
    return $this->variableSetList;
  }

  /**
   * Get the errors list.
   *
   * @return \Drupal\ui_patterns_devel\ValidatorMessage[]
   *   List of errors.
   */
  public function errors(): array {
    return $this->errors;
  }

  /**
   * {@inheritdoc}
   *
   * @SuppressWarnings(PHPMD.UnusedLocalVariable)
   */
  public function enterNode(Node $node, Environment $env): Node {
    // Collect block names as valid variable, fix #3464630.
    if ($node instanceof BlockReferenceNode) {
      if ($node->hasAttribute('name')) {
        $name = $node->getAttribute('name');
        $this->variablePrintList[$name] = '';
      }
    }

    if ($node instanceof NameExpression) {
      // Is parent a macro? Than add macro parameters to set list.
      $macro = TwigNodeFinder::filterParents($node, static fn (Node $node): bool => $node instanceof MacroNode);
      if (!empty($macro) && isset($macro[0])) {
        foreach ($macro[0]->getNode('arguments') as $key => $value) {
          $this->variableSetList[$key] = '';
        }
      }

      // Find ConstantExpression in parent to guess type?
      $name = $node->getAttribute('name');
      if ($node instanceof AssignNameExpression) {
        $this->variableSetList[$name] = '';
      }
      else {
        $this->variablePrintList[$name] = '';
      }
    }

    return $node;
  }

  /**
   * {@inheritdoc}
   */
  public function leaveNode(Node $node, Environment $env): ?Node {
    return $node;
  }

  /**
   * {@inheritdoc}
   */
  public function getPriority(): int {
    return 10;
  }

}
