<?php

declare(strict_types=1);

namespace Drupal\ui_patterns_devel\TwigValidator;

use Drupal\Core\Logger\RfcLogLevel;
use Drupal\Core\Plugin\Component;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ui_patterns\ComponentPluginManager;
use Drupal\ui_patterns_devel\TwigValidatorRulePluginManager;
use Drupal\ui_patterns_devel\ValidatorBase;
use Drupal\ui_patterns_devel\ValidatorMessage;
use Twig\Environment;
use Twig\Error\Error;
use Twig\NodeTraverser;
use Twig\Source;
use Twig\TemplateWrapper;

/**
 * The Twig Validator service.
 *
 * Provide a Twig static analysis tool to detect and catch errors on Component
 * and enforce good practices.
 * Inspired by: https://github.com/matthiasnoback/phpstan-twig-analysis.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
final class TwigValidator extends ValidatorBase {

  /**
   * The Twig Template to control.
   */
  private ?TemplateWrapper $template = NULL;

  /**
   * Constructs a new TwigValidator object.
   *
   * @param \Twig\Environment $twig
   *   The Twig environment service.
   * @param \Drupal\ui_patterns_devel\TwigValidatorRulePluginManager $rulePluginManager
   *   The Twig validator rule manager plugin service.
   * @param \Drupal\ui_patterns\ComponentPluginManager $componentPluginManager
   *   The component manager plugin service.
   */
  public function __construct(
    private readonly Environment $twig,
    private readonly TwigValidatorRulePluginManager $rulePluginManager,
    private readonly ComponentPluginManager $componentPluginManager,
  ) {
    $this->twig->enableDebug();
    $this->twig->enableAutoReload();
  }

  /**
   * {@inheritdoc}
   */
  public function validateComponent(string $id, Component $component): void {
    $path = $component->getTemplatePath();

    if (NULL === $path) {
      $this->addMessage(ValidatorMessage::createForString($id, new TranslatableMarkup('This component has no template!'), RfcLogLevel::CRITICAL));
      return;
    }

    try {
      $this->template = $this->twig->load($path);
    }
    catch (Error $error) {
      $this->addMessage(ValidatorMessage::createFromTwigError($id, $error));
    }

    $source = $this->twig->getLoader()->getSourceContext($path);
    $this->processSource($id, $source);
  }

  /**
   * Validates a source and processes the source for errors.
   *
   * @todo this is used only for tests, should move or change tests?
   *
   * @param string $source
   *   The source code or path to validate.
   */
  public function validateSource(string $source): void {
    try {
      $this->template = $this->twig->createTemplate($source);
    }
    catch (Error $error) {
      $this->addMessage(ValidatorMessage::createFromTwigError('', $error));
    }

    if (NULL === $this->template) {
      return;
    }

    $source = $this->template->getSourceContext();
    $this->processSource('', $source);
  }

  /**
   * Validator entrypoint to collect errors from Twig source.
   *
   * @param string $id
   *   The ID of the component.
   * @param \Twig\Source $source
   *   The source code to process.
   */
  private function processSource(string $id, Source $source): void {
    try {
      $nodeTree = $this->twig->parse($this->twig->tokenize($source));
    }
    catch (Error $error) {
      $this->addMessage(ValidatorMessage::createFromTwigError($id, $error));
      return;
    }

    $pluginDefinitions = $this->rulePluginManager->getDefinitions();
    $definitionVariables = $this->flattenDefinitionVariablesWithType($id);

    // Set the parent node as an attribute on each node, so rules can access
    // traverse up the node tree:
    $nodeTraverser = new NodeTraverser($this->twig, [new SetParentNodeAsAttribute()]);
    $nodeTraverser->traverse($nodeTree);

    $variableCollector = new TwigVariableCollectorVisitor();
    $nodeTraverser = new NodeTraverser($this->twig, [$variableCollector]);
    $nodeTraverser->traverse($nodeTree);

    $this->addMessages($variableCollector->errors());

    $collectedVariables = $this->collectVariables($variableCollector, $id, $definitionVariables);

    $rulePluginVisitor = new TwigRulePluginVisitor($id, $this->rulePluginManager, $pluginDefinitions, $definitionVariables, $collectedVariables);
    $nodeTraverser = new NodeTraverser($this->twig, [$rulePluginVisitor]);
    $nodeTraverser->traverse($nodeTree);

    $this->addMessages($rulePluginVisitor->errors());
  }

  /**
   * Collect variables on the node tree and add errors.
   *
   * @param \Drupal\ui_patterns_devel\TwigValidator\TwigVariableCollectorVisitor $variableCollector
   *   The variable collector visitor.
   * @param string $id
   *   The ID of the component.
   * @param array $definitionVariables
   *   The flatten definitions as $name => type.
   *
   * @return array
   *   The variables list.
   */
  private function collectVariables(TwigVariableCollectorVisitor $variableCollector, string $id, array $definitionVariables): array {
    $diff = \array_diff_key($variableCollector->getVariableSetList() + $definitionVariables, $variableCollector->getVariablePrintList());

    $whitelist = $variableCollector::ALLOW_NOT_SET_VARIABLE;
    $unusedVarList = \array_filter(\array_keys($diff), static fn($varName) => !isset($whitelist[$varName]));

    if (!empty($unusedVarList)) {
      $message = new TranslatableMarkup('Unused variables: @list', ['@list' => \implode(', ', $unusedVarList)]);
      $this->addMessage(ValidatorMessage::createForTwigString($id, $message));
    }

    return $variableCollector->getVariableSetList();
  }

  /**
   * Flatten Definition to keep only variables with type.
   *
   * @param string $id
   *   The ID of the component.
   *
   * @return array
   *   Flat variables with their types.
   */
  private function flattenDefinitionVariablesWithType(string $id): array {
    // Attributes is injected and should be always used.
    $result['attributes'] = 'object';

    if (!$this->componentPluginManager->hasDefinition($id)) {
      return [];
    }

    $definition = $this->componentPluginManager->getDefinition($id);

    if (!\is_array($definition)) {
      return [];
    }

    foreach ($definition['slots'] ?? [] as $name => $value) {
      $result[$name] = $value['type'] ?? 'array';
    }

    foreach ($definition['props']['properties'] ?? [] as $name => $value) {
      $result[$name] = $value['type'] ?? '';
    }

    return $result;
  }

}
