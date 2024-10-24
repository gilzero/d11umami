<?php

/**
 * @file
 */

use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\Core\Template\Attribute;
use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\Tags;
use Drupal\at_core\Layout\LayoutLoad;
use Drupal\at_core\Theme\ThemeConfig;
use Drupal\image\Entity\ImageStyle;
use Drupal\block\BlockRepositoryInterface;

/**
 * Preprocess variables for all templates.
 *
 * @param $variables
 */
function at_core_preprocess(&$variables) {
  // Add theme variables. template_preprocess is hit many times so we statically
  // cache $theme.
  $theme = &drupal_static(__FUNCTION__);
  if (!isset($theme)) {
    $data = new ThemeConfig(\Drupal::theme()->getActiveTheme()->getName());
    $theme = $data->getConfig();
  }
  $variables['theme'] = $theme;

  // Set global is_front.
  try {
    $variables['is_front'] = \Drupal::service('path.matcher')->isFrontPage();
  }
  catch (Exception $e) {
    $variables['is_front'] = FALSE;
  }
  // Ensure the cache varies correctly (new in Drupal 8.3).
  $variables['#cache']['contexts'][] = 'url.path.is_front';
}

/**
 * Preprocess variables for html templates.
 *
 * @param $variables
 */
function at_core_preprocess_html(&$variables) {
  // Get the right config for this theme type.
  // $themeConfig = new ThemeConfig($active_theme_name);
  $config = $variables['theme']['config'];

  // Set active theme name.
  $active_theme_name = $variables['theme']['name'];

  // Reset $variables['directory'] if this is a skin theme.
  if ($variables['theme']['type'] === 'adaptive_skin' && !empty($variables['theme']['base'])) {
    $variables['directory'] = $variables['theme']['path'];
  }

  // Set the skip navigation target ID.
  $variables['skip_link_target'] = '#block-' . Html::cleanCssIdentifier($active_theme_name) . '-content';

  // Defaults for Appearance setting variables.
  $variables['touch_icons'] = [];
  // BC, deprecated.
  $variables['googlefonts_url'] = '';
  $variables['typekit_id'] = '';

  // Initialize prefetch variables.
  $variables['google_dns_prefetch'] = FALSE;
  $variables['false_dns_prefetch'] = FALSE;

  // We use this to set body classes based in the URI.
  $request = \Drupal::request();

  // Set a class for query pages, e,g, pager page 1, page 2 etc.
  $request_uri = parse_url($request->getRequestUri());
  if (isset($request_uri['query'])) {
    $query = isset($request_uri['query']) ? Html::cleanCssIdentifier(ltrim($request_uri['query'], '/')) : NULL;
    $variables['path_info']['query'] = (strlen($query) > 25) ? substr($query, 0, 25) : $query;
  }
  else {
    $variables['path_info']['query'] = NULL;
  }

  // We use this to replicate Drupal 7's path-[root_path]-[id] type classes.
  $variables['path_info']['args'] = FALSE;
  $path = $request->getPathInfo();
  $path_args = explode('/', $path);
  if (count($path_args) >= 3) {
    $variables['path_info']['args'] = Html::cleanCssIdentifier(ltrim($path, '/'));
  }

  // Extensions.
  if (isset($config['enable_extensions']) && $config['enable_extensions'] === 1) {

    // Skip navigation target setting.
    if (isset($config['skip_link_target'])) {
      $variables['skip_link_target'] = '#' . Html::cleanCssIdentifier($config['skip_link_target']);
    }

    // Apple touch icons - low, medium and high (see the Apple docs).
    if (isset($config['enable_touch_icons']) && $config['enable_touch_icons'] === 1) {
      $icons = [];
      $touch_rel = 'apple-touch-icon';

      if (isset($config['apple_touch_icon_precomposed']) && $config['apple_touch_icon_precomposed'] === 1) {
        $touch_rel = 'apple-touch-icon-precomposed';
      }

      // For non-Retina iPhone, iPod Touch, and Android 2.1+ devices.
      if (isset($config['icon_path_default']) && !empty($config['icon_path_default'])) {
        $icon_path_default = $variables['theme']['path'] . '/' . Html::escape($config['icon_path_default']);
        if (file_exists($icon_path_default) === TRUE) {
          $icons['touch_icon_iphone_1x'] = [
            'href' => \Drupal::service('file_url_generator')->generateAbsoluteString($icon_path_default),
            'size' => '60x60',
            'rel' => $touch_rel,
          ];
        }
      }
      // For the iPad mini and the first- and second-generation iPad (@1× display) on iOS ≥ 7 (76x76).
      if (isset($config['apple_touch_icon_path_ipad']) && !empty($config['apple_touch_icon_path_ipad'])) {
        $icon_path_ipad = $variables['theme']['path'] . '/' . Html::escape($config['apple_touch_icon_path_ipad']);
        if (file_exists($icon_path_ipad) === TRUE) {
          $icons['touch_icon_ipad_1x'] = [
            'href' => \Drupal::service('file_url_generator')->generateAbsoluteString($icon_path_ipad),
            'size' => '76x76',
            'rel' => $touch_rel,
          ];
        }
      }
      // For iPhone with @2× display running iOS ≥ 7 (120x120).
      if (isset($config['apple_touch_icon_path_iphone_retina']) && !empty($config['apple_touch_icon_path_iphone_retina'])) {
        $icon_path_iphone_retina = $variables['theme']['path'] . '/' . Html::escape($config['apple_touch_icon_path_iphone_retina']);
        if (file_exists($icon_path_iphone_retina) === TRUE) {
          $icons['touch_icon_iphone_2x'] = [
            'href' => \Drupal::service('file_url_generator')->generateAbsoluteString($icon_path_iphone_retina),
            'size' => '120x120',
            'rel' => $touch_rel,
          ];
        }
      }
      // For iPad with @2× display running iOS ≥ 7 (152x152)
      if (isset($config['apple_touch_icon_path_ipad_retina']) && !empty($config['apple_touch_icon_path_ipad_retina'])) {
        $icon_path_ipad_retina = $variables['theme']['path'] . '/' . Html::escape($config['apple_touch_icon_path_ipad_retina']);
        if (file_exists($icon_path_ipad_retina) === TRUE) {
          $icons['touch_icon_ipad_2x'] = [
            'href' => \Drupal::service('file_url_generator')->generateAbsoluteString($icon_path_ipad_retina),
            'size' => '152x152',
            'rel' => $touch_rel,
          ];
        }
      }
      // For iPhone 6 Plus with @3× display (180x180)
      if (isset($config['apple_touch_icon_path_ipad_retina_3x']) && !empty($config['apple_touch_icon_path_ipad_retina_3x'])) {
        $icon_path_ipad_retina_3x = $variables['theme']['path'] . '/' . Html::escape($config['apple_touch_icon_path_ipad_retina_3x']);
        if (file_exists($icon_path_ipad_retina_3x) === TRUE) {
          $icons['touch_icon_iphone_3x'] = [
            'href' => \Drupal::service('file_url_generator')->generateAbsoluteString($icon_path_ipad_retina_3x),
            'size' => '180x180',
            'rel' => $touch_rel,
          ];
        }
      }
      // For Chrome on Android (192x192)
      if (isset($config['apple_touch_icon_path_chrome_android']) && !empty($config['apple_touch_icon_path_chrome_android'])) {
        $icon_path_chrome_android = $variables['theme']['path'] . '/' . Html::escape($config['apple_touch_icon_path_chrome_android']);
        if (file_exists($icon_path_chrome_android) === TRUE) {
          $icons['touch_icon_chrome_android'] = [
            'href' => \Drupal::service('file_url_generator')->generateAbsoluteString($icon_path_chrome_android),
            'size' => '192x192',
            'rel' => 'icon',
          ];
        }
      }
      // Inline template for touch icons.
      foreach ($icons as $touch_item => $touch_item_values) {
        $variables['touch_icons'][$touch_item] = [
          '#type'     => 'inline_template',
          '#template' => '<link href="{{ href }}" rel="{{ rel }}" sizes="{{ size }}" />',
          '#context'  => [
            'href' => $touch_item_values['href'],
            'rel'  => $touch_item_values['rel'],
            'size' => $touch_item_values['size'],
          ],
        ];
      }
    }

    // Prefetch Font provider DNS.
    if (isset($config['enable_fonts']) && $config['enable_fonts'] === 1) {
      if (isset($config['font_use_google_fonts']) && $config['font_use_google_fonts'] === 1) {
        $variables['google_dns_prefetch'] = TRUE;
      }
      if (isset($config['font_use_typekit']) && $config['font_use_typekit'] === 1) {
        $variables['typekit_dns_prefetch'] = TRUE;
      }
    }
  }

  // Short codes.
  if ($variables['theme']['extensions']['is_enabled'] === TRUE) {
    if ($variables['theme']['shortcodes']['is_enabled'] === TRUE) {
      $shortcodes_config = $variables['theme'][$variables['theme']['shortcodes']['config']];
      if (!empty($shortcodes_config['page_classes_body'])) {
        $shortcodes = Tags::explode($shortcodes_config['page_classes_body']);
        foreach ($shortcodes as $class) {
          $variables['attributes']['class'][] = Html::cleanCssIdentifier($class);
        }
      }
    }
  }

  // Add all breakpoints to drupalSettings (key:media query).
  $breakpoints_module = \Drupal::moduleHandler()->moduleExists('breakpoint');
  if ($breakpoints_module == TRUE) {
    $breakpoints_array = [];
    $breakpoint_groups[] = isset($config['breakpoint_group_layout']) ? $config['breakpoint_group_layout'] : 'at_core.simple';
    // Push 'at_core.simple' onto the array, the Layout Plugin layouts always
    // require these breakpoints to be set.
    if ($breakpoint_groups !== 'at_core.simple') {
      $breakpoint_groups[] = 'at_core.simple';
    }
    foreach ($breakpoint_groups as $breakpoint_group) {
      $breakpoints = \Drupal::service('breakpoint.manager')->getBreakpointsByGroup($breakpoint_group);
      foreach ($breakpoints as $breakpoint_key => $breakpoint_values) {
        $breakpoint_label = strtolower($breakpoint_values->getLabel()->getUntranslatedString());
        $clean_breakpoint_key = str_replace('.', '_', $breakpoint_key);
        $breakpoints_array[$clean_breakpoint_key]['breakpoint'] = $breakpoint_label;
        $breakpoints_array[$clean_breakpoint_key]['mediaquery'] = $breakpoint_values->getMediaQuery();
      }
      $variables['#attached']['drupalSettings'][$variables['theme']['name']]['at_breakpoints'] = $breakpoints_array;
    }
  }
  else {
    \Drupal::messenger()->addMessage(t('This theme requires the <b>Breakpoint module</b> to be installed. Go to the <a href="@extendpage" target="_blank">Modules</a> page and install Breakpoint.', ['@extendpage' => base_path() . 'admin/modules']), 'error');
  }

  // Layout type class & CSS for BC and other usage.
  if (isset($variables['theme']['provider_info']['layout'])) {
    $variables['attributes']['class'][] = 'layout-type--' . Html::cleanCssIdentifier($variables['theme']['provider_info']['layout']);

    // TODO remove BC at some time in the future when rc2 usage is dead.
    if ($variables['theme']['provider_info']['layout'] === 'flex-builder' && $variables['theme']['provider_info']['base theme original'] === '8.x-1.0-rc2') {
      $variables['#attached']['library'][] = 'at_core/at.bc';
    }
  }
}

