<?php

/**
 * @file
 */

use Drupal\Core\Asset\AttachedAssetsInterface;
use Symfony\Component\Yaml\Parser;
use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\Xss;
use Drupal\layout_plugin\Layout;
use Drupal\at_core\Theme\ThemeConfig;
use Drupal\at_core\Layout\LayoutDiscoveryPlugin;
use Drupal\Core\Extension\ThemeExtensionList;

/**
 * Alter attachments (typically assets) to a page before it is rendered.
 *
 * Use this hook when you want to remove or alter attachments on the page, or
 * add attachments to the page that depend on another module's attachments (this
 * hook runs after hook_page_attachments().
 *
 * @param array &$page
 *   An empty renderable array representing the page.
 *
 * @see hook_page_attachments_alter()
 */
function at_core_page_attachments_alter(array &$page) {
  // Config.
  $theme = &drupal_static(__FUNCTION__);
  if (!isset($theme)) {
    $data = new ThemeConfig(\Drupal::theme()->getActiveTheme()->getName());
    $theme = $data->getConfig();
  }
  $config = $theme['config'];

  // Attach required libraries.
  $page['#attached']['library'][] = $theme['provider'] . '/fontfaceobserver';
  $page['#attached']['library'][] = $theme['provider'] . '/base';
  $page['#attached']['library'][] = $theme['provider'] . '/color';

  // Try to avoid running this stuff in admin on Seven.
  if ($theme['provider'] === 'seven') {
    return;
  }

  // Generated files path.
  $generated_files_path = \Drupal::service('extension.list.theme')->getPath($theme['provider']) . '/styles/css/generated';

  // Attach at.settings, we need the ajaxPageState theme name.
  $page['#attached']['library'][] = 'at_core/at.settings';

  // Load at messages JS.
  // TODO this is currently disabled, it's very complex and we need to review the usefulness vs the performance issues.
  //  $page['#attached']['library'][] = 'at_core/at.status_messages';.

  // Attach the JS layout script. We run this to cleanup potentially
  // incorrect layout classes in the markup due to placeholders.
  // See: https://www.drupal.org/node/953034
  // TODO this is currently disabled, it's very complex and we need to review the usefulness vs the performance issues.
  //  if (isset($config['layouts_enable']) && $config['layouts_enable'] === 1) {
  //    // Never run on admin pages, it may cause issues with theme setting pages or module forms.
  //    if (\Drupal::service('router.admin_context')->isAdminRoute(\Drupal::routeMatch()->getRouteObject()) == FALSE) {
  //      $page['#attached']['library'][] = 'at_core/at.layout';
  //    }
  //  }.

  // Attach Drupal Core module dependant libraries.
  // These libraries are declared in your themeName.libraries.yml and we only
  // load if the module is installed.
  $module_libraries = [
    'aggregator',
    'book',
    'comment',
    'contact',
    'forum',
    'language',
    'search',
    'taxonomy',
  ];
  $theme_libraries = \Drupal::service('library.discovery')->getLibrariesByExtension($theme['provider']);
  foreach ($module_libraries as $module_library) {
    if (array_key_exists($module_library, $theme_libraries) && \Drupal::moduleHandler()->moduleExists($module_library) === TRUE) {
      $page['#attached']['library'][] = $theme['provider'] . '/' . $module_library;
    }
  }
  // Base theme libs.
  if (isset($base_themes)) {
    foreach ($base_themes as $base_key => $base_values) {
      $base_theme_libraries = \Drupal::service('library.discovery')->getLibrariesByExtension($base_key);
      foreach ($module_libraries as $module_library) {
        if (array_key_exists($module_library, $base_theme_libraries) && \Drupal::moduleHandler()->moduleExists($module_library) === TRUE) {
          $page['#attached']['library'][] = "$base_key/$module_library";
        }
      }
    }
  }

  // Process extension settings.
  if ($theme['extensions']['is_enabled'] === TRUE) {

    // Mimic is active, load the ckeditor.css.
    if (isset($config['enable_ckeditor']) && $config['enable_ckeditor'] === 1) {
      if (isset($config['mimic_enabled']) && $config['mimic_enabled'] === 1) {
        // BC for older sub-themes that may have info file entires that mess up Mimic.
        $mimic_good_to_go = TRUE;

        // TODO check mimic.
        if (isset($theme['provider']['info']['ckeditor_stylesheets'])) {
          $mimic_good_to_go = FALSE;
        }
        if (isset($theme['provider']['info']['libraries-override']['ckeditor/drupal.ckeditor'])) {
          $mimic_good_to_go = FALSE;
        }
        if ($mimic_good_to_go === TRUE) {
          $page['#attached']['library'][] = $theme['provider'] . '/ckeditor';
        }
      }
    }

    // Fonts.
    if (isset($config['enable_fonts']) && $config['enable_fonts'] === 1) {
      // Fonts generated CSS.
      if (file_exists($generated_files_path . '/fonts.css')) {
        $page['#attached']['library'][] = $theme['provider'] . '/fonts';
      }
      // Google font URL.
      if (isset($config['font_use_google_fonts']) && $config['font_use_google_fonts'] === TRUE) {
        $page['#attached']['library'][] = $theme['provider'] . '/google_fonts';
      }
      // Typekit ID and JS.
      if (isset($config['font_use_typekit']) && $config['font_use_typekit'] === TRUE) {
        $page['#attached']['library'][] = $theme['provider'] . '/typekit_id';
        $page['#attached']['library'][] = 'at_core/at.typekit';
      }
    }

    // Images.
    if (isset($config['enable_images']) && $config['enable_images'] === 1) {
      if (file_exists($generated_files_path . '/image-styles.css')) {
        $page['#attached']['library'][] = $theme['provider'] . '/image_styles';
      }
    }

    // Titles.
    if (isset($config['enable_titles']) && $config['enable_titles'] === 1) {
      if (file_exists($generated_files_path . '/title-styles.css')) {
        $page['#attached']['library'][] = $theme['provider'] . '/title_styles';
      }
    }

    // Mobile blocks.
    if (isset($config['enable_mobile_blocks']) && $config['enable_mobile_blocks'] === 1) {
      if (file_exists($generated_files_path . '/mobile-blocks.css')) {
        $page['#attached']['library'][] = $theme['provider'] . '/mobile_blocks';
      }
    }

    // Custom CSS.
    if (isset($config['enable_custom_css']) && $config['enable_custom_css'] === 1) {
      if (file_exists($generated_files_path . '/custom-css.css')) {
        $page['#attached']['library'][] = $theme['provider'] . '/custom_css';
      }
    }

    // Markup Overrides.
    if (isset($config['enable_markup_overrides']) && $config['enable_markup_overrides'] === 1) {

      // Responsive tables.
      if (isset($config['responsive_tables']) && $config['responsive_tables'] === 1) {
        $page['#attached']['library'][] = $theme['provider'] . '/responsive_tables';
      }

      // Breadcrumbs.
      if (!empty($config['breadcrumb_separator'])) {
        if (file_exists($generated_files_path . '/breadcrumb.css')) {
          $page['#attached']['library'][] = $theme['provider'] . '/breadcrumb';
        }
      }

      // Login block.
      // Just load the login block CSS without the currentUser check.
      if (isset($config['horizontal_login_block']) && $config['horizontal_login_block'] === 1) {
        $page['#attached']['library'][] = $theme['provider'] . '/login_block';
      }
    }

    // Devel assets.
    if (isset($config['enable_devel']) && $config['enable_devel'] === 1) {

      // Attach Windowsize library.
      if (isset($config['show_window_size']) && $config['show_window_size'] === 1) {
        $page['#attached']['library'][] = 'at_core/at.windowsize';
      }

      // Attach devel_layout CSS file.
      if (isset($config['devel_layout']) && $config['devel_layout'] === 1) {
        $page['#attached']['library'][] = 'at_core/at.devel_debug_layout';
      }

      // Attach devel_colorize-regions CSS file.
      if ((isset($config['devel_color_regions']) && $config['devel_color_regions'] === 1) && (isset($config['devel_layout']) && $config['devel_layout'] === 0)) {
        $page['#attached']['library'][] = 'at_core/at.devel_colorize_regions';
      }

      // Attach show_grid.
      if (isset($config['show_grid']) && $config['show_grid'] === 1) {
        $page['#attached']['library'][] = $theme['provider'] . '/show_grid';
      }

      // Attach nuke_toolbar CSS file.
      if (isset($config['nuke_toolbar']) && $config['nuke_toolbar'] === 1) {
        $page['#attached']['library'][] = 'at_core/at.devel_nuke_toolbar';
      }

      // Live Reload.
      if (isset($config['enable_live_reload']) && $config['enable_live_reload'] === 1) {
        $page['#attached']['library'][] = 'at_core/at.livereload';
      }
    }

    // Attach poly-fills to support IE8.
    if (isset($config['enable_legacy_browsers']) && $config['enable_legacy_browsers'] === 1) {
      if (isset($config['legacy_browser_polyfills']) && $config['legacy_browser_polyfills'] === 1) {
        $page['#attached']['library'][] = 'at_core/at.html5shiv';
        $page['#attached']['library'][] = 'at_core/at.respond';
        $page['#attached']['library'][] = 'at_core/at.selectivizr';
      }
    }

    // Load slideshow files.
    if (isset($config['enable_slideshows']) && $config['enable_slideshows'] === 1) {

      // Get config settings and jam them into drupalSettings.
      if (isset($config['slideshow_count']) && $config['slideshow_count'] >= 1) {

        $basic_slider_settings = [
          'animation',
          'direction',
          'smoothheight',
          'slideshowspeed',
          'animationspeed',
          'controlnav',
          'directionnav',
        ];

        $carousel_settings = [
          'as_carousel',
          'itemwidth',
          'itemmargin',
          'minitems',
          'maxitems',
          'move',
        ];

        $advanced_slider_settings = [
          'pauseonaction',
          'pauseonhover',
          'animationloop',
          'reverse',
          'randomize',
        // Flexslider calls this "slideshow".
          'autostart',
          'initdelay',
          'easing',
          'usecss',
          'touch',
          'video',
          'prevtext',
          'nexttext',
          'slideshow_class',
          'selector',
        ];

        $slider_settings = [];
        for ($i = 0; $i < $config['slideshow_count']; $i++) {

          // Set a key.
          $ss_key = Html::cleanCssIdentifier($theme['provider'] . '-slideshow-' . $i);

          if (isset($config['slideshow_' . $i . '_enable']) && $config['slideshow_' . $i . '_enable'] === 1) {

            // Basic settings.
            foreach ($basic_slider_settings as $basic_slider_setting) {
              if (isset($config['slideshow_' . $i . '_' . $basic_slider_setting])) {
                $slider_settings[$ss_key][$basic_slider_setting] = $config['slideshow_' . $i . '_' . $basic_slider_setting];
              }
            }

            // As Carousel.
            if (isset($config['slideshow_' . $i . '_as_carousel']) && $config['slideshow_' . $i . '_as_carousel'] === 1) {
              foreach ($carousel_settings as $carousel_setting) {
                if (isset($config['slideshow_' . $i . '_' . $carousel_setting])) {
                  $slider_settings[$ss_key][$carousel_setting] = $config['slideshow_' . $i . '_' . $carousel_setting];
                }
              }
              // Reset options for carousels, fade won't work and vertical causes issues with Flexslider.
              $slider_settings[$ss_key]['animation'] = 'slide';
              $slider_settings[$ss_key]['direction'] = 'horizonal';
            }

            // Advanced options.
            foreach ($advanced_slider_settings as $advanced_slider_setting) {
              if (isset($config['slideshow_' . $i . '_' . $advanced_slider_setting])) {
                $slider_settings[$ss_key][$advanced_slider_setting] = $config['slideshow_' . $i . '_' . $advanced_slider_setting];
              }
            }
          }
        }

        // Attach JS settings.
        if (!empty($slider_settings)) {
          $page['#attached']['drupalSettings'][$theme['name']]['at_slideshows'] = $slider_settings;
          $page['#attached']['library'][] = 'at_core/at.slideshow_settings';
          $page['#attached']['library'][] = $theme['provider'] . '/slideshow_styles';
        }
      }
    }
  }

  // Short codes. For normal sub theme types yml and CSS comes from the theme,
  // however skin themes can override the settings with the CSS and yml coming
  // from the skins base theme.
  if ($theme['extensions']['is_enabled'] === TRUE) {
    if ($theme['shortcodes']['is_enabled'] === TRUE) {
      $shortcodes_config = $theme[$theme['shortcodes']['config']];
      $shortcodes_yml = $theme['path'] . '/' . $theme['provider'] . '.shortcodes.yml';
      if (file_exists($shortcodes_yml)) {
        $shortcodes_parser = new Parser();
        $shortcodes = $shortcodes_parser->parse(file_get_contents($shortcodes_yml));
        unset($shortcodes['animate']);
        foreach ($shortcodes as $class_type => $class_values) {
          if (isset($shortcodes_config['shortcodes_' . $class_type . '_enable']) && $shortcodes_config['shortcodes_' . $class_type . '_enable'] === 1) {
            $page['#attached']['library'][] = $theme['provider'] . '/shortcodes_' . str_replace('-', '_', $class_type);
          }
        }
      }
      // Animate has its own naming convention, being a vendor library.
      if (isset($shortcodes_config['shortcodes_animate_enable']) && $shortcodes_config['shortcodes_animate_enable'] === 1) {
        $page['#attached']['library'][] = $theme['provider'] . '/animate';
      }
    }
  }

  // Add the responsive menu styles settings.
  $responsive_menu_is_enabled = FALSE;
  $responsive_menu_library_provider = '';
  if ($theme['extensions']['is_enabled'] === TRUE) {
    // TODO deprecated, skins now always use the base themes config.
    // Leave this in place because we might switch back and it's not really
    // hurting being here.
    if ($theme['type'] === 'adaptive_subtheme') {
      if (isset($config['enable_responsive_menus']) && $config['enable_responsive_menus'] === 1) {
        $responsive_menu_is_enabled = TRUE;
        $responsive_menu_config = $config;
        $responsive_menu_library_provider = $theme['provider'];
      }
    }
    elseif ($theme['type'] === 'adaptive_skin') {
      // In skin themes the provider is always the theme base.
      $responsive_menu_library_provider = $theme['base'];
      // Skin theme has enabled responsive menus?
      if (isset($theme['config_skin']['enable_responsive_menus']) && $theme['config_skin']['enable_responsive_menus'] === 1) {
        $responsive_menu_is_enabled = TRUE;
        $responsive_menu_config = $theme['config_skin'];
      }
      // Base theme has enabled responsive menus?
      elseif (isset($config['enable_responsive_menus']) && $config['enable_responsive_menus'] === 1) {
        $responsive_menu_is_enabled = TRUE;
        $responsive_menu_config = $config;
      }
    }

    // Process responsive menu settings.
    if ($responsive_menu_is_enabled === TRUE) {
      $responsive_menu_settings = [];
      $load_accordion = FALSE;
      $click_menus_enabled = FALSE;
      $click_menu_settings['acd_load'] = FALSE;

      if (isset($responsive_menu_config['click_menus_enabled']) && $responsive_menu_config['click_menus_enabled'] === 1) {
        $click_menus_enabled = TRUE;
      }

      // Breakpoint.
      if (isset($responsive_menu_config['responsive_menu_breakpoint'])) {
        $responsive_menu_settings['bp'] = $responsive_menu_config['responsive_menu_breakpoint'];
      }

      // Loop the config settings to find selected menu styles.
      foreach (['default', 'responsive'] as $style) {
        if (isset($responsive_menu_config['responsive_menu_' . $style . '_style'])) {

          // Load the library for each selected menu style.
          $page['#attached']['library'][] = "$responsive_menu_library_provider/responsive_menus_" . $responsive_menu_config['responsive_menu_' . $style . '_style'];

          // Accordion click menu settings.
          if ($click_menus_enabled == TRUE) {
            $click_menu_settings['acd_' . $style] = FALSE;
            if (in_array($responsive_menu_config['responsive_menu_' . $style . '_style'], [
              'vertical',
              'slidedown',
              'offcanvas',
              'overlay'
            ])) {
              $click_menu_settings['acd_' . $style] = TRUE;
              $click_menu_settings['acd_load'] = TRUE;
              $load_accordion = TRUE;
            }
          }
          // Set the menu option for each style.
          $responsive_menu_settings[$style] = 'ms-' . $responsive_menu_config['responsive_menu_' . $style . '_style'];
        }
      }

      // Accordion click menu settings.
      if ($click_menus_enabled == TRUE) {
        $click_menu_settings['acd_both'] = FALSE;
        if ($click_menu_settings['acd_default'] == $click_menu_settings['acd_responsive']) {
          $click_menu_settings['acd_both'] = TRUE;
        }
      }
      $responsive_menu_settings['acd'] = $click_menu_settings;

      // Load Responsive menu dependencies.
      $page['#attached']['library'][] = "$responsive_menu_library_provider/responsive_menus";
      $page['#attached']['library'][] = "at_core/at.responsivemenus";
      if ($load_accordion == TRUE) {
        $page['#attached']['library'][] = "$responsive_menu_library_provider/responsive_menus_accordion";
        $page['#attached']['library'][] = "at_core/at.accordion";
      }
      // $page['#attached']['library'][] = "at_core/at.orientationchangereload";

      // Attach JS settings.
      $page['#attached']['drupalSettings'][$theme['name']]['at_responsivemenus'] = $responsive_menu_settings;
    }
  }

  // Custom CSS file for novice users.
  if (file_exists($theme['path'] . '/styles/css/custom.css')) {
    $page['#attached']['library'][] = $theme['provider'] . '/custom_css_file';
  }

  // Finally we load our jquery.ui overrides.
  if (file_exists($theme['path'] . '/styles/css/components/jquery-ui.css')) {
    $page['#attached']['library'][] = $theme['provider'] . '/jquery_ui';
  }
}

