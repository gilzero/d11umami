<?php

declare(strict_types=1);

namespace Drupal\ui_patterns_devel\TwigValidator;

use Twig\Node\Node;

/**
 * The TwigNodeFinder Class.
 *
 * This class provides methods for finding and filtering parent nodes in a
 * Twig template.
 */
final class TwigNodeFinder {

  /**
   * Filter parent nodes with a callable function.
   *
   * @param \Twig\Node\Node $node
   *   The Twig Node.
   * @param callable(\Twig\Node\Node): bool $filter
   *   The Callable function to apply on the Node.
   *
   * @return \Twig\Node\Node[]
   *   List of twig nodes.
   */
  public static function filterParents(Node $node, callable $filter): array {
    $matchingNodes = [];

    $currentNode = $node;

    while ($currentNode->hasAttribute(NodeAttribute::PARENT)) {
      $currentNode = $currentNode->getAttribute(NodeAttribute::PARENT);

      if (!$filter($currentNode)) {
        continue;
      }

      $matchingNodes[] = $currentNode;
    }

    return $matchingNodes;
  }

  /**
   * Find if a parent has a specific class.
   *
   * @param \Twig\Node\Node $node
   *   The Twig Node.
   * @param string $class
   *   The class name to look up.
   *
   * @return bool
   *   If we find the class.
   */
  public static function findParentIs(Node $node, string $class): bool {
    $currentNode = $node;
    while ($currentNode->hasAttribute(NodeAttribute::PARENT)) {
      $currentNode = $currentNode->getAttribute(NodeAttribute::PARENT);
      if (\is_a($currentNode, $class)) {
        return TRUE;
      }
    }

    return FALSE;
  }

}
