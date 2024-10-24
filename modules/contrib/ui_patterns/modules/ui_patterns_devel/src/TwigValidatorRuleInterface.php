<?php

declare(strict_types=1);

namespace Drupal\ui_patterns_devel;

use Twig\Node\Node;

/**
 * Interface for twig_validator_rule plugins.
 */
interface TwigValidatorRuleInterface {

  /**
   * Returns the rules by names.
   */
  public function getRulesByName(): array;

  /**
   * Returns the allowed names.
   */
  public function getNameAllow(): array;

  /**
   * Returns the deprecated names.
   */
  public function getNameDeprecate(): array;

  /**
   * Returns the warned names.
   */
  public function getNameWarn(): array;

  /**
   * Returns the forbidden names.
   */
  public function getNameForbid(): array;

  /**
   * Returns the ignored names.
   */
  public function getNameIgnore(): array;

  /**
   * Returns the translated plugin label.
   */
  public function label(): string;

  /**
   * Process The current Node for this Twig template.
   *
   * @param string $id
   *   The Component id.
   * @param \Twig\Node\Node $node
   *   The Twig Node to process.
   * @param array $definition
   *   The component flatten slot and props as name => type.
   * @param array $variableSet
   *   The list of variables set in this template.
   *
   * @return \Drupal\ui_patterns_devel\ValidatorMessage[]
   *   List of errors.
   */
  public function processNode(string $id, Node $node, array $definition, array $variableSet): array;

}