/**
 * Preprocess variables for page templates.
 *
 * @param $variables
 */
function at_core_preprocess_page(&$variables) {
  // Config.
  $config = $variables['theme']['config'];

  // Page classes.
  $variables['attributes']['class'][] = 'page';

  // Set attribution.
  $attribution_url = Url::fromUri('//drupal.org/project/at_theme',
    $options = [
      'attributes' => [
        'class' => ['attribution__link'],
        'target' => '_blank',
      ],
      'absolute' => TRUE,
    ]
  );
  $variables['attribution'] = [
    '#type' => 'inline_template',
    '#template' => '<div data-at-row="attribution" class="l-attribution l-row"><div class="l-pr attribution"><div class="l-rw">{{ attribution }}</div></div></div>',
    '#context' => [
      'attribution' => Link::fromTextAndUrl(t('Design by Adaptive Theme'), $attribution_url)->toString(),
    ],
  ];

  // Disallow access if attribution link is toggled off.
  if (isset($config['attribution_toggle']) && $config['attribution_toggle'] === 0) {
    $variables['attribution']['#access'] = FALSE;
  }

  // Short codes.
  if ($variables['theme']['extensions']['is_enabled'] === TRUE) {
    if ($variables['theme']['shortcodes']['is_enabled'] === TRUE) {
      $shortcodes_config = $variables['theme'][$variables['theme']['shortcodes']['config']];
      if (!empty($shortcodes_config['page_classes_page'])) {
        $shortcodes = Tags::explode($shortcodes_config['page_classes_page']);
        foreach ($shortcodes as $class) {
          $variables['attributes']['class'][] = Html::cleanCssIdentifier($class);
        }
      }
    }
  }

  // --- Layout -------------------------------------------------------------- //
  // Active theme name.
  $active_theme_name = $variables['theme']['name'];

  // BC for pre RC2 themes:
  // - allow the main content to render in at_generator so users can login if
  // they set it as default and blew their site up.
  // TODO remove at some stage.
  if ($active_theme_name === 'at_generator') {
    $variables['main']['has_regions'] = TRUE;
    // Return early to avoid notices and errors.
    return;
  }

  if (isset($config['layouts_enable']) && $config['layouts_enable'] === 1) {
    // Add dynamic classes to each region wrapper (.regions).
    // This adds two classes to every wrapper:
    //  - "arc--[n]" active region count in this row, e.g. "arc--2".
    //  - "hr--[n-n]" has regions, by source order, e.g. "hr--1-3".
    $variables['attributes']['class'][] = 'js-layout';
    // $regions = system_region_list($active_theme_name, REGIONS_VISIBLE);
    $regions = system_region_list($active_theme_name, BlockRepositoryInterface::REGIONS_VISIBLE);
    $active_regions = [];
    // Do a basic check for emptiness, however we can never be sure because of
    // placeholders: https://www.drupal.org/node/953034
    // After the page loads at.layout.js runs to fix incorrect layout classes.
    // Note the old render early code has been removed, i.e.:
    // if ($region = \Drupal::service('renderer')->render($variables['page'][$region_name])) {}.
    foreach ($regions as $region_name => $region_label) {
      if (!empty($variables['page'][$region_name])) {
        $active_regions[] = $region_name;
      }
    }
    if (!empty($active_regions)) {
      $layout_load = new LayoutLoad($active_theme_name, $active_regions);
      if ($row_attributes = $layout_load->rowAttributes()) {
        // Append to $variables for backwards compatibility.
        $variables += $row_attributes;
      }

      // Rows.
      foreach ($active_regions as $key => $region) {
        // Get the row key for each region.

        // TODO shortcodes on method for rows.
        $row_key = $layout_load->regionAttributes($region);
        if (!empty($row_key)) {
          // Prepare variables for each row template.
          $row_regions[$row_key]['#row'] = $row_key;
          $row_regions[$row_key][$region] = $variables['page'][$region];
          $row_regions[$row_key]['#row_attributes'] = $row_attributes[$row_key]['row_attributes'];
          $row_regions[$row_key]['#wrapper_attributes'] = $row_attributes[$row_key]['wrapper_attributes'];
          $row_regions[$row_key]['#container_attributes'] = $row_attributes[$row_key]['container_attributes'];
          // Call the row template and pass in the processed variables.
          $variables['rows'][$row_key] = [
            '#theme' => 'row',
            '#regions' => $row_regions[$row_key],
          ];
        }
      }
    }
  }
}