/**
 * Implements hook_js_settings_alter().
 * Perform necessary alterations to the JavaScript settings (drupalSettings).
 *
 * @param array &$settings
 *   An array of all JavaScript settings (drupalSettings) being presented on the
 *   page.
 * @param \Drupal\Core\Asset\AttachedAssetsInterface $assets
 *   The assets attached to the current response.
 *
 * @see \Drupal\Core\Asset\AssetResolver
 */
function at_core_js_settings_alter(array &$settings, AttachedAssetsInterface $assets) {
  $mimic = FALSE;
  $mimic_good_to_go = TRUE;

  // Alter CKEditor JS settings.
  if (\Drupal::moduleHandler()->moduleExists('ckeditor') == TRUE) {

    // Config.
    $theme = &drupal_static(__FUNCTION__);
    if (!isset($theme)) {
      $data = new ThemeConfig(\Drupal::theme()->getActiveTheme()->getName());
      $theme = $data->getConfig();
    }
    $config = $theme['config'];

    if ($theme['extensions']['is_enabled'] === TRUE) {
      if (isset($config['enable_ckeditor']) && $config['enable_ckeditor'] === 1) {
        if (isset($theme['provider']['info']['ckeditor_stylesheets'])) {
          $mimic_good_to_go = FALSE;
        }
        if (isset($theme['provider']['info']['libraries-override']['ckeditor/drupal.ckeditor'])) {
          $mimic_good_to_go = FALSE;
        }
        if ($mimic_good_to_go === TRUE) {
          if (isset($config['mimic_enabled']) && $config['mimic_enabled'] === 1) {
            $mimic = TRUE;
            global $base_url;
            // AT Cores "Mimic" ckeditor skin.
            $editor_skin = $base_url . '/' . \Drupal::service('extension.list.theme')->getPath('at_core') . '/ckeditor/skins/mimic/';
            if (isset($settings['editor']['formats'])) {
              foreach (array_keys($settings['editor']['formats']) as $text_format_id) {
                if ($settings['editor']['formats'][$text_format_id]['editor'] === 'ckeditor') {
                  $settings['editor']['formats'][$text_format_id]['editorSettings']['skin'] = 'mimic, ' . $editor_skin;
                }
              }
            }
          }
        }
      }
    }

    if (isset($settings['editor']['formats'])) {
      // Fontawesome + webfont CSS.
      if (file_exists($theme['path'] . '/styles/css/components/font-awesome.css')) {
        $styles[] = $theme['path'] . '/styles/css/components/font-awesome.css';
      }
      if (file_exists($theme['path'] . '/styles/css/components/webfonts.css')) {
        $styles[] = $theme['path'] . '/styles/css/components/webfonts.css';
      }
      // Component base, image & tables.
      $base_styles = ['base', 'image', 'tables'];
      foreach ($base_styles as $base_style) {
        if (file_exists($theme['path'] . '/styles/css/components/' . $base_style . '.css')) {
          $styles[] = $theme['path'] . '/styles/css/components/' . $base_style . '.css';
        }
      }
      // Generated font & text styles.
      if (!empty($config['generated_files_path'])) {
        // Get the generated files path or the theme provider.
        $generated_files_path = $theme['path'] . '/styles/css/generated';
        if (isset($config['enable_extensions']) && $config['enable_extensions'] === 1) {
          // Google fonts.
          if (isset($config['font_use_google_fonts']) && $config['font_use_google_fonts'] === TRUE) {
            $styles[] = Xss::filter($config['font_google']);
          }
          // Fonts.
          if (isset($config['enable_fonts']) && $config['enable_fonts'] === 1) {
            if (file_exists($generated_files_path . '/fonts.css')) {
              $styles[] = $generated_files_path . '/fonts.css';
            }
          }
          // Title styles.
          if (isset($config['enable_titles']) && $config['enable_titles'] === 1) {
            if (file_exists($generated_files_path . '/title-styles.css')) {
              $styles[] = $generated_files_path . '/title-styles.css';
            }
          }
        }
      }
      foreach ($settings['editor']['formats'] as $format_key => $format_values) {
        if ($mimic === TRUE) {
          // Alter ckeditor-iframe CSS.
          $settings['editor']['formats'][$format_key]['editorSettings']['contentsCss'][] = \Drupal::service('file_url_generator')->generateAbsoluteString($theme['path'] . '/styles/css/components/ckeditor-iframe.css');
          // Remove cores version, it just gets in the way.
          $settings['editor']['formats'][$format_key]['editorSettings']['contentsCss'] = array_merge(
            array_diff($settings['editor']['formats'][$format_key]['editorSettings']['contentsCss'], ['/core/modules/ckeditor/css/ckeditor-iframe.css'])
          );
        }
        // Add styles to settings.
        if (!empty($styles)) {
          foreach ($styles as $stylesheet) {
            $settings['editor']['formats'][$format_key]['editorSettings']['contentsCss'][] = \Drupal::service('file_url_generator')->generateAbsoluteString($stylesheet);
          }
        }
      }
      // Color styles.
      if ($mimic === TRUE) {
        if (\Drupal::moduleHandler()->moduleExists('color') == TRUE) {
          $color_paths = \Drupal::config('color.theme.' . $theme['provider'])->get('stylesheets');
          foreach ($settings['editor']['formats'] as $format_key => $format_values) {
            // Add Color module generated stylesheets.
            if (!empty($color_paths)) {
              // Add the color module saved files.
              foreach ($color_paths as $color_key => $color_path) {
                if (basename($color_path) === 'color.css') {
                  $settings['editor']['formats'][$format_key]['editorSettings']['contentsCss'][] = \Drupal::service('file_url_generator')->generateAbsoluteString($color_path);
                }
              }
            }
            // Else add the themes color component stylesheet.
            else {
              $settings['editor']['formats'][$format_key]['editorSettings']['contentsCss'][] = \Drupal::service('file_url_generator')->generateAbsoluteString($theme['path'] . '/styles/css/components/color.css');
            }
          }
        }
      }
    }
  }
}

