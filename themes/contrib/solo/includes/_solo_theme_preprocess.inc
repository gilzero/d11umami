<?php

/**
 * @file
 * Theme preprocess functions.
 */

use Drupal\Component\Utility\Html;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Template\Attribute;
use Drupal\node\NodeInterface;
use Drupal\user\UserInterface;

/**
 * Helper function.
 */
function _add_node_id_to_body_class($route_match, &$variables): void {
  $node_id = $route_match->getParameter('node')->id();
  if ($node_id) {
    $variables['attributes']['class'][] = 'path-node-' . $node_id;
  }
}

/**
 * Helper function.
 */
function _add_taxonomy_term_to_body_class($route_match, &$variables): void {
  $term = $route_match->getParameter('taxonomy_term');
  if ($term->id()) {
    $variables['attributes']['class'][] = 'path-taxonomy-term-' . $term->id();
  }
  if ($term->bundle()) {
    $variables['attributes']['class'][] = 'path-vocabulary-' . Html::cleanCssIdentifier($term->bundle());
  }
}

/**
 * Helper function.
 */
function _add_user_path_to_body_class(&$variables): void {
  $curr_path = \Drupal::service('path.current')->getPath();
  $curr_path = ltrim($curr_path, '/');
  $user_url = explode('/', $curr_path);
  if (isset($user_url[0], $user_url[1]) && ($user_url[0] == 'user')) {
    $variables['attributes']['class'][] = 'path-user-' . $user_url[1];
  }
}

/**
 * Helper function.
 */
function _add_view_id_to_body_class($route_match, &$variables): void {
  $view_id = $route_match->getParameter('view_id');
  if ($view_id) {
    $variables['attributes']['class'][] = 'views-page path-view-' . Html::cleanCssIdentifier($view_id);
  }
}

/**
 * Helper function.
 */
function _add_admin_context_to_body_class(&$variables): void {
  /** @var \Drupal\Core\Routing\AdminContext $admin_context */
  $admin_context = \Drupal::service('router.admin_context');
  if ($admin_context->isAdminRoute()) {
    $variables['attributes']['class'][] = 'admin-context';
  }
}

/**
 * Helper function.
 */
function _get_theme_setting_class($setting, $class_prefix) {
  $value = theme_get_setting($setting);
  return $value ? " {$class_prefix}-{$value}" : '';
}

/**
 * Helper function.
 */
function _get_html_classes() {
  $fs_size = _get_theme_setting_class('site_global_font_size', 'fs');
  $gap_size = _get_theme_setting_class('site_global_regions_gap', 'gap');

  return $fs_size . $gap_size;
}

/**
 * Implements hook_preprocess_HOOK() for HTML document templates.
 */