/**
 * Preprocess variables for row templates.
 *
 * @param $variables
 */
function at_core_preprocess_row(&$variables) {
  // Easy printing variable for the row name.
  $variables['row_name'] = $variables['regions']['#row'];

  // Set easy to print variables for attributes.
  $variables['row_attributes'] = $variables['regions']['#row_attributes'];
  $variables['wrapper_attributes'] = $variables['regions']['#wrapper_attributes'];
  $variables['container_attributes'] = $variables['regions']['#container_attributes'];

  // Initialize variables for inserting content/markup before or after a row.
  $variables['row_prefix'] = [];
  $variables['row_suffix'] = [];
}

/**
 * Preprocess variables for region templates.
 *
 * @param $variables
 */
function at_core_preprocess_region(&$variables) {
  // Set source order data attribute, used to set the layout classes.
  $active_theme_name = $variables['theme']['name'];
  $layout_loaded = new LayoutLoad($active_theme_name, $active_regions = NULL);
  $region_source_order = $layout_loaded->regionSourceOrder($variables['region']);
  $variables['attributes']['data-at-region'] = $region_source_order[$variables['region']];

  // Set variable for the row this region belongs to.
  $region_row = $layout_loaded->regionAttributes($variables['region']);
  if (!empty($region_row)) {
    $variables['region_row'] = $region_row;
  }

  // Set wrapper element. Required for BC. Deprecated.
  $variables['html_element'] = 'div';

  $i = 0;
  $block_count[$variables['region']] = 0;
  foreach ($variables['elements'] as $element => $values) {
    if (isset($values) && !empty($values) && is_array($values)) {
      foreach ($values as $vk => $vv) {
        if ($vk == '#markup') {
          $block_count[$variables['region']] = $i++;
        }
      }
    }
  }
  if (!empty($block_count[$variables['region']])) {
    $variables['attributes']['data-at-block-count'] = $block_count[$variables['region']];
  }

  // Short codes.
  if ($variables['theme']['extensions']['is_enabled'] === TRUE) {
    if ($variables['theme']['shortcodes']['is_enabled'] === TRUE) {
      $shortcodes_config = $variables['theme'][$variables['theme']['shortcodes']['config']];
      if (!empty($shortcodes_config['page_classes_region_' . $variables['region']])) {
        $shortcode_tags = Tags::explode($shortcodes_config['page_classes_region_' . $variables['region']]);
        foreach ($shortcode_tags as $class) {
          $variables['attributes']['class'][] = Html::cleanCssIdentifier($class);
        }
      }
    }
  }
}

