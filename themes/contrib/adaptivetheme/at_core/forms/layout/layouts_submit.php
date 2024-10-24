<?php

/**
 * @file
 * Submit layouts.
 */

use Drupal\Component\Utility\Unicode;
use Drupal\at_core\Theme\ThemeSettingsConfig;
use Drupal\at_core\Layout\LayoutSubmit;
use Drupal\Core\Extension\ThemeExtensionList;

/**
 * Form submit handler for the Layout settings.
 *
 * @param $form
 * @param $form_state
 */
function at_core_submit_layouts(&$form, &$form_state) {
  $build_info = $form_state->getBuildInfo();
  $values = $form_state->getValues();
  $theme = $build_info['args'][0];

  // Don't let this timeout easily.
  set_time_limit(60);

  // Generate and save a new layout.
  if (isset($values['settings_layouts_enable']) && $values['settings_layouts_enable'] == 1) {
    $generateLayout = new LayoutSubmit($theme, $values);

    // Update the themes info file with new regions.
    $generateLayout->saveLayoutRegions();

    // Build and save the suggestions layout css files.
    $generateLayout->saveLayoutSuggestionsCSS();

    // Build and save the suggestions twig templates.
    $generateLayout->saveLayoutSuggestionsMarkup();

    // Merge in row order (weight) settings.
    $converted_layout_settings = $generateLayout->convertLayoutSettings();
    if (!empty($converted_layout_settings)) {
      $values = array_merge($converted_layout_settings, $values);
    }

    // Add a new suggestion to the page suggestions array in config.
    if (!empty($values['ts_name'])) {
      $suggestion = trim($values['ts_name']);
      $clean_suggestion = str_replace('-', '_', $suggestion);
      $values["settings_suggestion_page__$clean_suggestion"] = $clean_suggestion;
    }

    // Delete suggestion files.
    $templates_directory = \Drupal::service('extension.list.theme')->getPath($theme) . '/templates/generated';
    $css_directory = $values['settings_generated_files_path'];
    foreach ($values as $values_key => $values_value) {
      if (substr($values_key, 0, 18) === 'delete_suggestion_') {
        if ($values_value === 1) {
          /*$delete_suggestion_keys[] = Unicode::substr($values_key, 18);*/
          $delete_suggestion_keys[] = mb_substr($values_key, 18);
        }
      }
    }
    if (isset($delete_suggestion_keys)) {
      foreach ($delete_suggestion_keys as $suggestion_to_remove) {
        $formatted_suggestion = str_replace('_', '-', $suggestion_to_remove);
        $template_file_name = $formatted_suggestion . '.html.twig';
        $css_file_name = $theme . '.layout.' . $formatted_suggestion . '.css';
        $template_file_path = "$templates_directory/$template_file_name";
        $css_file_path = "$css_directory/$css_file_name";
        $files_to_delete[] = $template_file_name;
        $files_to_delete[] = $css_file_name;
        if (file_exists($template_file_path)) {
          unlink($template_file_path);
        }
        if (file_exists($css_file_path)) {
          unlink($css_file_path);
        }
      }
    }
  }
  if (isset($files_to_delete)) {
    $deleted_files_message_list = [
      '#theme' => 'item_list',
      '#items' => $files_to_delete,
    ];
    \Drupal::messenger()->addMessage(t('The following <b>files</b> were removed: @removed_files', [
      '@removed_files' => \Drupal::service('renderer')->renderPlain($deleted_files_message_list)
      ]
    ), 'status');
  }

  // Flush caches. I really, really tried to avoid this, but if you know a better
  // way of always clearing twig, CSS and the registry?
  drupal_flush_all_caches();

  // Manage settings and configuration.
  $config = \Drupal::configFactory()->getEditable($theme . '.settings');
  $convertToConfig = new ThemeSettingsConfig();
  $convertToConfig->settingsLayoutConvertToConfig($values, $config);
}