function solo_preprocess_html(&$variables): void {
  $route_match = \Drupal::routeMatch();
  $route_name = $route_match->getRouteName();
  $solo_utilities = \Drupal::request()->getBasePath();
  $solo_list = \Drupal::service('extension.list.theme')->getPath('solo');
  $solo_path = $solo_utilities . '/' . $solo_list;
  $variables['solo_path'] = $solo_path;

  // Check if we're viewing a user profile page.
  if ($route_name === 'entity.user.canonical') {
    // Add a custom CSS class to the body tag.
    $variables['attributes']['class'][] = 'user-profile-page';
  }

  $theme_head_vars = ['site_css_injector',
    'site_css_dynamic',
    'site_import_google_font',
  ];
  foreach ($theme_head_vars as $theme_head_var) {
    $variables[$theme_head_var] = theme_get_setting($theme_head_var);
  }

  $google_font = theme_get_setting('site_import_google_font');
  if (!empty($google_font)) {
    $variables['#attached']['html_head'][] = [
      [
        '#tag' => 'link',
        '#attributes' => [
          'href' => $google_font,
          'rel' => 'stylesheet',
          'type' => 'text/css',
        ],
      ],
      'google_font',
    ];
  }

  $css_injector = theme_get_setting('site_css_injector');
  if (!empty($css_injector)) {
    $variables['#attached']['html_head'][] = [
      [
        '#type' => 'html_tag',
        '#tag' => 'style',
        '#value' => $css_injector,
      ],
      'inline_css',
    ];
  }

  $file_system = \Drupal::service('file_system');
  $file_url_generator = \Drupal::service('file_url_generator');

  // Assuming you've already retrieved your dynamic content.
  $css_dynamic = theme_get_setting('site_css_dynamic');
  $js_dynamic = theme_get_setting('site_js_dynamic');

  // Define the file URIs.
  $css_file_uri = 'public://solo/css/solo-css-dynamic.css';
  $js_file_uri = 'public://solo/js/solo-js-dynamic.js';

  // Check and prepare directories.
  $css_directory = $file_system->dirname($css_file_uri);
  $js_directory = $file_system->dirname($js_file_uri);
  if (!$file_system->prepareDirectory($css_directory, FileSystemInterface::CREATE_DIRECTORY) ||
      !$file_system->prepareDirectory($js_directory, FileSystemInterface::CREATE_DIRECTORY)) {
    // Log an error if directories could not be prepared.
    \Drupal::logger('solo_preprocess_html')->error('Failed to prepare directory for CSS or JS.');
    return;
  }

  // Handle dynamic CSS.
  if (!empty($css_dynamic)) {
    // Write the CSS content to the file.
    if ($file_system->saveData($css_dynamic, $css_file_uri, FileSystemInterface::EXISTS_REPLACE) !== FALSE) {
      // Generate the absolute URL for the CSS file and attach it to the HTML.
      $css_url = $file_url_generator->generateAbsoluteString($css_file_uri);
      $variables['#attached']['html_head'][] = [
        [
          '#type' => 'html_tag',
          '#tag' => 'link',
          '#attributes' => ['href' => $css_url, 'rel' => 'stylesheet'],
        ],
        'solo_dynamic_css',
      ];
    }
    else {
      \Drupal::logger('solo_preprocess_html')->error('Failed to save dynamic CSS content.');
    }
  }
  else {
    // Delete the CSS file if $css_dynamic is empty.
    $css_real_path = \Drupal::service('file_system')->realpath($css_file_uri);
    if (file_exists($css_real_path)) {
      $file_system->delete($css_file_uri);
    }
  }

  // Handle dynamic JS.
  if (!empty($js_dynamic)) {
    // Attach the JS library.
    $variables['#attached']['library'][] = 'solo/solo-js-dynamic';
    // Write the JS content to the file.
    if ($file_system->saveData($js_dynamic, $js_file_uri, FileSystemInterface::EXISTS_REPLACE) !== FALSE) {
      // Generate the absolute URL for the JS file and attach it to the HTML.
      $js_url = $file_url_generator->generateAbsoluteString($js_file_uri);
      $variables['#attached']['html_head'][] = [
        [
          '#type' => 'html_tag',
          '#tag' => 'script',
          '#attributes' => [
            'src' => $js_url,
            'type' => 'application/javascript',
          ],
        ],
        'solo_dynamic_js',
      ];
    }
    else {
      \Drupal::logger('solo_preprocess_html')->error('Failed to save dynamic JS content.');
    }
  }
  else {
    // Delete the JS file if $js_dynamic is empty.
    $js_real_path = \Drupal::service('file_system')->realpath($js_file_uri);
    if (file_exists($js_real_path)) {
      $file_system->delete($js_file_uri);
    }
  }

  if (theme_get_setting('site_global_font_size') || theme_get_setting('site_global_regions_gap')) {
    $variables['html_attributes']->setAttribute('class', _get_html_classes());
  }

  // Add node id to the body class.
  if ($route_name === 'entity.node.canonical') {
    _add_node_id_to_body_class($route_match, $variables);
  }

  // Add vocabulary name and term id to the body class.
  if ($route_name === 'entity.taxonomy_term.canonical') {
    _add_taxonomy_term_to_body_class($route_match, $variables);
  }

  // Check if the cookies module is enabled.
  if (\Drupal::moduleHandler()->moduleExists('cookies')) {
    // Attach the library when the module is enabled.
    $variables['#attached']['library'][] = 'solo/solo-cookies';
  }

  // If the user is logged in use ID if not use second word after user/login.
  _add_user_path_to_body_class($variables);

  // Add view id name to the body class.
  _add_view_id_to_body_class($route_match, $variables);

  // Add admin context to the body class.
  _add_admin_context_to_body_class($variables);

}

/**
 * Helper function.
 */
function _get_site_regions() {
  return [
    'page_wrapper',
    'popup_login_block',
    'fixed_search_block',
    'header',
    'primary_sidebar_menu',
    'primary_menu',
    'welcome_text',
    'top_container',
    'top_first',
    'top_second',
    'top_third',
    'highlighted',
    'system_messages',
    'page_title',
    'breadcrumb',
    'main_container',
    'sidebar_first',
    'content',
    'sidebar_second',
    'bottom_container',
    'bottom_first',
    'bottom_second',
    'bottom_third',
    'bottom_fourth',
    'footer_container',
    'footer_first',
    'footer_second',
    'footer_third',
    'footer_menu',
    'credit_copyright',
  ];
}