/**
 * Implements hook_library_info_alter().
 *
 * @param array $libraries
 *   An associative array of libraries registered by $extension. Keyed by
 *   internal library name and passed by reference.
 * @param string $extension
 *   Can either be 'core' or the machine name of the extension that registered
 *   the libraries.
 */
function at_core_library_info_alter(&$libraries, $extension) {
  // Layouts (Plugin or Discovery).
  if ($extension === 'at_core') {
    // Theme data.
    $data = new ThemeConfig(\Drupal::theme()->getActiveTheme()->getName());
    $theme = $data->getConfig();

    // Replace layout_plugin CSS auto-magically. This avoids the whole issue
    // of absolute paths in libraries-override in the info.yml file.
    // First check if this is a pre 3.x theme.
    $layout_plugin_dir = '';
    if (is_dir($theme['path'] . '/layout/plugin-layout')) {
      $layout_plugin_dir = $theme['path'] . '/layout/plugin-layout/css';
    }
    else {
      if (is_dir($theme['path'] . '/styles/layout_plugin')) {
        $layout_plugin_dir = $theme['path'] . '/styles/layout_plugin/css';
      }
    }
    if (!empty($layout_plugin_dir)) {
      $library_names = LayoutDiscoveryPlugin::libraryNames();
      foreach ($libraries as $lib_key => $library) {
        if (in_array($lib_key, $library_names)) {
          // BC layer so we can namespace libraries in the newer versions.
          $prefix = 'at.';
          if (substr($lib_key, 0, strlen($prefix)) == $prefix) {
            $filename = substr($lib_key, strlen($prefix));
          }
          else {
            $filename = $lib_key;
          }
          $new_css = $layout_plugin_dir . '/' . $filename . '.css';
          if (file_exists($new_css)) {
            $libraries[$lib_key]['css']['theme'] = ['/' . $new_css => []];
          }
        }
      }
    }
  }

  // Blow away Social Media Links (module) Font Awesome library.
  if ($extension == 'social_media_links') {
    if (isset($libraries['fontawesome.component'])) {
      unset($libraries['fontawesome.component']);
    }
  }
}

