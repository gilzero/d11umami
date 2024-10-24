<?php

declare(strict_types=1);

namespace Drupal\ui_patterns_legacy\Element;

use Drupal\Core\Security\TrustedCallbackInterface;
use Drupal\Core\Theme\ComponentPluginManager;
use Drupal\Core\Theme\ThemeManagerInterface;
use Drupal\ui_patterns\Element\ComponentElementAlter as UiPatternsComponentElementAlter;
use Drupal\ui_patterns_legacy\RenderableConverter;

/**
 * Renders a component story.
 */
class ComponentElementAlter implements TrustedCallbackInterface {

  /**
   * Constructs a ComponentElementAlter.
   *
   * @param \Drupal\Core\Theme\ThemeManagerInterface $themeManager
   *   The theme manager.
   * @param \Drupal\Core\Theme\ComponentPluginManager $componentPluginManager
   *   The component plugin manager.
   * @param \Drupal\ui_patterns\Element\ComponentElementAlter $componentElementAlter
   *   The component element alter.
   */
  public function __construct(
    protected ThemeManagerInterface $themeManager,
    protected ComponentPluginManager $componentPluginManager,
    protected UiPatternsComponentElementAlter $componentElementAlter,
  ) {
  }

  /**
   * {@inheritdoc}
   */
  public static function trustedCallbacks() {
    return ['convert'];
  }

  /**
   * Convert legacy render element to SDC render element.
   */
  public function convert(array $element): array {
    $converter = new RenderableConverter($this->componentPluginManager);
    $theme = $this->themeManager->getActiveTheme()->getName();
    $converter->setExtension($theme);
    $element = $converter->convertPattern($element, "#");
    return $this->addPreviewStory($element);
  }

  /**
   * Load preview.
   *
   * @param array $element
   *   Render array.
   *
   * @return array
   *   Render array.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function addPreviewStory(array $element): array {
    $component = $this->componentPluginManager->getDefinition($element["#component"]);
    if (!isset($component["stories"])) {
      return $element;
    }
    if (empty($component["stories"])) {
      return $element;
    }
    $element["#story"] = $this->getStoryId($component["stories"]);
    $element["#slots"] = $this->componentElementAlter->processStoriesSlots($element["#slots"] ?? []);
    return $element;
  }

  /**
   * Get story ID.
   */
  private function getStoryId(array $stories): string {
    // In UI Patterns 1.x, there was only one story by component, called
    // "preview".
    if (array_key_exists("preview", $stories)) {
      return "preview";
    }
    return (string) array_key_first($stories);
  }

}