/**
 * Helper function.
 */
function _get_css_classes() {
  $regions = _get_site_regions();
  $css_classes = [];
  foreach ($regions as $region) {
    $css_classes[] = "classes_{$region}";
  }
  return $css_classes;
}

/**
 * Helper function.
 */
function _get_region_attributes() {
  return [
    '__r_bg' => 'Background',
    '__r_tx' => 'Text',
    '__r_h1' => 'H1, H2, H3 Text',
    '__r_lk' => 'Text Link',
    '__r_lk_h' => 'Text Link Hover',
    '__r_br' => 'Border Color',
    '__r_bg_fr' => 'Background Input Form',
    '__r_tx_lk' => 'Text Menu Link',
    '__r_tx_lk_h' => 'Text Menu Link Hover',
    '__r_bg_lk' => 'Background Menu Link',
    '__r_bg_lk_h' => 'Background Menu Link Hover',
    '__r_tx_bt' => 'Text Button',
    '__r_tx_bt_h' => 'Text Button Hover',
    '__r_bg_bt' => 'Background Button',
    '__r_bg_bt_h' => 'Background Button Hover',

  ];
};

/**
 * Helper function.
 */
function _get_settings_region() {
  $regions = _get_site_regions();
  $attributes = _get_region_attributes();
  $settings_region = [];
  foreach ($regions as $region) {
    foreach ($attributes as $attribute_key => $attribute_label) {
      $settings_region[] = "settings_{$region}_{$attribute_key}";
    }
  }
  return $settings_region;
}

/**
 * Helper function.
 */
function _get_region_attributes_for_region($region, $attributes) {
  $region_attr = [];

  foreach ($attributes as $key => $value) {
    if (theme_get_setting("settings_{$region}_{$key}")) {
      $css_val = theme_get_setting("settings_{$region}_{$key}");
      $css_var = str_replace('_', '-', $key);
      // Un comment the below to use hsla colors.
      // $css_val = _solo_convert_hex_to_hsl($css_val);
      // $css_val = implode(', ', $css_val);
      // $css_val = "hsla({$css_val},1)";.
      $css_var = "{$css_var}:{$css_val}";
      $region_attr[] = $css_var;
    }
  }

  return $region_attr;
}

/**
 * Helper function.
 */
function _create_region_attributes(&$variables) {
  $site_regions = _get_site_regions();
  $attributes = _get_region_attributes();

  foreach ($site_regions as $region) {
    $variables["attributes_{$region}"] = new Attribute();
    $region_attr = _get_region_attributes_for_region($region, $attributes);

    if ($region_attr) {
      $variables["attributes_{$region}"]->setAttribute('style', implode(';', $region_attr));
    }
  }
}

/**
 * Helper function.
 */
function _get_theme_variables() {

  return [
    'misc' => [
      'opacity_page_wrapper',
      'site_global_breakpoints',
      'site_menu_breakpoints',
      'site_global_width',
      'site_css_injector',
      'site_name_google_font',
      'site_pagetitle_google_font',
      'site_font_google_special_on',
      'site_font_google_heading_on',
      'site_font_google_global_on',
      'site_font_google_local_on',
      'site_font_awesome_on',
      'site_load_w3css_on',
      'site_opacity_onscroll_on',
      'site_login_page_on',
      'site_pagetitle_animate_on',
      'site_pagetitle_font_size_s',
      'site_pagetitle_font_size_l',
      'site_name_font_size_s',
      'site_name_font_size_l',
      'site_name_animate_on',
      'site_breadcrumb_scroll',
      'site_breadcrumb_pagetitle_off',
      'site_inline_items_on',
      'site_regions_top_animate_border',
      'site_regions_bottom_animate_border',
      'site_regions_footer_animate_border',
      'site_regions_top_animate_hover',
      'site_regions_bottom_animate_hover',
      'site_regions_footer_animate_hover',
      'site_regions_top_rounded',
      'site_regions_main_rounded',
      'site_regions_bottom_rounded',
      'site_regions_footer_rounded',
      'site_regions_highlighted_disable',
      'site_regions_welcome_disable',
      'site_regions_welcome_width',
      'site_regions_top_disable',
      'site_regions_bottom_disable',
      'site_regions_footer_disable',
      'site_flip_header_menu',
      'site_mobile_layout_order',
      'primary_menu_border',
      'primary_menu_branding',
      'primary_menu_justify_content',
      'primary_sidebar_menu_border',
      'primary_sidebar_menu_branding',
      'site_regions_top_border',
      'site_regions_main_border',
      'site_regions_bottom_border',
      'site_regions_footer_border',
      'top_2col',
      'top_3col',
      'main_2col',
      'main_3col',
      'bottom_2col',
      'bottom_3col',
      'bottom_4col',
      'footer_2col',
      'footer_3col',
      'header_change_icons',
      'header_sitename_center',
      'header_sitename_center_small',
      'header_sitename_center_default',
      'header_logo_image_alt',
      'header_popup_login',
      'count_top',
      'count_main',
      'count_bottom',
      'count_footer',
      'theme_category',
    ],
    'social' => [
      'sm_icon_size',
      'sm_icon_colors',
      'sm_show_icons',
      'sm_icon_snapchat',
      'sm_icon_tiktok',
      'sm_icon_threads',
      'sm_icon_whatsapp',
      'sm_icon_telegram',
      'sm_icon_reddit',
      'sm_icon_facebook',
      'sm_icon_twitter',
      'sm_icon_pinterest',
      'sm_icon_linkedin',
      'sm_icon_instagram',
      'sm_icon_youtube',
      'sm_icon_drupal',
      'sm_icon_email',
      'sm_icon_rss',
      'sm_icon_mastodon',
      'footer_copyright',
      'footer_link',
      'footer_link_label',
      'footer_link_text',
    ],
    'css_classes' => _get_css_classes(),
    'settings_region' => _get_settings_region(),
  ];
}

