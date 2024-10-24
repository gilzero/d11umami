<?php

declare(strict_types=1);

namespace Drupal\ui_patterns\Template;

use Drupal\ui_patterns\ComponentPluginManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Twig extension providing UI Patterns-specific functionalities.
 *
 * @package Drupal\ui_patterns\Template
 */
class TwigExtension extends AbstractExtension {

  use AttributesFilterTrait;

  /**
   * Creates TwigExtension.
   *
   * @param \Drupal\ui_patterns\ComponentPluginManager $componentManager
   *   The component plugin manager.
   */
  public function __construct(
    protected ComponentPluginManager $componentManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function getName(): string {
    return 'ui_patterns';
  }

  /**
   * {@inheritdoc}
   */
  public function getNodeVisitors(): array {
    return [
      new ComponentNodeVisitor($this->componentManager),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFunctions() {
    return [
      new TwigFunction('component', [$this, 'renderComponent']),
      new TwigFunction('_ui_patterns_preprocess_props', [$this, 'preprocessProps'], ['needs_context' => TRUE]),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFilters() {
    return [
      new TwigFilter('add_class', [$this, 'addClass']),
      new TwigFilter('set_attribute', [$this, 'setAttribute']),
    ];
  }

  /**
   * Render given component.
   *
   * @param string $component_id
   *   Component ID.
   * @param array $slots
   *   Pattern slots.
   * @param array $props
   *   Pattern props.
   *
   * @return array
   *   Pattern render array.
   *
   * @see \Drupal\Core\Theme\Element\ComponentElement
   */
  public function renderComponent(string $component_id, array $slots = [], array $props = []) {
    return [
      '#type' => 'component',
      '#component' => $component_id,
      '#slots' => $slots,
      '#props' => $props,
    ];
  }

  /**
   * Preprocess props.
   *
   * This function must not be used by the templates authors. In a perfect
   * world, it would not be necessary to set such a function. We did that to be
   * compatible with SDC's ComponentNodeVisitor, in order to execute props
   * preprocessing after SDC's validate_component_props Twig function.
   *
   * @param array $context
   *   The context provided to the component.
   * @param string $component_id
   *   The component ID.
   *
   * @throws \Drupal\Core\Render\Component\Exception\InvalidComponentException
   */
  public function preprocessProps(array &$context, string $component_id): void {
    $component = $this->componentManager->find($component_id);
    $props = $component->metadata->schema['properties'] ?? [];
    foreach ($context as $variable => $value) {
      if (!isset($props[$variable])) {
        continue;
      }
      $prop_type = $props[$variable]['ui_patterns']['type_definition'];
      $context[$variable] = $prop_type->preprocess($value);
    }
  }

}
