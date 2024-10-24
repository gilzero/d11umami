<?php

namespace Drupal\ui_patterns_layouts\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Extension\ThemeHandlerInterface;
use Drupal\Core\Layout\LayoutDefinition;
use Drupal\Core\Plugin\Context\ContextDefinition;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\ui_patterns\ComponentPluginManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;
use Symfony\Component\Filesystem\Path;

/**
 * Provides layout plugin definitions for components.
 *
 * @see \Drupal\ui_patterns_layouts\Plugin\Layout\ComponentLayout
 */
class ComponentLayout extends DeriverBase implements ContainerDeriverInterface {

  /**
   * Constructs new ComponentLayout Deriver.
   *
   * @param \Drupal\ui_patterns\ComponentPluginManager $pluginManager
   *   The component plugin manager.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   The module handler.
   * @param \Drupal\Core\Extension\ThemeHandlerInterface $themeHandler
   *   The theme handler.
   */
  public function __construct(protected ComponentPluginManager $pluginManager, protected ModuleHandlerInterface $moduleHandler, protected ThemeHandlerInterface $themeHandler) {
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $container->get('plugin.manager.sdc'),
      $container->get('module_handler'),
      $container->get('theme_handler'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $components = $this->pluginManager->getSortedDefinitions();
    foreach ($components as $component) {
      $component_instance = $this->pluginManager->find($component['id']);
      /** @var \Drupal\Core\Layout\LayoutDefinition $base_plugin_definition */
      $definition = array_merge([
        "deriver" => $base_plugin_definition->getDeriver(),
        "class" => $base_plugin_definition->getClass(),
      ], [
        'label' => $component['annotated_name'] ?? $component['name'] ?? $component['id'],
        'category' => $component['group'] ?? t('Others'),
        'provider' => $component['provider'],
        'id' => $component['id'],
        'context_definitions' => [
          'entity' => new ContextDefinition('entity', 'Entity', FALSE),
        ],
        'admin_label' => $component['annotated_name'] ?? $component['name'] ?? $component['id'],
        // "context_mapping" => ["entity" => "layout_builder.entity"],
        "regions" => [],
      ]);

      $layout_definition = new LayoutDefinition($definition);
      if (isset($component['slots']) && is_array($component['slots']) && count($component['slots']) > 0) {
        $regions = [];
        foreach ($component['slots'] as $slot_id => $slot) {
          $regions[$slot_id] = ['label' => $slot['title']];
        }
        $layout_definition->setRegions($regions);
        $layout_definition->setDefaultRegion(array_key_first($regions));
      }
      if (isset($component['icon_map'])) {
        $layout_definition->setIconMap($component['icon_map']);
      }
      $thumbnail_path = $component_instance->metadata->getThumbnailPath();
      $layout_path = $this->getLayoutDefinitionPath($layout_definition);
      if (!empty($thumbnail_path)) {
        $layout_definition->setIconPath($this->getIconPath($thumbnail_path, $layout_path));
      }
      if (isset($component['icon_path'])) {
        $layout_definition->setIconPath($this->getIconPath($component['icon_path'], $layout_path));
      }
      $id = str_replace('-', '_', (string) $component['id']);
      $this->derivatives[$id] = $layout_definition;
    }
    return $this->derivatives;
  }

  /**
   * Get Layout definition path.
   *
   * @param \Drupal\Core\Layout\LayoutDefinition $definition
   *   Layout definition.
   *
   * @return string
   *   Path.
   */
  protected function getLayoutDefinitionPath(LayoutDefinition $definition) {
    // Add the module or theme path to the 'path'.
    $provider = $definition->getProvider();
    if ($this->moduleHandler->moduleExists($provider)) {
      $base_path = $this->moduleHandler->getModule($provider)->getPath();
    }
    elseif ($this->themeHandler->themeExists($provider)) {
      $base_path = $this->themeHandler->getTheme($provider)->getPath();
    }
    else {
      $base_path = '';
    }

    $path = $definition->getPath();
    $path = !empty($path) ? $base_path . '/' . $path : $base_path;
    return $path;
  }

  /**
   * Get a layout icon path.
   *
   * @param string $icon_path
   *   Icon path as input.
   * @param string $layout_path
   *   Path of the layout.
   *
   * @return string
   *   Path of the icon, relative to the layout path.
   */
  protected function getIconPath(string $icon_path, string $layout_path) : string {
    $base_path = base_path();
    $layout_path = Path::makeAbsolute($layout_path, $base_path);
    $path = (new SymfonyFilesystem())->makePathRelative(Path::makeAbsolute($icon_path, $base_path), $layout_path);
    return str_ends_with($path, "/") ? substr($path, 0, strlen($path) - 1) : $path;
  }

}
