<?php

namespace Drupal\at_core\Layout;

use Drupal\layout_plugin\Layout as LayoutPlugin;

/**
 *
 */
class LayoutDiscoveryPlugin {

  /**
   * @return string
   */
  public static function getProvider() {
    $provider = '';

    // Layout discovery, Drupal core 8.3.x and up.
    if (class_exists('\Drupal\Core\Layout\LayoutPluginManager')) {
      if (\Drupal::moduleHandler()->moduleExists('layout_discovery') == TRUE) {
        $provider = 'layout_discovery';
      }
    }
    // Layout plugin contrib module, Drupal core 8.2.x and below.
    elseif (\Drupal::moduleHandler()->moduleExists('layout_plugin') == TRUE) {
      $provider = 'layout_plugin';
    }

    return $provider;
  }

  /**
   * @return null
   */
  public static function getDefinitions() {
    $provider = self::getProvider();
    $layout_definitions = NULL;

    if (!empty($provider)) {
      if ($provider === 'layout_discovery') {
        $layout_definitions['layout_discovery'] = \Drupal::service('plugin.manager.core.layout')->getDefinitions();
      }
      elseif ($provider === 'layout_plugin') {
        $layout_definitions['layout_plugin'] = LayoutPlugin::layoutPluginManager()->getDefinitions();
      }
    }

    return $layout_definitions;
  }

  /**
   * @return array
   */
  public static function getThemeHooks() {
    $layout_definitions = self::getDefinitions();
    $theme_hooks = [];

    if (isset($layout_definitions['layout_discovery'])) {
      foreach ($layout_definitions['layout_discovery'] as $info) {
        $theme_hooks[] = $info->getThemeHook();
      }
    }
    elseif (isset($layout_definitions['layout_plugin'])) {
      foreach ($layout_definitions['layout_plugin'] as $info) {
        $theme_hooks[] = $info['theme'];
      }
    }

    return $theme_hooks;
  }

  /**
   * @return array
   */
  public static function libraryNames() {
    $provider = self::getProvider();
    $library_names = [];

    if (!empty($provider)) {
      if ($provider === 'layout_discovery') {
        $library_names = [
          'at.twocol-2-10',
          'at.twocol-3-9',
          'at.twocol-4-8',
          'at.twocol-5-7',
          'at.twocol-6-6',
          'at.twocol-7-5',
          'at.twocol-8-4',
          'at.twocol-9-3',
          'at.twocol-10-2',
          'at.threecol-2-8-2',
          'at.threecol-2-2-8',
          'at.threecol-8-2-2',
          'at.threecol-3-6-3',
          'at.threecol-3-3-6',
          'at.threecol-6-3-3',
          'at.threecol-4-4-4',
          'at.fourcol-3-3-3-3',
          'at.grid-2x2',
          'at.grid-3x3',
          'at.grid-4x4',
        ];
      }
      elseif ($provider === 'layout_plugin') {
        $library_names = [
          'twocol-2-10',
          'twocol-3-9',
          'twocol-4-8',
          'twocol-5-7',
          'twocol-6-6',
          'twocol-7-5',
          'twocol-8-4',
          'twocol-9-3',
          'twocol-10-2',
          'threecol-2-8-2',
          'threecol-2-2-8',
          'threecol-8-2-2',
          'threecol-3-6-3',
          'threecol-3-3-6',
          'threecol-6-3-3',
          'threecol-4-4-4',
          'fourcol-3-3-3-3',
          'grid-2x2',
          'grid-3x3',
          'grid-4x4',
        ];
      }
    }

    return $library_names;
  }

}