/**
 * Implements hook_preprocess_page() for page templates.
 */
function solo_preprocess_page(&$variables) {
  // $variables['solo_path'] = &$variables['solo_path'];
  $variables_lists = _get_theme_variables();
  foreach ($variables_lists as $variables_list) {
    foreach ($variables_list as $variable_list_value) {
      $variables[$variable_list_value] = theme_get_setting($variable_list_value);
    }
  }

  if (theme_get_setting('site_global_breakpoints')) {
    $g_point = theme_get_setting('site_global_breakpoints');
    $variables['#attached']['library'][] = 'solo/solo-global-breakpoints-' . $g_point;
  }

  if (theme_get_setting('site_menu_breakpoints')) {
    $m_point = theme_get_setting('site_menu_breakpoints');
    $variables['#attached']['library'][] = 'solo/solo-menu-breakpoints-' . $m_point;
  }

  if (theme_get_setting('primary_menu_keyboard')) {
    $variables['#attached']['library'][] = 'solo/solo-menu-keyboard';
  }

  if ($layout_order = theme_get_setting('site_mobile_layout_order')) {
    $layout_class = str_replace('_', '-', $layout_order);
    $variables['main_layout_order'] = $layout_class;
  }

  if (!theme_get_setting('system_messages_disable_dark')) {
    $variables['#attached']['library'][] = 'solo/solo-system-messages-dark';
  }

  if (theme_get_setting('site_name_font_size_s')) {
    $variables['name_size_s'] = 'name-s-' . theme_get_setting('site_name_font_size_s');
  }

  if (theme_get_setting('site_name_font_size_l')) {
    $variables['name_size_l'] = 'name-l-' . theme_get_setting('site_name_font_size_l');
  }

  if (theme_get_setting('site_pagetitle_font_size_s')) {
    $variables['title_size_s'] = 'title-s-' . theme_get_setting('site_pagetitle_font_size_s');
  }

  if (theme_get_setting('site_pagetitle_font_size_l')) {
    $variables['title_size_l'] = 'title-l-' . theme_get_setting('site_pagetitle_font_size_l');
  }

  if (theme_get_setting('site_pagetitle_center')) {
    $variables['site_pagetitle_center'] = 'pt-center';
  }

  if (theme_get_setting('primary_sidebar_menu_branding')) {
    $variables['site_logo'] = theme_get_setting('logo.url');
    $variables['logo_default'] = _set_logo_default($variables);
    $variables['site_name'] = \Drupal::config('system.site')->get('name');
    $variables['logo_alt'] = theme_get_setting('header_logo_image_alt') ?: 'Home';
  }

  // Top regions variables.
  $regions = [
    'top' => ['first', 'second', 'third'],
    'sidebar' => ['first', 'content', 'second'],
    'bottom' => ['first', 'second', 'third', 'fourth'],
    'footer' => ['first', 'second', 'third'],
  ];

  foreach ($regions as $region => $subregions) {
    // Loop through each subregion.
    foreach ($subregions as $subregion) {
      if ($subregion == 'content') {
        ${"has_{$subregion}"} = !empty($variables['page']["{$subregion}"]);
      }
      // Check if the region and subregion exist in the 'page' array.
      ${"has_{$region}_{$subregion}"} = !empty($variables['page']["{$region}_{$subregion}"]);
    }
  }
  // Add Responsive classes to Top Container.
  $count_top = (int) $has_top_first + (int) $has_top_second + (int) $has_top_third;
  $count_main = (int) $has_sidebar_first + (int) $has_content + (int) $has_sidebar_second;
  $count_bottom = (int) $has_bottom_first + (int) $has_bottom_second + (int) $has_bottom_third + (int) $has_bottom_fourth;
  $count_footer = (int) $has_footer_first + (int) $has_footer_second + (int) $has_footer_third;
  $found_main = [$has_sidebar_first, $has_content, $has_sidebar_second];

  $counts = [
    'top' => $count_top,
    'main' => $count_main,
    'bottom' => $count_bottom,
    'footer' => $count_footer,
  ];

  foreach ($counts as $key => $count) {
    if ($count === 1) {
      $variables[$key . '_layout'] = 'solo-col-1';
    }
    if ($count === 2) {
      $variables[$key . '_layout'] = theme_get_setting($key . '_2col') ?? 'solo-col-1-1';
      // Special setting for main container.
      if ($key == 'main') {
        if (!$found_main[0]) {
          $variables[$key . '_layout'] = theme_get_setting($key . '_2col') ?? 'solo-col-2-1';
        }
        if (!$found_main[2]) {
          $variables[$key . '_layout'] = theme_get_setting($key . '_2col') ?? 'solo-col-1-2';
        }
      }
    }
    if ($count === 3) {
      $variables[$key . '_layout'] = theme_get_setting($key . '_3col') ?? 'solo-col-1-1-1';
      // Special setting for main container.
      if ($key == 'main') {
        $variables[$key . '_layout'] = theme_get_setting($key . '_3col') ?? 'solo-col-1-2-1';
      }
    }
    if ($count === 4) {
      $variables[$key . '_layout'] = theme_get_setting($key . '_4col') ?? 'solo-col-1-1-1-1';
    }
  }

  // Check if the curren user on login/register or reset.
  if (theme_get_setting('site_login_page_on')) {
    $curr_path = \Drupal::service('path.current')->getPath();
    $curr_path = ltrim($curr_path, '/');
    if ($curr_path == 'user/login' || $curr_path == 'user/register' || $curr_path == 'user/password') {
      unset($variables['page']['sidebar_first'], $variables['page']['sidebar_second']);
      $variables['#attached']['library'][] = 'solo/solo-user-login-custom';
    }
  }

  _create_region_attributes($variables);

  // Initialize with the global width setting.
  $final_widths = theme_get_setting('site_global_width');
  $type_widths  = theme_get_setting('enable_custom_widths');
  $node_widths  = theme_get_setting('enable_custom_node_width');

  if ($type_widths || $node_widths) {
    $node = \Drupal::routeMatch()->getParameter('node');
    if ($node instanceof NodeInterface) {
      $content_type = $node->bundle();
      // Check for content type-specific.
      $content_type_width = theme_get_setting('site_width_' . $content_type);
      if ($content_type_width) {
        $final_widths = $content_type_width;
      }

      // Check for node-specific width.
      $node_widths = \Drupal::entityTypeManager()->getStorage('node_width')->loadByProperties(['node_id' => $node->id()]);
      if (!empty($node_widths)) {
        $final_widths = reset($node_widths)->get('width_class')->value;
      }
    }
  }

  // Pass the width class to the Twig template.
  $variables['site_width_class'] = $final_widths;
}

