<?php

namespace Drupal\at_core\Theme;

/**
 * ThemeInfo declares methods used to return theme info
 * for use in themes, mainly the front end.
 */
class ThemeInfo {

  /**
   * The theme of the theme settings object.
   *
   * @var string
   */
  protected $theme;

  /**
   * The data of the theme settings object.
   *
   * @var array
   */
  protected $data;

  /**
   * Constructs a theme info object.
   *
   * @param string $theme
   */
  public function __construct($theme) {
    $this->theme = $theme;
    $this->data = \Drupal::service('theme_handler')->listInfo();
  }

  /**
   * Returns the theme of this theme info object.
   *
   * @return string
   *   The theme of this theme settings object.
   */
  public function getTheme() {
    return $this->theme;
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
  public function getThemeInfo($key = '') {
    if (empty($key)) {
      return $this->data[$this->theme];
    }
    else {
      return isset($this->data[$this->theme]->$key) ? $this->data[$this->theme]->$key : NULL;
    }
  }

  /**
   * Determine the subtheme type, typically this is either adaptive_subtheme or
   * adaptive_skin (skins are sub-sub themes).
   *
   * @return string
   */
  public function getSubthemeType() {
    $theme_info = $this->getThemeInfo('info');
    return $theme_info['subtheme type'];
  }

  /**
   * Return the base theme name.
   *
   * @return string
   */
  public function getBaseTheme() {
    return $this->getThemeInfo('base_theme');
  }

  /**
   * Return the base theme info.
   *
   * @return array
   */
  public function getBaseThemeInfo() {
    return $this->data[$this->getThemeInfo('base_theme')]->info;
  }

  /**
   * Return the sub themes.
   *
   * @return array
   */
  public function getSubThemes() {
    return $this->getThemeInfo('sub_themes');
  }

  /**
   * Return the sub themes info.
   *
   * @return array
   */
  public function getSubThemesInfo() {
    $sub_themes_info = [];
    $sub_themes = $this->getSubThemes();

    if (isset($sub_themes) && !empty($sub_themes)) {
      foreach ($sub_themes as $machine_name => $label) {
        $sub_themes_info[$machine_name] = $this->data[$machine_name]->info;
      }
    }

    return $sub_themes_info;
  }

  /**
   * Return the sub themes info.
   *
   * @return array
   */
  public function getSubThemesPaths() {
    $sub_themes_paths = [];
    $sub_themes = $this->getSubThemes();
    foreach ($sub_themes as $machine_name => $label) {
      $sub_themes_paths[$machine_name] = $this->data[$machine_name]->getPath();
    }

    return $sub_themes_paths;
  }

  /**
   * Return the theme sub path.
   *
   * @return string
   */
  public function getSubPath() {
    return $this->getThemeInfo('subpath');
  }

}