/**
 * Preprocess variables for node templates.
 *
 * @param $variables
 */
function at_core_preprocess_node(&$variables) {
  // Header and Footer attributes.
  $variables['header_attributes'] = new Attribute(['class' => []]);
  $variables['footer_attributes'] = new Attribute(['class' => []]);

  // SEE https://drupal.org/node/2004252 or a follow up issue.
  if ($variables['display_submitted']) {

    // Add a class to the header if submitted is active, so we can theme dynamically.
    $variables['header_attributes']['class'][] = 'node__header--has-meta';

    // Initialize new attributes arrays.
    $variables['meta_attributes'] = new Attribute(['class' => []]);
    $variables['meta_attributes']['class'][] = 'node__meta';
    $variables['submitted_attributes'] = new Attribute(['class' => []]);
    $variables['submitted_attributes']['class'][] = 'node__submitted';

    // Add a class if author picture is printing.
    $author_picture = \Drupal::service('renderer')->render($variables['author_picture']);
    if (!empty($author_picture)) {
      // If twig debug is on $author_picture is not empty.
      $twig_debug = \Drupal::service('twig')->isDebug();
      if ($twig_debug == TRUE) {
        $ap = _at_core_cleanup_twig_debug_output($author_picture);
      }
      else {
        $ap = $author_picture;
      }
      if (!empty($ap)) {
        $variables['meta_attributes']['class'][] = 'node__meta--has-author-picture';
      }
    }
  }

  // Short codes.
  if ($variables['theme']['extensions']['is_enabled'] === TRUE) {
    if ($variables['theme']['shortcodes']['is_enabled'] === TRUE) {
      $shortcodes_config = $variables['theme'][$variables['theme']['shortcodes']['config']];
      $node_type = $variables['node']->getType() ?: '';
      if (!empty($node_type)) {
        if (!empty($shortcodes_config['nodetype_classes_' . $node_type])) {
          $shortcodes = Tags::explode($shortcodes_config['nodetype_classes_' . $node_type]);
          foreach ($shortcodes as $class) {
            $variables['attributes']['class'][] = Html::cleanCssIdentifier($class);
          }
        }
      }
    }
  }
}