/**
 * Implements hook_preprocess_HOOK() for node.html.twig.
 */
function solo_preprocess_node(&$variables) {
  $d_format = theme_get_setting('site_node_date_format') ?? 'solo_date_l';
  // Remove the "Add new comment" link on teasers or when the comment form is
  // displayed on the page.
  if ($variables['teaser'] || !empty($variables['content']['comments']['comment_form'])) {
    unset($variables['content']['links']['comment']['#links']['comment-add']);
  }

  // Apply custom date formatter to "date" field.
  if (!empty($variables['date']) && !empty($variables['display_submitted']) && $variables['display_submitted'] === TRUE) {
    $variables['date'] = \Drupal::service('date.formatter')->format($variables['node']->getCreatedTime(), $d_format);
  }
  // Check if the content type is in the array of
  // enabled types for reading mode.
  $content_type = $variables['node']->getType();
  $reading_mode_enabled = theme_get_setting('site_reading_mode_content_type');
  if (is_array($reading_mode_enabled) && in_array($content_type, $reading_mode_enabled, TRUE)) {
    $variables['attributes']['class'][] = 'reading-mode';
  }

}

/**
 * Implements hook_preprocess_breadcrumb().
 */
function solo_preprocess_breadcrumb(&$variables) {

  if (!empty($variables['breadcrumb']) && !theme_get_setting('site_breadcrumb_pagetitle_off')) {
    $request = \Drupal::request();
    $route_match = \Drupal::routeMatch();
    $page_title = \Drupal::service('title_resolver')->getTitle($request, $route_match->getRouteObject());
    if (!empty($page_title) && is_string($page_title)) {
      $variables['breadcrumb'][] = [
        'text' => $page_title,
      ];
      $variables['#cache']['contexts'][] = 'url';
    }
  }
}

