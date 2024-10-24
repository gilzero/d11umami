<?php

/**
 * @file
 * Custom theme settings.
 */

use Drupal\Component\Utility\Html;
use Drupal\Core\Form\FormStateInterface;
use Drupal\at_core\Theme\ThemeInfo;
use Drupal\at_core\File\FileOperations;
use Drupal\at_core\File\DirectoryOperations;
use Drupal\at_core\Ext\ExtGet;
use Drupal\block\BlockRepositoryInterface;
use Drupal\Core\Extension\ThemeExtensionList;

/**
 * Implementation of hook_form_system_theme_settings_alter()
 *
 * @param $form
 *   Nested array of form elements that comprise the form.
 * @param $form_state
 *   A keyed array containing the current state of the form.
 */
function at_core_form_system_theme_settings_alter(&$form, FormStateInterface $form_state, $form_id = NULL) {
  // Work-around for a core bug affecting admin themes. See issue #943212.
  if (isset($form_id)) {
    return;
  }

  // Set the theme name.
  $build_info = $form_state->getBuildInfo();

  $active_theme = \Drupal::theme()->getActiveTheme();
  $theme = $active_theme->getName();
  $theme_extension = $active_theme->getExtension();

  // Instantiate our Theme info object.
  $themeInfo = new ThemeInfo($theme);
  $getThemeInfo = $themeInfo->getThemeInfo('info');

  // Get this themes config settings.
  $config = \Drupal::config($theme . '.settings')->get('settings');

  // Common paths.
  $at_core_path = \Drupal::service('extension.list.theme')->getPath('at_core');
  $subtheme_path = \Drupal::service('extension.list.theme')->getPath($theme);
  $generated_files_path = NULL;

  // Path to save generated CSS files. We don't want this happening for at_core or the generator.
  if (isset($getThemeInfo['subtheme type']) && ($getThemeInfo['subtheme type'] === 'adaptive_subtheme')) {
    $directoryOperations = new DirectoryOperations();
    $generated_files_path = $directoryOperations->directoryPrepare($backup_file_path = [$subtheme_path, 'styles/css/generated']);
  }

  // Get the active themes regions so we can use this in
  // various other places.
  // $theme_regions = system_region_list($theme, $show = REGIONS_VISIBLE);.
  $theme_regions = system_region_list($theme, $show = BlockRepositoryInterface::REGIONS_VISIBLE);

  // Set variables if modules exist.
  $modules = ['node', 'comment', 'block', 'block_content', 'paragraphs', 'breakpoint', 'at_tool'];
  $module_exists = [];
  foreach ($modules as $module) {
    $module_exists[$module] = FALSE;
    if (\Drupal::moduleHandler()->moduleExists($module) === TRUE) {
      $module_exists[$module] = TRUE;
    }
  }

  // Modules exist BC.
  $node_module = $module_exists['node'];
  $comment_module = $module_exists['comment'];
  $block_module = $module_exists['block'];
  $block_content_module = $module_exists['block_content'];
  $paragraphs_module = $module_exists['paragraphs'];
  $breakpoint_module = $module_exists['breakpoint'];
  $at_tools_module = $module_exists['at_tool'];

  // Get stuff.
  $ext_get = new ExtGet();

  // Active theme blocks.
  $theme_blocks = $ext_get->getActiveThemeBlocks($theme);
  // kint($theme_blocks);

  // Breakpoints.
  $break_points = $ext_get->getBreakPoints();
  $breakpoint_groups = $break_points['breakpoint_groups'];
  $breakpoints = $break_points['breakpoints'];
  $breakpoint_options = $break_points['breakpoint_options'];

  // Entity types.
  $entity_types = $ext_get->getEntityTypes();

  // Set easy access variables for entity types.
  if (!empty($entity_types)) {
    if (isset($entity_types['node'])) {
      $node_types = $entity_types['node'];
    }
    if (isset($entity_types['comment'])) {
      $comment_types = $entity_types['comment'];
    }
    if (isset($entity_types['block_content'])) {
      $block_content_types = $entity_types['block_content'];
    }
    if (isset($entity_types['paragraphs'])) {
      $paragraph_types = $entity_types['paragraphs'];
    }
  }

  // View modes.
  $view_modes = $ext_get->getViewModes();

  // Image Styles.
  $image_styles = $ext_get->getImageStyles();

  // Set a class on the form for the current admin theme, note if this is set to
  // "Default theme" the result is always 0.
  $system_theme_config = \Drupal::config('system.theme');
  $admin_theme = $system_theme_config->get('admin');
  if (!empty($admin_theme)) {
    $admin_theme_class = 'admin-theme--' . Html::cleanCssIdentifier($admin_theme);
    $form['#attributes'] = ['class' => [$admin_theme_class]];
  }

  // Attached required CSS and JS.
  $form['#attached']['library'][] = 'at_core/at.appearance_settings';

  // Display a rude message if AT Tools is missing...
  if ($at_tools_module === FALSE) {
    \Drupal::messenger()->addMessage(t('Please install the <a href="@at_tools_href" target="_blank">AT Tool</a> module for Drupal 8/9. Your theme may not operate correctly without this module installed.', ['@at_tools_href' => 'https://www.drupal.org/project/at_tool']), 'warning');
  }

  // AT Core.
  if ($theme === 'at_core') {
    $form['at_core']['message'] = [
      '#type' => 'container',
      '#markup' => t('AT Core has no configuration and cannot be used as a front end theme - it is a base them only. Use the <b>AT Theme Generator</b> to generate or clone a theme to get started.'),
    ];

    // Hide form items.
    $form['theme_settings']['#attributes']['class'] = ['visually-hidden'];
    $form['logo']['#attributes']['class'] = ['visually-hidden'];
    $form['favicon']['#attributes']['class'] = ['visually-hidden'];
    $form['actions']['#attributes']['class'] = ['visually-hidden'];
  }

  // AT Subtheme.
  if (isset($getThemeInfo['subtheme type'])) {

    // BC layer for older themes.
    if ($getThemeInfo['subtheme type'] !== 'adaptive_generator') {

      // Pass in the generated files path to values and settings.
      $form['at']['settings_generated_files_path'] = [
        '#type' => 'hidden',
        '#value' => $generated_files_path,
      ];

      // Theme info block.
      require_once $at_core_path . '/forms/theme_info.php';

      // Check for breakpoint module, a lot of errors without it, this is brutal.
      if ($breakpoint_module === TRUE) {
        if ($getThemeInfo['subtheme type'] === 'adaptive_subtheme') {
          require_once $at_core_path . '/forms/ext/extension_settings.php';
          require_once $at_core_path . '/forms/layout/layouts.php';
        }
        elseif ($getThemeInfo['subtheme type'] === 'adaptive_skin') {
          require_once $at_core_path . '/forms/ext/extension_settings_skin.php';
        }
      }

      // Basic settings - move into details wrapper and collapse.
      $form['basic_settings'] = [
        '#type' => 'details',
        '#title' => t('Basic Settings'),
      ];

      // If it's skin type, set details to open.
      if ($getThemeInfo['subtheme type'] === 'adaptive_skin') {
        $form['basic_settings']['#open'] = TRUE;
      }

      $form['theme_settings']['#open'] = FALSE;
      $form['theme_settings']['#group'] = 'basic_settings';
      $form['logo']['#open'] = FALSE;
      $form['logo']['#group'] = 'basic_settings';
      $form['favicon']['#open'] = FALSE;
      $form['favicon']['#group'] = 'basic_settings';

      // Buttons don't work with #group, move it the hard way.
      $form['actions']['#type'] = $form['basic_settings']['actions']['#type'] = 'actions';
      $form['actions']['submit']['#type'] = $form['basic_settings']['actions']['submit']['#type'] = 'submit';
      $form['actions']['submit']['#value'] = $form['basic_settings']['actions']['submit']['#value'] = t('Save basic settings');
      $form['actions']['submit']['#button_type'] = $form['basic_settings']['actions']['submit']['#button_type'] = 'primary';
      unset($form['actions']);
    }
  }

  // Modify the color scheme form.
  if (\Drupal::moduleHandler()->moduleExists('color')) {
    include_once $at_core_path . '/forms/color/color_submit.php';
    if (isset($build_info['args'][0]) && ($theme = $build_info['args'][0]) && color_get_info($theme) && function_exists('gd_info')) {
      $form['#process'][] = 'at_core_color_form';
    }
    // TODO This should only happen after color form submit. We need this to
    // stop 404 errors for the map URL for rewritten color stylesheets.
    $color_paths = \Drupal::config('color.theme.' . $theme)->get('stylesheets');
    if (!empty($color_paths)) {
      $fileOperations = new FileOperations();
      foreach ($color_paths as $color_path) {
        $map_string = '/*# sourceMappingURL=maps/' . str_replace('.css', '.css.map', basename($color_path)) . ' */';
        $fileOperations->fileStrReplace("$color_path", $map_string, '');
      }
    }
  }
}

/**
 * Helper function to modify the color scheme form.
 *
 * @param $form
 *
 * @return array $form
 */
function at_core_color_form($form) {
  $form['color']['#open'] = FALSE;
  $form['color']['actions'] = [
    '#type' => 'actions',
    '#attributes' => ['class' => ['submit--color-scheme']],
  ];
  $form['color']['actions']['submit'] = [
    '#type' => 'submit',
    '#value' => t('Save color scheme'),
    '#button_type' => 'primary',
    '#submit' => ['at_color_scheme_form_submit'],
    '#weight' => 100,
  ];
  $form['color']['actions']['log'] = [
    '#type' => 'submit',
    '#value' => t('Log color scheme'),
    '#submit' => ['at_core_log_color_scheme'],
    '#weight' => 101,
    '#access' => FALSE,
  ];

  // Magic user Obi Wan gets special Jedi powers.
  $user = \Drupal::currentUser();
  if (in_array('administrator', $user->getRoles()) && $user->getAccountName() == 'Obi Wan') {
    $form['color']['actions']['log']['#access'] = TRUE;
  }

  return $form;
}