/**
 * Preprocess variables for comment templates.
 *
 * @param $variables
 */
function at_core_preprocess_comment(&$variables) {
  // View mode.
  if (isset($variables['elements']['#view_mode']) && !empty($variables['elements']['#view_mode'])) {
    $variables['view_mode'] = $variables['elements']['#view_mode'];
  }

  // Initialize new attributes arrays.
  $variables['meta_attributes'] = new Attribute(['class' => []]);
  $variables['meta_attributes']['class'][] = 'comment__meta';

  $variables['submitted_attributes'] = new Attribute(['class' => []]);
  $variables['submitted_attributes']['class'][] = 'comment__submitted';

  // Add a class if user picture is printing. Render early.
  $user_picture = \Drupal::service('renderer')->render($variables['user_picture']);
  if (!empty($user_picture)) {

    // If twig debug is on $author_picture is not empty.
    $twig_debug = \Drupal::service('twig')->isDebug();
    if ($twig_debug == TRUE) {
      $up = _at_core_cleanup_twig_debug_output($user_picture);
    }
    else {
      $up = $user_picture;
    }
    if (!empty($up)) {
      $variables['meta_attributes']['class'][] = 'comment__meta--has-user-picture';
    }
  }

  // Use permalink URI as the title link.
  $comment = $variables['elements']['#comment'];
  if (!isset($comment->in_preview)) {
    $uri = $comment->permalink();
    $attributes = $uri->getOption('attributes') ?: [];
    $attributes += ['class' => ['permalink'], 'rel' => 'bookmark'];
    $uri->setOption('attributes', $attributes);
    $variables['title'] = Link::fromTextAndUrl($comment->getSubject(), $uri);
  }

  // Hide comment titles.
  $variables['title_visibility'] = TRUE;
  if ($variables['theme']['extensions']['is_enabled'] === TRUE) {
    $config = $variables['theme']['config'];
    if (isset($config['enable_markup_overrides']) && $config['enable_markup_overrides'] === 1) {
      if (isset($config['comments_hide_title']) && $config['comments_hide_title'] === 1) {
        $variables['title_visibility'] = FALSE;
      }
    }
  }

  // Short codes.
  if ($variables['theme']['extensions']['is_enabled'] === TRUE) {
    if ($variables['theme']['shortcodes']['is_enabled'] === TRUE) {
      $shortcodes_config = $variables['theme'][$variables['theme']['shortcodes']['config']];
      $content_type = $variables['comment']->bundle() ?: '';
      if (!empty($content_type)) {
        if (!empty($shortcodes_config['commenttype_classes_' . $content_type])) {
          $shortcodes = Tags::explode($shortcodes_config['commenttype_classes_' . $content_type]);
          foreach ($shortcodes as $class) {
            $variables['attributes']['class'][] = Html::cleanCssIdentifier($class);
          }
        }
      }
    }
  }
}

/**
 * Preprocess variables for block templates.
 *
 * @param $variables
 */