/**
 * Implements hook_preprocess_field().
 */
function solo_preprocess_field(&$variables, $hook) {
  // Check if the field is of type 'entity_reference'.
  if (isset($variables['element']['#field_type']) && $variables['element']['#field_type'] === 'entity_reference') {
    // Check if items are set and not empty.
    if (!empty($variables['element']['#items'])) {
      // Load the first item.
      $first_item = $variables['element']['#items']->first();

      // Check if the first item is not empty and has an 'entity' property.
      if ($first_item && $first_item->entity) {
        $entity = $first_item->entity;

        // Get the entity type of the referenced entity.
        $entity_type = $entity->getEntityTypeId();

        // Create a CSS class based on the entity type
        // (e.g., 'taxonomy-entity', 'user-entity').
        $class = $entity_type . '-entity';

        // Sanitize and add the CSS class to the field wrapper.
        // Replace 'drupal_html_class()' with a custom sanitization or
        // use Html::getClass() from Drupal\Component\Utility\Html.
        $variables['attributes']['class'][] = Html::getClass($class);
      }
    }
  }
}

/**
 * Implements hook_preprocess_HOOK().
 */
function solo_preprocess_field__node__created(&$variables): void {
  array_walk($variables['items'], function (&$item) {
    if (isset($item['content']['#prefix'])) {
      unset($item['content']['#prefix']);
    }
  });
}

/**
 * Implements hook_preprocess_HOOK() for setting classes.
 */
function solo_preprocess_filter_caption(&$variables): void {
  $variables['classes'] = $variables['classes'] ?? '';
  $variables['classes'] .= ' caption';
}

/**
 * Implements hook_preprocess_search_result().
 */
function solo_preprocess_search_result(&$variables): void {
  // Apply custom date formatter to "date" field.
  if (!empty($variables['result']['date'])) {
    $variables['info_date'] = \Drupal::service('date.formatter')->format($variables['result']['node']->getCreatedTime(), 'solo_date_s');
  }
}

/**
 * Implements hook_preprocess_HOOK() for comment.html.twig.
 */
function solo_preprocess_comment(&$variables): void {
  // Getting the node creation time stamp from the comment object.
  $date = $variables['comment']->getCreatedTime();

  // Getting the current request time.
  $request_time = DrupalDateTime::createFromTimestamp(\Drupal::time()->getRequestTime());

  // Getting the date formatter service.
  $date_formatter = \Drupal::service('date.formatter');

  // Formatting "created" as "X days ago".
  $variables['created'] = t('@time ago', [
    '@time' => $date_formatter->formatInterval($request_time->getTimestamp() - $date),
  ]);
}

/**
 * Implements hook_preprocess_HOOK() for block.html.twig.
 */
function solo_preprocess_block(&$variables): void {
  if (empty($variables['elements']['#id'])) {
    return;
  }

  $block = _load_block($variables['elements']['#id']);
  if (!$block) {
    return;
  }

  _process_system_menu_block($variables, $block);
  _process_system_branding_block($variables);
  _set_logo_default($variables);
  _set_logo_alt($variables);
}

/**
 * Helper function.
 */
function _load_block($id): ?EntityInterface {
  return \Drupal::entityTypeManager()
    ->getStorage('block')
    ->load($id);
}

/**
 * Helper function.
 */
function _process_system_menu_block(&$variables, $block) {
  if ($variables['base_plugin_id'] !== 'system_menu_block') {
    return;
  }

  $region = $block->getRegion();
  $variables['content']['#attributes']['region'] = $region;
}

