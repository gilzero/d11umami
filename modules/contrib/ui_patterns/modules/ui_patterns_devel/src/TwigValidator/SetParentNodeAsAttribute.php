<?php

declare(strict_types=1);

namespace Drupal\ui_patterns_devel\TwigValidator;

use Twig\Environment;
use Twig\Node\Node;
use Twig\NodeVisitor\NodeVisitorInterface;

/**
 * The SetParentNodeAsAttribute class.
 */
final class SetParentNodeAsAttribute implements NodeVisitorInterface {

  /**
   * {@inheritdoc}
   */
  public function enterNode(Node $node, Environment $env): Node {
    foreach ($node as $subNode) {
      /** @var \Twig\Node\Node $subNode */
      $subNode->setAttribute(NodeAttribute::PARENT, $node);
    }

    return $node;
  }

  /**
   * {@inheritdoc}
   */
  public function getPriority(): int {
    return 0;
  }

  /**
   * {@inheritdoc}
   */
  public function leaveNode(Node $node, Environment $env): Node {
    return $node;
  }

}
