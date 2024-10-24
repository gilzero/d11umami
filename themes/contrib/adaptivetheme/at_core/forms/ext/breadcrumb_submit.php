<?php

/**
 * @file
 * Save Breadcrumb CSS to file.
 */

use Drupal\Component\Utility\Html;
use Drupal\Core\File\FileSystemInterface;

/**
 *
 */
function at_core_submit_breadcrumb($values, $generated_files_path) {
  if (!empty($values['settings_breadcrumb_separator'])) {
    $breadcrumb_separator = Html::escape(trim($values['settings_breadcrumb_separator']));
    $css = '.breadcrumb__list-item:before {content: "' . $breadcrumb_separator . '"} .fa-loaded .breadcrumb__list-item:before {content: "' . $breadcrumb_separator . '"}';
  }
  if (!empty($css)) {
    $file_name = 'breadcrumb.css';
    $filepath = $generated_files_path . '/' . $file_name;
    /*file_unmanaged_save_data($css, $filepath, FILE_EXISTS_REPLACE);*/
    \Drupal::service('file_system')->saveData($css, $filepath, FileSystemInterface::EXISTS_REPLACE);
  }
}
