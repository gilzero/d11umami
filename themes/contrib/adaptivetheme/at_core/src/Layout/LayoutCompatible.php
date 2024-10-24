<?php

namespace Drupal\at_core\Layout;

use Drupal\at_core\Theme\ThemeInfo;
use Drupal\at_core\Theme\ThemeSettingsInfo;

/**
 *
 */
class LayoutCompatible {

  /**
   * The active theme name.
   */
  protected $theme_name;

  /**
   *
   */
  public function __construct($theme_name) {
    $this->theme_name = $theme_name;
  }

  /**
   * Find and return the most compatible layout.
   *
   * @return mixed
   */
  public function getCompatibleLayout() {
    $layout_compatible_data = [];

    // Caching the data here appears to shave about 50ms off page execution.
    if ($cache = \Drupal::cache()->get($this->theme_name . ':compatiblelayout')) {
      $layout_compatible_data = $cache->data;
    }
    else {
      // Get all base themes for the current theme, any one of these could
      // have a compatible layout.
      $themeSettingsInfo = new ThemeSettingsInfo($this->theme_name);
      $providers = $themeSettingsInfo->baseThemeInfo('base_themes');

      // Unset at_core and classy, these never have a layout.
      unset($providers['stable']);
      unset($providers['classy']);
      unset($providers['at_core']);

      $ThemeInfo = new ThemeInfo($this->theme_name);
      $info_layout = $ThemeInfo->getThemeInfo('info');

      // This is critical to restrict the theme to use only the layout specified
      // in the info file, because there can be many layouts throughout the
      // base_theme tree, but not all might be compatible and a theme can only
      // use one layout at a time.
      if (!empty($info_layout['layout'])) {
        $compatible_layout = $info_layout['layout'];
      }
      else {
        $compatible_layout = '';
        \Drupal::messenger()->addMessage(t('"layout" not declared in info file. Adaptivetheme requires a compatible layout to be declared in your theme info file e.g. "layout: page-layout". Add the declaration, clear the cache and try again.'), 'error');
      }

      // Push the current theme into the array - if it has a layout, use it.
      $providers[$this->theme_name] = $this->theme_name;

      // Define variables.
      $layout_markup = [];
      $layout_css = [];

      // Get the configuration data for layout markup and CSS.
      foreach ($providers as $key => $provider_name) {

        $this_layout[$key] = new Layout($key, $compatible_layout);

        $layout_markup[$key] = $this_layout[$key]->getLayoutMarkup();
        $layout_css[$key] = $this_layout[$key]->getLayoutCSS();

        // Push additional information about the layout, useful later on.
        if (isset($layout_markup[$key]['rows'])) {
          $layout_markup[$key]['layout'] = $compatible_layout;
          $layout_markup[$key]['layout_provider'] = $key;
        }
        if (isset($layout_css[$key]['css'])) {
          $layout_css[$key]['layout'] = $compatible_layout;
          $layout_css[$key]['layout_provider'] = $key;
        }
      }

      // Remove empty values and get the last item values, this is our layout
      // and css configuration.this only really matters if the exact same layout
      // has been duplicated, which might happen if a themer is customizing the
      // layout for a particular sub-theme and does not bother to change the
      // name of the layout (and reflects that change in the themes info file).
      $filter_layout_config = array_filter($layout_markup);
      $filter_css_config = array_filter($layout_css);

      // Split end from array_filter to avoid a strict pass by reference warning.
      $layout_compatible_data['layout_config'] = end($filter_layout_config);
      $layout_compatible_data['css_config'] = end($filter_css_config);

      // Push the layout name into the array for access during form building.
      $layout_compatible_data['layout_name'] = $compatible_layout;

      if (!empty($layout_compatible_data)) {
        \Drupal::cache()->set($this->theme_name . ':compatiblelayout', $layout_compatible_data);
      }
    }

    return $layout_compatible_data;
  }

}
