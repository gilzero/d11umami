<?php

/**
 * @file
 * Save custom CSS to file.
 */

use Drupal\Component\Utility\Xss;
/*use Drupal\Core\File\FileSystem;*/
use Drupal\Core\File\FileSystemInterface;

/**
 *
 */
function at_core_submit_custom_css($values, $generated_files_path) {
  $custom_css = '';
  if (!empty($values['settings_custom_css'])) {
    // Sanitize user entered data.
    $custom_css = Xss::filter($values['settings_custom_css']);
  }

  $file_name = 'custom-css.css';
  $filepath = $generated_files_path . '/' . $file_name;
  \Drupal::service('file_system')->saveData($custom_css, $filepath, FileSystemInterface::EXISTS_REPLACE);
  /*file_unmanaged_save_data($custom_css, $filepath, FILE_EXISTS_REPLACE);*/
  /*file_save_data($custom_css, $filepath, FileSystemInterface::EXISTS_REPLACE);*/
}
