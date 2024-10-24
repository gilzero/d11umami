<?php

/**
 * @file
 */

/**
 * Validate form values.
 *
 * @param $form
 * @param $form_state
 */

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Extension\ThemeExtensionList;

/**
 *
 */
function at_core_validate_extension_settings(&$form, FormStateInterface &$form_state) {
  $build_info = $form_state->getBuildInfo();
  $values = $form_state->getValues();
  $theme = $build_info['args'][0];
  $at_core_path = \Drupal::service('extension.list.theme')->getPath('at_core');

  if ($values['settings_enable_extensions'] === 1) {
    // Validation handler for Fonts.
    if (isset($values['settings_enable_fonts']) && $values['settings_enable_fonts'] === 1) {
      require_once $at_core_path . '/forms/ext/fonts_validate.php';
      at_core_validate_fonts($form, $form_state);
    }
  }
}
