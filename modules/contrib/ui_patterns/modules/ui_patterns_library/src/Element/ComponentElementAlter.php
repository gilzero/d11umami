<?php

declare(strict_types=1);

namespace Drupal\ui_patterns_library\Element;

use Drupal\Core\Security\TrustedCallbackInterface;
use Drupal\Core\Theme\ComponentPluginManager;
use Drupal\ui_patterns\Element\ComponentElementAlter as UiPatternsComponentElementAlter;

/**
 * Renders a component story.
 */
class ComponentElementAlter implements TrustedCallbackInterface {

  /**
   * Constructs a ComponentElementAlter.
   *
   * @param \Drupal\Core\Theme\ComponentPluginManager $componentPluginManager
   *   The component plugin manager.
   * @param \Drupal\ui_patterns\Element\ComponentElementAlter $componentElementAlter
   *   The component element alter.
   */
  public function __construct(
    protected ComponentPluginManager $componentPluginManager,
    protected UiPatternsComponentElementAlter $componentElementAlter,
  ) {
  }

  /**
   * {@inheritdoc}
   */
  public static function trustedCallbacks() {
    return ['alter'];
  }

  /**
   * Alter SDC component element.
   */
  public function alter(array $element): array {
    $element = $this->loadStory($element);
    return $element;
  }

  /**
   * Load story from component definition.
   */
  protected function loadStory(array $element): array {
    if (!isset($element["#story"])) {
      return $element;
    }
    $story_id = $element["#story"];
    $component = $this->componentPluginManager->getDefinition($element["#component"]);
    if (!isset($component["stories"])) {
      return $element;
    }
    if (!isset($component["stories"][$story_id])) {
      return $element;
    }
    $story = $component["stories"][$story_id];
    $slots = array_merge($element["#slots"] ?? [], $story["slots"] ?? []);
    $element["#slots"] = $this->componentElementAlter->processStoriesSlots($slots);
    $element["#props"] = array_merge($element["#props"] ?? [], $story["props"] ?? []);
    return $element;
  }

}
