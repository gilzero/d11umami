<?php

namespace Drupal\at_core\Theme;

/**
 *
 */

use Drupal\Core\Extension\ThemeExtensionList;

class ThemeConfig extends ThemeInfo {

  /**
   * @param $theme
   * @return array|mixed
   */
  public function extensionsEnabled($theme) {
    $extensions['is_enabled'] = FALSE;
    if ($theme['type'] === 'adaptive_subtheme') {
      if (isset($theme['config']['enable_extensions']) && $theme['config']['enable_extensions'] === 1) {
        $extensions['is_enabled'] = TRUE;
      }
    }
    elseif ($theme['type'] === 'adaptive_skin') {
      // Skin theme has enabled extensions?
      if (isset($theme['config_skin']['enable_extensions']) && $theme['config_skin']['enable_extensions'] === 1) {
        $extensions['is_enabled'] = TRUE;
      }
      // Base theme has enabled extensions?
      elseif (isset($theme['config']['enable_extensions']) && $theme['config']['enable_extensions'] === 1) {
        $extensions['is_enabled'] = TRUE;
      }
    }

    return $extensions;
  }

  /**
   * @param $theme
   * @return array|mixed
   */
  public function shortcodesEnabled($theme) {
    $shortcodes['is_enabled'] = FALSE;
    if ($theme['type'] === 'adaptive_subtheme') {
      if (isset($theme['config']['enable_shortcodes']) && $theme['config']['enable_shortcodes'] === 1) {
        $shortcodes['is_enabled'] = TRUE;
        $shortcodes['config'] = 'config';
      }
    }
    elseif ($theme['type'] === 'adaptive_skin') {
      // Skin theme has enabled short codes?
      if (isset($theme['config_skin']['enable_shortcodes']) && $theme['config_skin']['enable_shortcodes'] === 1) {
        $shortcodes['is_enabled'] = TRUE;
        $shortcodes['config'] = 'config_skin';
      }
      // Base theme has enabled short codes?
      elseif (isset($theme['config']['enable_shortcodes']) && $theme['config']['enable_shortcodes'] === 1) {
        $shortcodes['is_enabled'] = TRUE;
        $shortcodes['config'] = 'config';
      }
    }

    return $shortcodes;
  }

  /**
   * Array of useful stuff we use to determine asset loading and other functions
   * depending on the active theme type - standard sub theme or a skin theme.
   *
   * @return array|mixed
   */
  public function getConfig() {
    $theme = &drupal_static(__METHOD__);
    if (!isset($theme)) {

      // Name & path.
      $active_theme = $this->getTheme();
      $theme['name'] = $active_theme;
      $theme_path = $this->getSubPath();

      // Type and base theme.
      $theme['type'] = $this->getSubthemeType();
      $theme['base'] = $this->getBaseTheme();

      // Config.
      $active_theme_config = \Drupal::config($theme['name'] . '.settings')->get('settings');
      if ($theme['type'] === 'adaptive_subtheme') {
        $theme['config'] = $active_theme_config;
        $theme['provider'] = $theme['name'];
        $theme['provider_info'] = $this->getThemeInfo('info');
        $theme['path'] = $theme_path;
      }
      elseif ($theme['type'] === 'adaptive_skin') {
        $theme['config'] = \Drupal::config($theme['base'] . '.settings')->get('settings');
        $theme['config_skin'] = $active_theme_config;
        $theme['provider'] = $theme['base'];
        $theme['provider_info'] = $this->getBaseThemeInfo();
        $theme['path'] = \Drupal::service('extension.list.theme')->getPath($theme['base']);
        $theme['path_skin'] = $theme_path;
      }

      // Extensions and Short codes.
      $theme['extensions'] = self::extensionsEnabled($theme);
      $theme['shortcodes'] = self::shortcodesEnabled($theme);
    }

    return $theme;
  }

}