function at_core_preprocess_block(&$variables) {
  // Inject a block layout class.
  $variables['attributes']['class'][] = 'l-bl';

  // Variable for custom block bundle.
  if (isset($variables['elements']['content']['#block_content'])) {
    $variables['bundle'] = $variables['elements']['content']['#block_content']->bundle();
    // Variable for custom block view mode.
    if (isset($variables['elements']['#configuration']['view_mode'])) {
      $variables['view_mode'] = $variables['elements']['#configuration']['view_mode'];
    }
  }

  // Plugin ID variables for block class and shortcodes.
  if (isset($variables['base_plugin_id']) && !empty($variables['base_plugin_id'])) {
    $variables['plugin_id_clean'] = $variables['base_plugin_id'];
    if (isset($variables['derivative_plugin_id']) && !empty($variables['derivative_plugin_id'])) {
      $variables['plugin_id_clean'] = $variables['base_plugin_id'] . '_' . $variables['derivative_plugin_id'];
    }
  }

  // Login block.
  if (\Drupal::currentUser()->isAnonymous()) {
    if ($variables['base_plugin_id'] === 'user_login_block') {
      $variables['#attached']['library'][] = $variables['theme']['provider'] . '/login_block';
    }
  }

  // Extension settings.
  if ($variables['theme']['extensions']['is_enabled'] === TRUE) {
    $config = $variables['theme']['config'];

    // Short codes.
    if ($variables['theme']['shortcodes']['is_enabled'] === TRUE) {
      $shortcodes_config = $variables['theme'][$variables['theme']['shortcodes']['config']];
      if (isset($variables['content'])) {
        if (isset($shortcodes_config['block_classes_' . $variables['plugin_id_clean']]) && !empty($shortcodes_config['block_classes_' . $variables['plugin_id_clean']])) {
          $shortcodes = Tags::explode($shortcodes_config['block_classes_' . $variables['plugin_id_clean']]);
          foreach ($shortcodes as $class) {
            $variables['attributes']['class'][] = Html::cleanCssIdentifier($class);
          }
        }
      }
    }

    // Add bool for click menus enabled.
    // Include support for Menu Block and System Block modules.
    $variables['click_menus_enabled'] = FALSE;
    if ($variables['base_plugin_id'] === 'system_menu_block' || $variables['base_plugin_id'] === 'menu_block') {
      if (isset($config['click_menus_enabled']) && $config['click_menus_enabled'] === 1) {
        $variables['click_menus_enabled'] = TRUE;
      }
    }

    // Markup overrides.
    if (isset($config['enable_markup_overrides']) && $config['enable_markup_overrides'] === 1) {

      // Use PNG logo in branding block.
      if ($variables['base_plugin_id'] === 'system_branding_block') {
        if (isset($config['png_logo']) && $config['png_logo'] === 1) {
          $variables['site_logo'] = '';
          if ($variables['content']['site_logo']['#access'] && $variables['content']['site_logo']['#uri']) {
            $variables['site_logo'] = str_replace('.svg', '.png', $variables['content']['site_logo']['#uri']);
          }
        }
      }

      // Remove login block links.
      if ($variables['base_plugin_id'] === 'user_login_block') {
        if ((isset($config['login_block_remove_links']) && $config['login_block_remove_links'] === 1) || (isset($config['horizontal_login_block']) && $config['horizontal_login_block'] === 1)) {
          unset($variables['content']['user_links']);
          // Add class for horizontal login.
          if (isset($config['horizontal_login_block']) && $config['horizontal_login_block'] === 1) {
            $variables['attributes']['class'][] = 'is-horizontal-login-block';
          }
        }
      }
    }
  }

  // Views CSS Classes set on the block for advanced theming of views blocks.
  // Todo - keep sharp eye on notices and warnings etc. Need to test with a wide
  // variety of views blocks.
  if ($variables['base_plugin_id'] === 'views_block') {
    if (isset($variables['content']['#view']->current_display) && !empty($variables['content']['#view']->current_display)) {
      $css_class_array = [];
      $current_display = $variables['content']['#view']->current_display;
      $default_display_storage = $variables['content']['#view']->storage->getDisplay('default');
      $current_display_storage = $variables['content']['#view']->storage->getDisplay($current_display);
      if (isset($current_display_storage['display_options']['css_class'])) {
        $css_class_array = explode(' ', $current_display_storage['display_options']['css_class']);
      }
      elseif (isset($default_display_storage['display_options']['css_class'])) {
        $css_class_array = explode(' ', $default_display_storage['display_options']['css_class']);
      }
      if (!empty($css_class_array)) {
        foreach ($css_class_array as $css_class) {
          $variables['attributes']['class'][] = 'block-views-css-class--' . $css_class;
        }
      }
    }
  }
}

/**
 * Preprocess variables for block templates.
 *
 * @param $variables
 */
// Function at_core_preprocess_menu(&$variables) {
//  kint($variables);
// }

/**
 * Preprocess variables for field templates.
 *
 * @param $variables
 */
function at_core_preprocess_field(&$variables) {
  // Field formatter variable.
  if (isset($variables['element']['#formatter'])) {
    $variables['field_formatter'] = $variables['element']['#formatter'];
  }
}

/**
 * Preprocess variables for entity reference field templates.
 *
 * @param $variables
 */
function at_core_preprocess_field__entity_reference(&$variables) {
  $variables['field_entity_type'] = NULL;
  if (isset($variables['element']['#items']) && is_object($variables['element']['#items'])) {
    $variables['field_entity_type'] = $variables['element']['#items']->getSetting('target_type');
  }
}

/**
 * Preprocess variables for image field templates.
 *
 * @param $variables
 */
