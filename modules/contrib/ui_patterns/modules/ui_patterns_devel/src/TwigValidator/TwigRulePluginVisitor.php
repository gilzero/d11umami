<?php

declare(strict_types=1);

namespace Drupal\ui_patterns_devel\TwigValidator;

use Drupal\ui_patterns_devel\TwigValidatorRulePluginManager;
use Twig\Environment;
use Twig\Node\Node;
use Twig\NodeVisitor\NodeVisitorInterface;

/**
 * Class TwigRulePluginVisitor to visit and collect errors.
 */
final class TwigRulePluginVisitor implements NodeVisitorInterface {

  /**
   * List of errors.
   *
   * @var \Drupal\ui_patterns_devel\ValidatorMessage[]
   */
  private array $errors = [];

  public function __construct(
    private string $id,
    private readonly TwigValidatorRulePluginManager $rulePluginManager,
    private array $rules,
    private array $definition,
    private array $variableSet,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function enterNode(Node $node, Environment $env): Node {
    foreach ($this->rules as $rule) {
      if (!\is_a($node, $rule['twig_node_type'])) {
        continue;
      }

      /** @var \Drupal\ui_patterns_devel\TwigValidatorRuleInterface $rule_instance */
      $rule_instance = $this->rulePluginManager->createInstance($rule['id']);
      foreach ($rule_instance->processNode($this->id, $node, $this->definition, $this->variableSet) as $error) {
        $this->errors[] = $error;
      }
    }

    return $node;
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
   */
  public function getPriority(): int {
    return 10;
  }

  /**
   * {@inheritdoc}
   */
  public function leaveNode(Node $node, Environment $env): Node {
    return $node;
  }

}