/**
 * Implements hook_theme_registry_alter().
 *
 * @param $theme_registry
 */
function at_core_theme_registry_alter(&$theme_registry) {
  $theme_hooks = LayoutDiscoveryPlugin::getThemeHooks();

  // Only add preprocess functions if entity exposes theme function, and this
  // layout is provided by at_core.
  if (!empty($theme_hooks)) {
    foreach ($theme_registry as $theme_hook => $info) {
      if (in_array($theme_hook, $theme_hooks) || (!empty($info['base hook']) && in_array($info['base hook'], $theme_hooks))) {
        $theme_registry[$theme_hook]['preprocess functions'][] = 'at_core_preprocess_at_layout';
      }
    }
  }
}

/**
 * Alter the element type information returned from modules.
 * TODO Review/remove after https://www.drupal.org/node/2409083 lands.
 *
 * @param array $info
 *
 * @see \Drupal\Core\Render\ElementInfoManager
 * @see \Drupal\Core\Render\Element\ElementInterface
 */
function at_core_element_info_alter(array &$info) {
  // Remove the html5 shiv. Add it later if legacy browser support is enabled.
  // if (in_array('core/html5shiv', $info['html']['#attached']['library'])) {
  // $info['html']['#attached']['library'] = array_merge(array_diff($info['html']['#attached']['library'], ['core/html5shiv']));
  // }.
}
