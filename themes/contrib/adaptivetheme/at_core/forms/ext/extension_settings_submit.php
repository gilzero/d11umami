<?php

/**
 * @file
 * Process extension settings submits.
 */

use Drupal\Core\Form\FormStateInterface;
use Drupal\at_core\Theme\ThemeSettingsConfig;
use Drupal\Core\Extension\ThemeExtensionList;

/**
 * Form submit handler for the Extension settings.
 *
 * @param $form
 * @param $form_state
 */
function at_core_submit_extension_settings(&$form, FormStateInterface &$form_state) {
  $build_info = $form_state->getBuildInfo();
  $values = $form_state->getValues();
  $theme = $build_info['args'][0];
  $at_core_path = \Drupal::service('extension.list.theme')->getPath('at_core');

  // Don't let this timeout easily.
  set_time_limit(60);

  // Path to save generated CSS files.
  $generated_files_path = $values['settings_generated_files_path'];

  if ($values['settings_enable_extensions'] === 1) {

    // Require submit handlers and helper functions for extensions.
    if ((isset($values['settings_enable_fonts']) && $values['settings_enable_fonts'] === 1) || (isset($values['settings_enable_titles']) && $values['settings_enable_titles'] === 1)) {
      require_once $at_core_path . '/forms/ext/fonts.inc';
      require_once $at_core_path . '/forms/ext/fonts_submit.php';
      require_once $at_core_path . '/forms/ext/titles_submit.php';
    }

    // Submit handler for Fonts.
    if (isset($values['settings_enable_fonts']) && $values['settings_enable_fonts'] === 1) {
      // Returns modified values to pass in new settings for typekit and google fonts.
      $values = at_core_submit_fonts($values, $generated_files_path);
    }

    // Submit handler for Images.
    if (isset($values['settings_enable_images']) && $values['settings_enable_images'] === 1) {
      // Generate image alignment CSS.
      require_once $at_core_path . '/forms/ext/images_submit.php';
      $values = at_core_submit_images($values, $generated_files_path);
    }

    // Submit handler for Titles.
    if (isset($values['settings_enable_titles']) && $values['settings_enable_titles'] === 1) {
      at_core_submit_titles($values, $generated_files_path);
    }

    // Submit handler for Markup Overrides.
    if (isset($values['settings_enable_markup_overrides']) && $values['settings_enable_markup_overrides'] === 1) {

      // Breadcrumbs.
      if (!empty($values['settings_breadcrumb_separator'])) {
        require_once $at_core_path . '/forms/ext/breadcrumb_submit.php';
        at_core_submit_breadcrumb($values, $generated_files_path);
      }
    }

    // Submit handler for Mobile blocks.
    if (isset($values['settings_enable_mobile_blocks']) && $values['settings_enable_mobile_blocks'] === 1) {
      require_once $at_core_path . '/forms/ext/mobile_blocks_submit.php';
      at_core_submit_mobile_blocks($values, $theme, $generated_files_path);
    }

    // Submit handler for Custom CSS.
    if (isset($values['settings_enable_custom_css']) && $values['settings_enable_custom_css'] === 1) {
      require_once $at_core_path . '/forms/ext/custom_css_submit.php';
      at_core_submit_custom_css($values, $generated_files_path);
    }
  }

  // Flush caches. I really, really tried to avoid this, but if you know a better
  // way of always clearing twig, CSS and the registry?
  drupal_flush_all_caches();

  // Manage settings and configuration.
  // Must get mutable config otherwise bad things happen.
  $config = \Drupal::configFactory()->getEditable($theme . '.settings');
  $convertToConfig = new ThemeSettingsConfig();
  $convertToConfig->settingsExtensionsConvertToConfig($values, $config);

  \Drupal::messenger()->addMessage(t('Extensions configuration saved.'), 'status');
}
