<?php

/**
 * @file
 * Output formatted CSS for fonts.
 */

use Drupal\at_core\Ext\ExtGet;
use Drupal\Core\File\FileSystemInterface;

/**
 * Convert the table row array to settings the rest of the system can use.
 */
function at_convert_image_alignment_settings($values) {
  $settings = [];
  foreach ($values as $row_key => $row_values) {
    foreach ($row_values['options'] as $alignment_item => $alignment) {
      $settings[$alignment_item] = $alignment;
    }
    foreach ($row_values['margins'] as $margin_item => $margin) {
      $settings[$margin_item] = $margin;
    }
  }

  return $settings;
}

/**
 *
 */
function at_core_submit_images($values, $generated_files_path) {
  $converted_values = at_convert_image_alignment_settings($values['table_image_align']);

  // Get stuff.
  $ext_get = new ExtGet();
  $entity_types = $ext_get->getEntityTypes();
  $view_modes = $ext_get->getViewModes();

  $image_breakpoints = \Drupal::service('breakpoint.manager')->getBreakpointsByGroup($values['settings_breakpoint_group_images']);
  $generated_files_path = $values['settings_generated_files_path'];
  $css = [];

  if (!empty($entity_types)) {
    foreach ($entity_types as $entity_type_key => $entity_type_values) {

      foreach ($entity_type_values as $evk => $etv) {
        if ($entity_type_key === 'paragraphs' || $entity_type_key === 'comment' || $entity_type_key === 'block_content') {
          $entity_type_id = $etv->id();
        }
        elseif ($entity_type_key === 'node') {
          $entity_type_id = $etv->get('type');
        }

        // Entity type selector parts.
        if ($entity_type_key === 'node' || $entity_type_key === 'comment') {
          $entity_type_class = $entity_type_key;
        }
        if ($entity_type_key === 'block_content') {
          $entity_type_class = 'block-content';
        }
        if ($entity_type_key === 'paragraphs') {
          $entity_type_class = 'paragraph';
        }

        if (!empty($image_breakpoints)) {
          foreach ($image_breakpoints as $image_breakpoint_id => $image_breakpoint_value) {
            $breakpoint_ia_label = $image_breakpoint_value->getLabel();
            $breakpoint_ia_mediaquery = $image_breakpoint_value->getMediaQuery();
            $breakpoint_ia_key = strtolower(preg_replace("/\W|_/", "", $breakpoint_ia_label));

            foreach ($view_modes[$entity_type_key] as $display_mode) {
              // Display mode strings.
              $display_mode_id = str_replace('.', '_', $display_mode['id']);
              $display_mode_selector_part = str_replace('.', '', strstr($display_mode['id'], '.'));

              // Alignment.
              $alignment = $converted_values['settings_image_alignment_' . $entity_type_id . '_' . $breakpoint_ia_key . '_' . $display_mode_id];

              // Alignment properties.
              $alignment_property = 'float';
              if ($alignment === 'center') {
                $alignment_property = 'text-align';
              }

              // Build margins in trbl.
              $margin['top']    = $converted_values['settings_image_alignment_' . $entity_type_id . '_' . $breakpoint_ia_key . '_' . $display_mode_id . '_top'];
              $margin['right']  = $converted_values['settings_image_alignment_' . $entity_type_id . '_' . $breakpoint_ia_key . '_' . $display_mode_id . '_right'];
              $margin['bottom'] = $converted_values['settings_image_alignment_' . $entity_type_id . '_' . $breakpoint_ia_key . '_' . $display_mode_id . '_bottom'];
              $margin['left']   = $converted_values['settings_image_alignment_' . $entity_type_id . '_' . $breakpoint_ia_key . '_' . $display_mode_id . '_left'];

              // Overwrite center aligned margin left and right, these need
              // to be auto.
              if ($alignment === 'center') {
                $margin['right'] = 'auto';
                $margin['left'] = 'auto';
              }

              foreach ($margin as $margin_key => $margin_value) {
                $margin_unit = '';
                if ($margin_value !== 'auto') {
                  if ($margin_value >= 1) {
                    $margin_unit = 'rem';
                  }
                  $set_margins[$margin_key] = $margin_value / 16 . $margin_unit;
                }
                elseif ($margin_value == 'auto') {
                  $set_margins[$margin_key] = $margin_value;
                }
              }

              // Build CSS output.
              if (isset($alignment) && !empty($alignment)) {
                $css[$entity_type_key . $entity_type_id . $breakpoint_ia_key . $display_mode_id]['css'] =
                  '@media ' . $breakpoint_ia_mediaquery . ' {' .
                  "\n" . '  .' . $entity_type_class . '--type-' . $entity_type_id . '.' . $entity_type_class . '--view-mode-' . $display_mode_selector_part . ' .field-type-image__figure { ' .
                  "\n" . '  ' . $alignment_property . ': ' . $alignment . ';' .
                  "\n" . '  margin: ' . implode(' ', $set_margins) . ';' .
                  "\n" . ' }' .
                  "\n" . '}';
              }
            }
          }
        }
      }
    }
  }

  if (!empty($css)) {
    // Implode into something we can print in a file.
    $output = implode("\n", array_map(function ($entry) {
      return $entry['css'];
    }, $css));
    $file_name = 'image-styles.css';
    $filepath = $generated_files_path . '/' . $file_name;
    /*file_unmanaged_save_data($output, $filepath, FILE_EXISTS_REPLACE);*/
    \Drupal::service('file_system')->saveData($output, $filepath, FileSystemInterface::EXISTS_REPLACE);
  }

  // Return the converted values for config.
  $values = array_merge($converted_values, $values);
  return $values;
}
