<?php

/**
 * @file
 * Validate fonts form fields.
 */

/**
 *
 */
function at_core_validate_fonts($form, $form_state) {
  $values = $form_state->getValues();
  if (!empty($values['settings_font_google'])) {
    if (empty($values['settings_font_google_names'])) {
      $form_state->setErrorByName('settings_font_google_names', t("Google fonts declared - you need to add the Google font names."));
    }
  }
  if (!empty($values['settings_font_typekit'])) {
    if (empty($values['settings_font_typekit_names'])) {
      $form_state->setErrorByName('settings_font_typekit_names', t("Typekit fonts declared - you need to add the Typekit font names."));
    }
  }
  if (!empty($values['settings_font_local'])) {
    if (empty($values['settings_font_local_names'])) {
      $form_state->setErrorByName('settings_font_local_names', t("Local fonts declared - you need to add the Local font names."));
    }
  }
}