/**
 * Helper function.
 */
function _set_logo_default(&$variables): void {
  // Get the machine name of the current default theme.
  $default_theme = \Drupal::config('system.theme')->get('default');
  // Get the machine name of the active theme.
  $active_theme_name = \Drupal::theme()->getActiveTheme()->getName();
  // Get the 'logo.use_default' configuration from the 'solo' theme.
  $logo_default = \Drupal::config('solo.settings')->get('logo.use_default');

  // If both active and default themes are 'solo' and 'logo.use_default' is 1.
  if ($default_theme === 'solo' && $active_theme_name === 'solo' && $logo_default == 1) {
    $variables['logo_default'] = 1;
  }
  else {
    // Initialize $logo_default to 0.
    $variables['logo_default'] = 0;
  }
}

/**
 * Helper function.
 */
function _set_logo_alt(&$variables): void {
  $variables['logo_alt'] = theme_get_setting('header_logo_image_alt') ?: 'Home';
}

/**
 * Helper function.
 */
function _process_system_branding_block(&$variables): void {
  // Ensure this function only processes system branding blocks.
  if ($variables['base_plugin_id'] !== 'system_branding_block') {
    return;
  }
}

/**
 * Implements hook_preprocess_menu().
 */
function solo_preprocess_menu__primary_menu(&$variables): void {

  if (theme_get_setting('primary_menu_on_hover')) {
    $variables['click_hover'] = 'navigation-responsive-hover';
  }
  else {
    $variables['click_hover'] = 'navigation-responsive-click';
  }

  if (theme_get_setting('primary_menu_align_center')) {
    $variables['align_center'] = 'menu-align-center';
  }

  if (theme_get_setting('primary_menu_expand_left')) {
    $variables['expand_left'] = 'expand-left';
  }

  if (theme_get_setting('primary_menu_megamenu')) {
    $variables['megamenu'] = 'navigation__megamenu';
    $variables['sub_mega'] = 'sub-mega';
    $variables['megamenu_layout'] = (theme_get_setting('primary_menu_megamenu_layout'));
    if (theme_get_setting('primary_menu_submenu_header')) {
      $variables['sub_menu_header'] = 'sub__menu-header';
    }
  }

  if (theme_get_setting('primary_menu_branding')) {
    $variables['primary_menu_branding'] = 'activate-branding';
    $variables['site_logo'] = theme_get_setting('logo.url');
    $variables['logo_default'] = _set_logo_default($variables);
    $variables['site_name'] = \Drupal::config('system.site')->get('name');
    $variables['logo_alt'] = theme_get_setting('header_logo_image_alt') ?: 'Home';
  }

}

/**
 * Implements hook_preprocess_menu().
 */
function solo_preprocess_menu__primary_sidebar_menu(&$variables): void {

  if (theme_get_setting('primary_sidebar_menu_hover')) {
    $variables['click_hover_sidebar'] = 'navigation-sidebar-hover';
  }
  else {
    $variables['click_hover_sidebar'] = 'navigation-sidebar-click';
  }

}

/**
 * Implements hook_preprocess_menu().
 */
function solo_preprocess_menu__account(&$variables): void {

  if (!$variables['user']->isAuthenticated()) {
    $enableLoginBlock = theme_get_setting('header_popup_login');

    if ($enableLoginBlock) {
      $loginText = theme_get_setting('header_login_links');
      $currentPath = \Drupal::service('path.current')->getPath();
      $variables['login_popup_block'] = $enableLoginBlock;
      $variables['login_text'] = $loginText;
      $variables['current_path'] = $currentPath;
      foreach ($variables['items'] as $key => $item) {
        $url = $item['url']->toString();
        if ($url == '/user/login') {
          unset($variables['items'][$key]);
        }
      }
    }

  }

}

/**
 * Implements hook_preprocess_form_element().
 */
function solo_preprocess_form_element(&$variables): void {
  if (in_array($variables['element']['#type'] ?? FALSE, ['checkbox', 'radio'], TRUE)) {
    $variables['attributes']['class'][] = 'form-type-boolean';
  }

  if (!empty($variables['description']['attributes'])) {
    $variables['description']['attributes']->addClass('form-item__description');
  }

  if ($variables['disabled']) {
    $variables['label']['#attributes']['class'][] = 'is-disabled';
  }
}

/**
 * Implements hook_preprocess_HOOK().
 */
function solo_preprocess_form_element_label(&$variables): void {
  $variables['attributes']['class'][] = 'form-item__label';
}

