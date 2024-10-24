<?php

/**
 * @file
 * Theme Setting.
 */

/**
 * Helper function.
 */
function _load_google_fonts(array &$attachments): void {
  $fonts = ['site_font_google_global_on', 'site_font_google_heading_on'];
  foreach ($fonts as $font) {
    if (theme_get_setting($font)) {
      $font_setting = theme_get_setting($font);
      $font_path = 'solo/solo-gf' . (theme_get_setting('site_font_google_local_on') ? 'in' : 'ex') . substr($font_setting, 4);
      $attachments['#attached']['library'][] = $font_path;
    }
  }
}

/**
 * Helper function.
 */
function _load_special_fonts(array &$attachments): void {
  if (theme_get_setting('site_font_google_special_on')) {
    $ex_font_s = theme_get_setting('site_font_google_special_on');
    $current_exgfs = 'solo/solo-gfex' . substr($ex_font_s, 4);
    $attachments['#attached']['library'][] = $current_exgfs;
  }
}

/**
 * Helper function.
 */
function _load_site_name_fonts(array &$attachments): void {
  if (theme_get_setting('site_name_google_font')) {
    $ex_font_s = theme_get_setting('site_name_google_font');
    $current_exgfs = 'solo/solo-gfex' . substr($ex_font_s, 4);
    $attachments['#attached']['library'][] = $current_exgfs;
  }
}

/**
 * Helper function.
 */
function _load_page_title_fonts(array &$attachments): void {
  if (theme_get_setting('site_pagetitle_google_font')) {
    $ex_font_s = theme_get_setting('site_pagetitle_google_font');
    $current_exgfs = 'solo/solo-gfex' . substr($ex_font_s, 4);
    $attachments['#attached']['library'][] = $current_exgfs;
  }
}

/**
 * Helper function.
 */
function _load_predefined_color_theme(array &$attachments): void {
  // Default library path based on theme settings.
  $theme_category = theme_get_setting('theme_category');
  $is_theme = '';

  if ($theme_category !== 'none') {
    $is_theme_setting = theme_get_setting('predefined_current_theme') ?? '';
    $theme_parts = explode('|', $is_theme_setting);
    $is_theme = count($theme_parts) > 1 ? $theme_parts[1] : $theme_parts[0];
  }

  // Check if the solo_utilities module is installed.
  if (\Drupal::moduleHandler()->moduleExists('solo_utilities')) {
    // Fetch the user-selected library from solo_utilities.
    $user_library = _solo_utilities_get_user_selected_library();

    if (!empty($user_library)) {
      // Attach the user-selected library if it exists.
      $attachments['#attached']['library'][] = 'solo/' . $user_library;
      // Early return if user library is set.
      return;
    }
  }

  // Fallback to the default theme library if no user-selected library.
  $theme_path = 'solo/' . $is_theme;
  $attachments['#attached']['library'][] = $theme_path;
}

/**
 * Helper function.
 */
function _solo_utilities_get_user_selected_library() {
  // Ensure the solo_utilities module is installed.
  if (!\Drupal::moduleHandler()->moduleExists('solo_utilities')) {
    return NULL;
  }

  // Create an instance of LibraryLoadNegotiator and call evaluateRules.
  $color_schemes_negotiator = \Drupal::service('solo_utilities.color_schemes_negotiator');

  // Call the evaluateRules method to get the library.
  $selected_library = $color_schemes_negotiator->evaluateRules();

  return $selected_library;
}

/**
 * Helper function.
 */
function _load_font_awesome(array &$attachments): void {
  $font_awesome_versions = [
    1 => 'font-awesome-4-in',
    2 => 'font-awesome-4-ex',
    3 => 'font-awesome-5-ex',
    4 => 'font-awesome-6-ex',
  ];
  $fw_version = theme_get_setting('site_font_awesome_on');
  if (isset($font_awesome_versions[$fw_version])) {
    $attachments['#attached']['library'][] = 'solo/' . $font_awesome_versions[$fw_version];
  }
}

/**
 * Helper function.
 */
function _load_full_opacity(array &$attachments): void {
  /** @var \Drupal\Core\Routing\AdminContext $admin_context */
  $admin_context = \Drupal::service('router.admin_context');
  if (theme_get_setting('site_opacity_onscroll_on') && !$admin_context->isAdminRoute()) {
    $attachments['#attached']['library'][] = 'solo/solo-full-opacity';
  }
}

/**
 * Helper function.
 */
function _site_load_w3css_library(array &$attachments): void {
  if (theme_get_setting('site_load_w3css_on')) {
    $attachments['#attached']['library'][] = 'solo/w3-css';
  }
}

/**
 * Helper function.
 */
function _load_color_coded_system_tabs(array &$attachments): void {
  if (theme_get_setting('color_coded_system_tabs')) {
    $attachments['#attached']['library'][] = 'solo/solo-color-coded-system-tabs';
  }
}

/**
 * Implements hook_page_attachments_alter().
 */
function solo_page_attachments_alter(array &$attachments): void {
  _load_google_fonts($attachments);
  _load_special_fonts($attachments);
  _load_site_name_fonts($attachments);
  _load_page_title_fonts($attachments);
  _load_predefined_color_theme($attachments);
  _load_font_awesome($attachments);
  _load_full_opacity($attachments);
  _site_load_w3css_library($attachments);
  _load_color_coded_system_tabs($attachments);
}

/**
 * Helper function.
 */
function _solo_convert_hex_to_hsl(string $hex_string): array {
  // Convert hexcode pairs to rgb values (0-255).
  $hex_val = trim($hex_string, '#');
  $r0 = hexdec($hex_val[0] . $hex_val[1]);
  $g0 = hexdec($hex_val[2] . $hex_val[3]);
  $b0 = hexdec($hex_val[4] . $hex_val[5]);

  // Convert rgb's 0-255 to decimal values.
  $r = fdiv($r0, 255);
  $g = fdiv($g0, 255);
  $b = fdiv($b0, 255);

  // Calculate Hue.
  $c_min = min($r, $g, $b);
  $c_max = max($r, $g, $b);
  $delta = $c_max - $c_min;

  if ($delta == 0) {
    $h = 0;
  }
  else {
    switch ($c_max) {
      case $r:
        $h = fmod((($g - $b) / $delta), 6);
        break;

      case $g:
        $h = (($b - $r) / $delta) + 2;
        break;

      case $b:
        $h = (($r - $g) / $delta) + 4;
        break;

      default:
        $h = 0;
        break;
    }
  }

  $h = round($h * 60);

  // Shift hue range from [-60 - 300] to [0 - 360].
  if ($h < 0) {
    $h += 360;
  }

  // Calculate Lightness.
  $l = ($c_max + $c_min) / 2;

  // Calculate Saturation.
  $s = $delta == 0 ? 0 : $delta / (1 - abs((2 * $l) - 1));

  // Convert Saturation and Lightness to percentages.
  return [round($h), round($s * 100) . '%', round($l * 100) . '%'];
}