function at_core_preprocess_field__image(&$variables) {
  // Extension settings.
  if ($variables['theme']['extensions']['is_enabled'] === TRUE) {
    $config = $variables['theme']['config'];
    if (isset($config['enable_images']) && $config['enable_images'] === 1) {

      $entity_type = $variables['element']['#entity_type'];
      $bundle = $variables['element']['#bundle'];
      $view_mode = $variables['element']['#view_mode'];

      // Reset items array to first item only. This causes only the first image
      // to be shown, e.g. in teaser view mode.
      if (isset($config['image_count_' . $bundle . '_' . $entity_type . '_' . $view_mode]) && $config['image_count_' . $bundle . '_' . $entity_type . '_' . $view_mode] === 1) {
        $item = reset($variables['items']);
        $variables['items'] = [$item];
      }

      // Captions.
      if (isset($config['image_captions_' . $bundle . '_' . $entity_type . '_' . $view_mode]) && $config['image_captions_' . $bundle . '_' . $entity_type . '_' . $view_mode] === 1) {
        foreach ($variables['items'] as $delta => $item) {
          if (isset($item['content']['#item'])) {
            $values = $item['content']['#item']->getValue();
            if (!empty($values['title'])) {
              $variables['items'][$delta]['caption'] = [
                'show' => TRUE,
                'title' => $values['title'],
              ];
              if (isset($item['content']['#image_style']) && !empty($item['content']['#image_style'])) {
                $image_style_width = 'auto';
                $image_style_load_configuration = ImageStyle::load($item['content']['#image_style'])
                  ->getEffects()
                  ->getConfiguration();
                foreach ($image_style_load_configuration as $uuid => $image_config_data) {
                  if (isset($image_config_data['data']['width']) && !empty($image_config_data['data']['width'])) {
                    $image_style_width = $image_config_data['data']['width'];
                  }
                }
                $variables['items'][$delta]['caption']['width'] = $image_style_width;
              }
              // Raw image with no image style and for responsive images.
              elseif (isset($values['width']) && !empty($values['width'])) {
                $variables['items'][$delta]['caption']['width'] = $values['width'];
              }
            }
            else {
              $variables['items'][$delta]['caption'] = [
                'show' => FALSE,
              ];
            }
          }
        }
      }

      // Image align class BC.
      $variables['image_align'] = '';
    }
  }
}

/**
 * Preprocess variables for breadcrumb templates.
 *
 * @param $variables
 */
function at_core_preprocess_breadcrumb(&$variables) {
  // Config.
  $config = $variables['theme']['config'];

  // New attributes array for breadcrumb title.
  $variables['title_attributes'] = new Attribute(['class' => []]);

  // Set attributes.
  $variables['breadcrumb_label'] = FALSE;

  // Label value.
  $variables['breadcrumb_label_value'] = isset($config['breadcrumb_label_value']) ? $config['breadcrumb_label_value'] : 'You are here:';

  // Theme settings.
  if ($variables['theme']['extensions']['is_enabled'] === TRUE) {
    if (isset($config['enable_markup_overrides']) && $config['enable_markup_overrides'] === 1) {

      // Show the label.
      if (isset($config['breadcrumb_label']) && $config['breadcrumb_label'] === 1) {
        $variables['breadcrumb_label'] = TRUE;
      }

      // Remove the Home link.
      if (isset($config['breadcrumb_home']) && $config['breadcrumb_home'] === 1) {
        $first_item = array_values($variables['breadcrumb'])[0];
        if (isset($first_item['url']) && $first_item['url'] === base_path()) {
          array_shift($variables['breadcrumb']);
        }
      }

      // Add title to breadcrumbs.
      if (isset($config['breadcrumb_title']) && $config['breadcrumb_title'] === 1) {
        // Use a placeholder to inject dynamic content.
        $placeholder_title = [
          '#lazy_builder' => ['at_tool.lazy_builders:breadcrumbTitle', []],
          '#create_placeholder' => TRUE,
        ];
        $variables['breadcrumb'][] = ['text' => $placeholder_title];
      }

      // If home is the only item, remove it.
      if (isset($config['breadcrumb_home_alone']) && $config['breadcrumb_home_alone'] === 1) {
        $arr_length = count($variables['breadcrumb']);
        if ($arr_length === 1 && $variables['breadcrumb'][0]['url'] === base_path()) {
          unset($variables['breadcrumb'][0]);
        }
      }

      // Trim long items.
      $variables['breadcrumb_item_length'] = 0;
      if (isset($config['breadcrumb_item_length']) && $config['breadcrumb_item_length'] > 0) {
        $variables['breadcrumb_item_length'] = Html::escape($config['breadcrumb_item_length']);
      }
    }
  }
}

/**
 * Preprocess variables for user templates.
 *
 * @param $variables
 */