/**
 * Implements hook_preprocess_HOOK().
 */
function solo_preprocess_input(&$variables): void {
  if (
    !empty($variables['element']['#title_display']) &&
    $variables['element']['#title_display'] === 'attribute' &&
    !empty((string) $variables['element']['#title'])
  ) {
    $variables['attributes']['title'] = (string) $variables['element']['#title'];
  }

  $type_api = $variables['element']['#type'];
  $type_html = $variables['attributes']['type'];
  $text_types_html = [
    'text',
    'email',
    'tel',
    'number',
    'search',
    'password',
    'date',
    'time',
    'file',
    'color',
    'datetime-local',
    'url',
    'month',
    'week',
  ];

  if (in_array($type_html, $text_types_html, TRUE)) {
    $variables['attributes']['class'][] = 'form-element';
    $variables['attributes']['class'][] = Html::getClass('form-element--type-' . $type_html);
    $variables['attributes']['class'][] = Html::getClass('form-element--api-' . $type_api);

    // This logic is functioning as expected, but there is nothing in the
    // theme that renders the result.
    // As a result it can't currently be covered by a functional test.
    if (!empty($variables['element']['#autocomplete_route_name'])) {
      $variables['autocomplete_message'] = t('Loading…');
    }
  }

  if (in_array($type_html, ['checkbox', 'radio'], TRUE)) {
    $variables['attributes']['class'][] = 'form-boolean';
    $variables['attributes']['class'][] = Html::getClass('form-boolean--type-' . $type_html);
  }
}

/**
 * Implements hook_preprocess_HOOK() for menu-local-task templates.
 */
function solo_preprocess_menu_local_task(&$variables): void {
  $variables['link']['#options']['attributes']['class'][] = 'tabs__link';
  $variables['link']['#options']['attributes']['class'][] = 'js-tabs-link';

  // Ensure is-active class is set when the tab is active. The generic active
  // link handler applies stricter comparison rules than what is necessary for
  // tabs.
  if (isset($variables['is_active']) && $variables['is_active'] === TRUE) {
    $variables['link']['#options']['attributes']['class'][] = 'is-active';
  }

  if (isset($variables['element']['#level'])) {
    $variables['level'] = $variables['element']['#level'];
  }

  // Retrieve the route name from the link.
  $route_name = $variables['element']['#link']['url']->getRouteName();

  // Based on the route name, add a unique class to the link.
  switch ($route_name) {
    case 'entity.node.canonical':
      $variables['link']['#options']['attributes']['class'][] = 'view-link';
      break;

    case 'entity.node.edit_form':
      $variables['link']['#options']['attributes']['class'][] = 'edit-link';
      break;

    case 'entity.node.delete_form':
      $variables['link']['#options']['attributes']['class'][] = 'delete-link';
      break;

    case 'entity.node.version_history':
      $variables['link']['#options']['attributes']['class'][] = 'revisions-link';
      break;

    // Add more cases as needed for other links.
  }

}

/**
 * Implements hook_preprocess_HOOK() for field--comment.html.twig.
 */
function solo_preprocess_field__comment(&$variables): void {
  // Add a comment_count.
  $variables['comment_count'] = count(array_filter($variables['comments'], 'is_numeric', ARRAY_FILTER_USE_KEY));

  // Add user.compact to field-comment if profile's avatar of current user
  // exist.
  $user = \Drupal::currentUser();
  if ($user->isAuthenticated() && $user instanceof UserInterface) {
    if ($user->hasField('user_picture') && !$user->get('user_picture')->isEmpty()) {
      $variables['user_picture'] = \Drupal::entityTypeManager()
        ->getViewBuilder('user')
        ->view($user, 'compact');
    }

    $variables['#cache']['contexts'][] = 'user';
  }
}

/**
 * Helper functions.
 */
function _set_site_name(&$variables): void {
  // By default, site_name is set to Drupal if no db connection is available
  // or during site installation. Setting site_name to an empty string makes
  // the site and update pages look cleaner.
  // @see template_preprocess_maintenance_page
  if (!$variables['db_is_active']) {
    $variables['site_name'] = '';
  }
}

/**
 * Helper functions.
 */
function _maintenance_library(&$variables): void {
  // Solo has custom styling for the maintenance page.
  $variables['#attached']['library'][] = 'solo/templates/layout/maintenance_page';
}

/**
 * Implements hook_preprocess_HOOK() for maintenance-page.html.twig.
 */
function solo_preprocess_maintenance_page(&$variables): void {
  _set_site_name($variables);
  _maintenance_library($variables);
}
