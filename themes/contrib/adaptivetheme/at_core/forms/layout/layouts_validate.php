<?php

/**
 * @file
 * Validate form values.
 */

/**
 *
 */
function at_core_validate_layouts(&$form, &$form_state) {
  $values = $form_state->getValues();

  // Validate Layout Generator.
  if (isset($values['settings_layouts_enable']) && $values['settings_layouts_enable'] == 1) {

    // Check the user set a value for max width.
    if (isset($values['settings_max_width_enable']) && $values['settings_max_width_enable'] === 1) {
      if (empty($values['settings_max_width_value'])) {
        $form_state->setErrorByName('settings_max_width_value', t("No value entered for the layout max-width setting."));
      }
    }
  }
}