function at_core_preprocess_user(&$variables) {
  // Current user.
  $user = $variables['elements']['#user'];

  // Label.
  $variables['label'] = Html::escape($user->getDisplayName());

  // Roles.
  $roles = $user->getRoles(FALSE);
  $variables['roles'] = [];
  // Playing it safe here because I don't trust Drupal to return an array...
  if (!empty($roles) && is_array($roles)) {
    foreach ($roles as $role) {
      $variables['roles'][] = 'has-role--' . Html::cleanCssIdentifier($role);
    }
  }

  // View mode.
  $variables['view_mode'] = $variables['elements']['#view_mode'];

  // Check if he current user has permission to access user profiles.
  $current_user = \Drupal::currentUser();
  $variables['access_profiles'] = FALSE;
  if ($current_user->hasPermission('access user profiles')) {
    $variables['access_profiles'] = TRUE;
  }

  // Base path, used to build a link to the profile in non-full view modes.
  $variables['base_path'] = base_path();
}

/**
 * Preprocess variables for user templates.
 *
 * @param $variables
 */
function at_core_preprocess_views_view(&$variables) {
  // Theme settings.
  if ($variables['theme']['extensions']['is_enabled'] === TRUE) {
    $config = $variables['theme']['config'];
    if (isset($config['enable_markup_overrides']) && $config['enable_markup_overrides'] === 1) {
      if (isset($config['views_hide_feedicon']) && $config['views_hide_feedicon'] === 1) {
        $variables['feed_icons'] = [];
      }
    }
  }
}

/**
 * Preprocess variables for links templates.
 *
 * @param $variables
 */
function at_core_preprocess_links(&$variables) {
  // We add lots of classes to link items, wrappers and links to support many
  // options for icons, backgrounds, borders etc.
  foreach ($variables['links'] as $key => $values) {
    $variables['links'][$key]['wrapper_attributes'] = new Attribute();
    $variables['links'][$key]['wrapper_attributes']->addClass(Html::cleanCssIdentifier($key . '__link-wrapper'));

    if (isset($variables['links'][$key]['link']['#options']['attributes'])) {
      $variables['links'][$key]['link']['#options']['attributes']['class'] = [Html::cleanCssIdentifier($key . '__link')];
    }

    if (isset($variables['links'][$key]['link']['#options']['attributes']['class'])) {
      $class = $variables['links'][$key]['link']['#options']['attributes']['class'];
      if (is_string($class)) {
        $variables['links'][$key]['attributes']->addClass(Html::cleanCssIdentifier($key . '--item' . '--' . $class));
      }
    }
  }
}

/**
 * Preprocess variables for forums templates.
 *
 * @param $variables
 */
function at_core_preprocess_forums(&$variables) {
  // Add a class to each forum topic table header td.
  if (isset($variables['topics']['#header'])) {
    foreach ($variables['topics']['#header'] as $topic_list_key => $topic_list_value) {
      $variables['topics']['#header'][$topic_list_key]['class'][] = 'forum-header__' . Html::cleanCssIdentifier(strtolower($variables['topics']['#header'][$topic_list_key]['data']));
    }
  }

  // Add BEM classes to row items to match the forum-list.html.twig BEM classes.
  if (isset($variables['topics']['#rows'])) {
    foreach ($variables['topics']['#rows'] as $row_key => $row_values) {
      foreach ($row_values as $row_values_key => $row_values_value) {
        foreach ($row_values_value['class'] as $class_key => $class_value) {
          $class_value = str_replace('forum__', '', $class_value);
          $variables['topics']['#rows'][$row_key][$row_values_key]['class'][] = 'forum-list__' . Html::cleanCssIdentifier(strtolower($class_value));
          unset($variables['topics']['#rows'][$row_key][$row_values_key]['class'][$class_key]);
        }
      }
    }
  }
}

/**
 * Implements hook_preprocess_HOOK() for maintenance-page.html.twig.
 *
 * @param $variables
 */
function at_core_preprocess_maintenance_page(&$variables) {
  // Load the maintenance page library.
  $variables['#attached']['library'][] = $variables['theme']['provider'] . '/maintenance_page';
}

/**
 * Preprocess variables for the select template.
 *
 * @param $variables
 */
function at_core_preprocess_select(&$variables) {
  $variables['content_attributes']['class'][] = 'form-type-select__select-wrapper';
  if ($variables['element']['#multiple'] == TRUE) {
    $variables['content_attributes']['class'][] = 'is-multiple';
  }
}

/**
 * Preprocess variables for AT Layout Plugin templates.
 * This function is added in at_core_theme_registry_alter().
 *
 * @param $variables
 */
function at_core_preprocess_at_layout(&$variables) {
  $attributes = _at_core_layout_plugin_attributes($variables);
  if (isset($attributes['role'])) {
    $variables['attributes']['role'] = $attributes['role'];
  }
  $variables['attributes']['class'] = $attributes['classes'];
  $variables['html_element'] = $attributes['html_element'];
}

/**
 * Preprocess variables for node add/edit templates.
 *
 * @param $variables
 */
function at_core_preprocess_node_edit_form(&$variables) {
  $request = \Drupal::request();
  $route_match = \Drupal::routeMatch();
  $title = \Drupal::service('title_resolver')->getTitle($request, $route_match->getRouteObject());
  $variables['title'] = $title ?: $variables['form']['#title'];
}
