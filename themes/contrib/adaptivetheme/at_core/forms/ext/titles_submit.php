<?php

/**
 * @file
 */

use Drupal\Core\File\FileSystemInterface;

/**
 * Generate title styles.
 *
 * @param $values
 * @param $generated_files_path
 */
function at_core_submit_titles($values, $generated_files_path) {
  // Array of valid title types.
  $titles_valid_types = title_valid_type_options();

  // Get the font elements array.
  $font_elements = font_elements();
  $css = [];

  // Build arrays of selectors with associated styles.
  foreach ($font_elements as $font_element_key => $font_element_value) {
    if (in_array($font_element_key, $titles_valid_types)) {
      $case = ' text-transform: ';
      $weight = ' font-weight: ';
      $alignment = ' text-align: ';
      $letter_spacing = ' letter-spacing: ';

      // Selector.
      if (!empty($font_element_value['selector'])) {
        $css[$font_element_key]['selector'] = $font_element_value['selector'];
      }

      // Case or Font variant: small-caps is a font-variant, set properties and values accordingly.
      // We need to set transform and variant explicitly so selectors can override each other, without
      // any nasty inheritance issues, such as when .page__title overrides h1.
      if (!empty($values['settings_titles_' . $font_element_key . '_case'])) {
        if ($values['settings_titles_' . $font_element_key . '_case'] == 'small-caps') {
          $css[$font_element_key]['styles']['font_variant'] = ' font-variant: ' . $values['settings_titles_' . $font_element_key . '_case'];
          $css[$font_element_key]['styles']['text_transform'] = ' text-transform: none';
        }
        else {
          $css[$font_element_key]['styles']['case'] = $case . $values['settings_titles_' . $font_element_key . '_case'];
          $css[$font_element_key]['styles']['font_variant'] = ' font-variant: normal';
        }
      }
      // Weight.
      if (!empty($values['settings_titles_' . $font_element_key . '_weight'])) {
        $css[$font_element_key]['styles']['weight'] = $weight . $values['settings_titles_' . $font_element_key . '_weight'];
      }
      // Alignment.
      if (!empty($values['settings_titles_' . $font_element_key . '_alignment'])) {
        $css[$font_element_key]['styles']['align'] = $alignment . $values['settings_titles_' . $font_element_key . '_alignment'];
      }
      // Letter spacing.
      if (!empty($values['settings_titles_' . $font_element_key . '_letterspacing'])) {
        $css[$font_element_key]['styles']['letterspacing'] = $letter_spacing . $values['settings_titles_' . $font_element_key . '_letterspacing'] . 'px';
      }
    }
  }

  // Format CSS.
  if (!empty($css)) {
    $output = [];
    foreach ($css as $selector_key => $selector_styles) {
      if (isset($selector_styles['styles'])) {
        $output[] = $selector_styles['selector'] . ' {' . implode(';', $selector_styles['styles']) . '; }';
      }
    }
    if (!empty($output)) {
      // Output data to file.
      $titles_styles = implode("\n", $output);
      if (!empty($titles_styles)) {
        $file_name = 'title-styles.css';
        $filepath = "$generated_files_path/$file_name";
        /*file_unmanaged_save_data($titles_styles, $filepath, FILE_EXISTS_REPLACE);*/
        \Drupal::service('file_system')->saveData($titles_styles, $filepath, FileSystemInterface::EXISTS_REPLACE);
      }
    }
  }
}
