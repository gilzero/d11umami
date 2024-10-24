<?php

namespace Drupal\at_core\Theme;

/**
 * ThemeSettingsInfo declares methods used to return theme info for use in
 * theme-settings.php. Note the constructor calls system_rebuild_theme_data()
 * which is not statically cached therefor only used in the backend, however
 * it always returns fresh data.
 */
class ThemeSettingsInfo extends ThemeInfo {

  /**
   * Constructs a theme info object.
   *
   * @param string $theme
   */
  public function __construct($theme = 'at_core') {
    $this->theme = $theme;
    $this->data = \Drupal::service('theme_handler')->rebuildThemeData();
  }

  /**
   * Return list of base theme options.
   * Looks for all themes with a base theme value of 'at_core' and returns
   * the list. This means you cannot sub-theme a "skin" type sub-theme.
   *
   * @return array
   */
  public function baseThemeOptions() {
    $base_themes = [];
    foreach ($this->data as $machine_name => $info) {
      foreach ($info as $info_key => $info_values) {
        if ($info_key == 'base_themes') {
          foreach ($info_values as $value_key => $value_values) {
            if ($value_key == 'at_core') {
              $base_themes[$machine_name] = $machine_name;
            }
          }
        }
      }
    }
    // These are just generator "templates, not to be used directly.
    unset($base_themes['at_standard']);
    unset($base_themes['at_minimal']);
    unset($base_themes['at_skin']);
    unset($base_themes['at_starterkit']);
    unset($base_themes['at_generator']);
    unset($base_themes['THEMENAME']);

    return $base_themes;
  }

  /**
   * Returns either the whole info array for $this theme or just one key
   * if the $key parameter is set.
   *
   * @param string $key
   *   A string that maps to a key within the theme settings data.
   *
   * @return mixed
   *   The info data that was requested.
   */
  public function baseThemeInfo($key = '') {
    if (empty($key)) {
      return $this->data[$this->theme];
    }
    else {
      return isset($this->data[$this->theme]->$key) ? $this->data[$this->theme]->$key : NULL;
    }
  }

  /**
   * Return a list of base themes and their sub-themes.
   * This is borrowed from drupal_find_theme_templates().
   *
   * @return array Arrays of sub themes keyed by base theme.
   */
  public function findThemePaths() {
    $theme_paths = [];

    foreach ($this->data as $theme_info) {
      if (!empty($theme_info->base_theme)) {
        $theme_paths[$theme_info->base_theme][$theme_info->name] = dirname($theme_info->filename);
      }
    }

    foreach ($theme_paths as $basetheme => $subthemes) {
      foreach ($subthemes as $subtheme => $subtheme_path) {
        if (isset($theme_paths[$subtheme])) {
          $theme_paths[$basetheme] = array_merge($theme_paths[$basetheme], $theme_paths[$subtheme]);
        }
      }
    }

    return $theme_paths;
  }

  /**
   * Check if a theme name already exists.
   * Looks in the list of themes to see if a theme name already exists, if so
   * returns TRUE. This is the callback method for the form field machine_name
   * as used in theme-settings.php for the theme Generator.
   *
   * @param $machine_name
   *
   * @return bool
   */
  public function themeNameExists($machine_name) {
    $result = FALSE;
    if (array_key_exists($machine_name, $this->data)) {
      $result = TRUE;
    }

    return $result;
  }

}
