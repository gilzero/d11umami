<?php

namespace Drupal\ui_patterns\Template;

use Drupal\Core\Plugin\Component;
use Drupal\Core\Render\Component\Exception\ComponentNotFoundException;
use Drupal\Core\Template\ComponentNodeVisitor as CoreComponentNodeVisitor;
use Drupal\Core\Theme\ComponentPluginManager;
use Twig\Environment;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\FunctionExpression;
use Twig\Node\ModuleNode;
use Twig\Node\Node;
use Twig\Node\PrintNode;
use Twig\NodeVisitor\NodeVisitorInterface;

/**
 * Provides a ComponentNodeVisitor to change the generated parse-tree.
 *
 * @internal
 */
class ComponentNodeVisitor implements NodeVisitorInterface {

  /**
   * Node name: expr.
   */
  const NODE_NAME_EXPR = 'expr';

  /**
   * The component plugin manager.
   */
  protected ComponentPluginManager $componentManager;

  /**
   * Constructs a new ComponentNodeVisitor object.
   *
   * @param \Drupal\Core\Theme\ComponentPluginManager $component_plugin_manager
   *   The component plugin manager.
   */
  public function __construct(ComponentPluginManager $component_plugin_manager) {
    $this->componentManager = $component_plugin_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function enterNode(Node $node, Environment $env): Node {
    return $node;
  }

  /**
   * {@inheritdoc}
   */
  public function leaveNode(Node $node, Environment $env): ?Node {
    if (!$node instanceof ModuleNode) {
      return $node;
    }
    $component = $this->getComponent($node);
    if (!($component instanceof Component)) {
      return $node;
    }
    $line = $node->getTemplateLine();
    $function = $this->buildPreprocessPropsFunction($line, $component);
    $node = $this->injectFunction($node, $function);
    return $node;
  }

  /**
   * {@inheritdoc}
   */
  public function getPriority(): int {
    $priority = &drupal_static(__METHOD__);
    if (!isset($priority)) {
      $original_node_visitor = new CoreComponentNodeVisitor($this->componentManager);
      // Ensure that this component node visitor's priority is higher than
      // core's node visitor class for components, because this class has to run
      // core's class.
      $priority = $original_node_visitor->getPriority() + 1;
    }
    return is_numeric($priority) ? (int) $priority : 0;
  }

  /**
   * Finds the SDC for the current module node.
   *
   * A duplicate of \Drupal\Core\Template\ComponentNodeVisitor::getComponent()
   *
   * @param \Twig\Node\Node $node
   *   The node.
   *
   * @return \Drupal\Core\Plugin\Component|null
   *   The component, if any.
   */
  protected function getComponent(Node $node): ?Component {
    $component_id = $node->getTemplateName();
    if (!preg_match('/^[a-z]([a-zA-Z0-9_-]*[a-zA-Z0-9])*:[a-z]([a-zA-Z0-9_-]*[a-zA-Z0-9])*$/', $component_id)) {
      return NULL;
    }
    try {
      return $this->componentManager->find($component_id);
    }
    catch (ComponentNotFoundException $e) {
      return NULL;
    }
  }

  /**
   * Build the _ui_patterns_preprocess_props Twig function.
   *
   * @param int $line
   *   The line .
   * @param \Drupal\Core\Plugin\Component $component
   *   The component.
   *
   * @return \Twig\Node\Node
   *   The Twig function.
   */
  protected function buildPreprocessPropsFunction(int $line, Component $component): Node {
    $component_id = $component->getPluginId();
    $function_parameter = new ConstantExpression($component_id, $line);
    $function_parameters_node = new Node([$function_parameter]);
    $function = new FunctionExpression('_ui_patterns_preprocess_props', $function_parameters_node, $line);
    return new PrintNode($function, $line);
  }

  /**
   * Injects custom Twig nodes into given node as child nodes.
   *
   * The function will be injected direct after  validate_component_props
   * function already injected by SDC's ComponentNodeVisitor.
   *
   * @param \Twig\Node\Node $node
   *   The node where we will inject the function in.
   * @param \Twig\Node\Node $function
   *   The Twig function.
   *
   * @return \Twig\Node\Node
   *   The node with the function inserted.
   */
  protected function injectFunction(Node $node, Node $function): Node {
    $insertion = new Node([$node->getNode('display_start'), $function]);
    $node->setNode('display_start', $insertion);
    return $node;
  }

}
