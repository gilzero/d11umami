<?php

use Drupal\Core\Extension\ThemeExtensionList;

/**
 * @file
 * Generate form elements for the Extension settings.
 */

// Submit handlers for the advanced settings.
include_once \Drupal::service('extension.list.theme')->getPath('at_core') . '/forms/ext/extension_settings_validate.php';
include_once \Drupal::service('extension.list.theme')->getPath('at_core') . '/forms/ext/extension_settings_submit.php';

$settings_extensions_form_open = theme_get_setting('settings.extensions_form_open', $theme);

$form['docs'] = [
  '#type' => 'container',
  '#markup' => t('<a class="at-docs" href=":docs" target="_blank" title="External link: docs.adaptivethemes.com">View online documentation <svg class="docs-ext-link-icon" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1408 928v320q0 119-84.5 203.5t-203.5 84.5h-832q-119 0-203.5-84.5t-84.5-203.5v-832q0-119 84.5-203.5t203.5-84.5h704q14 0 23 9t9 23v64q0 14-9 23t-23 9h-704q-66 0-113 47t-47 113v832q0 66 47 113t113 47h832q66 0 113-47t47-113v-320q0-14 9-23t23-9h64q14 0 23 9t9 23zm384-864v512q0 26-19 45t-45 19-45-19l-176-176-652 652q-10 10-23 10t-23-10l-114-114q-10-10-10-23t10-23l652-652-176-176q-19-19-19-45t19-45 45-19h512q26 0 45 19t19 45z"/></svg></a>', [':docs' => ' //docs.adaptivethemes.com/']),
  '#weight' => -1000,
];

$form['extensions'] = [
  '#type' => 'details',
  '#title' => t('Extensions'),
  '#weight' => -201,
  '#open' => $settings_extensions_form_open,
  '#attributes' => ['class' => ['extension-settings', 'clearfix']],
];

// Enable extensions, the extension settings are hidden by default to ease the
// the UI clutter, this setting is also used as a global enable/disable for any
// extension in other logical operations.
$form['extensions']['extensions-enable-container'] = [
  '#type' => 'container',
  '#attributes' => ['class' => ['subsystem-enabled-container', 'layouts-column-onequarter']],
];

$form['extensions']['extensions-enable-container']['settings_extensions_form_open'] = [
  '#type' => 'checkbox',
  '#title' => t('Keep open'),
  '#default_value' => $settings_extensions_form_open,
  '#states' => [
    'disabled' => ['input[name="settings_enable_extensions"]' => ['checked' => FALSE]],
  ],
];

$form['extensions']['extensions-enable-container']['settings_enable_extensions'] = [
  '#type' => 'checkbox',
  '#title' => t('Enable'),
  '#default_value' => theme_get_setting('settings.enable_extensions', $theme),
];

$form['extensions']['extension_settings'] = [
  '#type' => 'vertical_tabs',
  '#attributes' => ['class' => ['clearfix']],
  '#states' => [
    'visible' => [':input[name="settings_enable_extensions"]' => ['checked' => TRUE]],
  ],
];

// Extensions.
$form['enable_extensions'] = [
  '#type' => 'details',
  '#title' => t('Enable extensions'),
  '#group' => 'extension_settings',
];

$form['enable_extensions']['description'] = [
  '#markup' => t('<p>Extensions are settings for configuring and styling your site. Enabled extensions appear in new vertical tabs.</p>'),
];

// Responsive Menus.
$form['enable_extensions']['settings_enable_responsive_menus'] = [
  '#type' => 'checkbox',
  '#title' => t('Responsive menus'),
  '#description' => t('Select responsive menu styles and breakpoints.'),
  '#default_value' => theme_get_setting('settings.enable_responsive_menus', $theme),
];

// Image alignment and captions.
$form['enable_extensions']['settings_enable_images'] = [
  '#type' => 'checkbox',
  '#title' => t('Image alignment and captions'),
  '#default_value' => theme_get_setting('settings.enable_images', $theme),
  '#description' => t('Set image alignment, captions and teaser view per content type.'),
];

// Touch icons.
$form['enable_extensions']['settings_enable_touch_icons'] = [
  '#type' => 'checkbox',
  '#title' => t('Touch icons'),
  '#description' => t('Add touch icon meta tags. A default set of icons are located in <code>@touchiconpath</code>.', ['@touchiconpath' => $subtheme_path . '/images/touch-icons/']),
  '#default_value' => theme_get_setting('settings.enable_touch_icons', $theme),
];

// Fonts.
$form['enable_extensions']['settings_enable_fonts'] = [
  '#type' => 'checkbox',
  '#title' => t('Fonts'),
  '#default_value' => theme_get_setting('settings.enable_fonts', $theme),
  '#description' => t('Apply fonts to site elements. Supports <a href=":gflink" target="_blank">Google</a> and <a href=":tklink" target="_blank">Typekit</a> fonts, as well as standard websafe fonts.', [':tklink' => 'https://typekit.com/', ':gflink' => 'https://fonts.google.com/']),
];

// Title styles.
$form['enable_extensions']['settings_enable_titles'] = [
  '#type' => 'checkbox',
  '#title' => t('Titles'),
  '#default_value' => theme_get_setting('settings.enable_titles', $theme),
  '#description' => t('Set case, weight, alignment and letter-spacing for titles (headings).'),
];

// Shortcodes.
$form['enable_extensions']['settings_enable_shortcodes'] = [
  '#type' => 'checkbox',
  '#title' => t('Shortcode CSS Classes'),
  '#description' => t('Adjust and enhance theme styles with pre-styled CSS classes.'),
  '#default_value' => theme_get_setting('settings.enable_shortcodes', $theme),
];

// Slideshows.
$form['enable_extensions']['settings_enable_slideshows'] = [
  '#type' => 'checkbox',
  '#title' => t('Slideshows'),
  '#description' => t('Enable slideshows and configure settings.'),
  '#default_value' => theme_get_setting('settings.enable_slideshows', $theme),
];

// Mobile blocks.
$form['enable_extensions']['settings_enable_mobile_blocks'] = [
  '#type' => 'checkbox',
  '#title' => t('Mobile Blocks'),
  '#description' => t('Show or hide blocks in mobile devices.'),
  '#default_value' => theme_get_setting('settings.enable_mobile_blocks', $theme),
];

// Custom CSS.
$form['enable_extensions']['settings_enable_custom_css'] = [
  '#type' => 'checkbox',
  '#title' => t('Custom CSS'),
  '#description' => t('Enter custom CSS rules for minor adjustment to your theme.'),
  '#default_value' => theme_get_setting('settings.enable_custom_css', $theme),
];

// CKEditor
// Check if theme is Mimic compatible.
if (theme_get_setting('settings.mimic_compatible', $theme) === 1) {
  $form['enable_extensions']['settings_enable_ckeditor'] = [
    '#type' => 'checkbox',
    '#title' => t('CKEditor Skin'),
    '#description' => t('Select CKEditor skin.'),
    '#default_value' => theme_get_setting('settings.enable_ckeditor', $theme),
  ];
}

// Devel.
$form['enable_extensions']['settings_enable_devel'] = [
  '#type' => 'checkbox',
  '#title' => t('Developer tools'),
  '#description' => t('Settings to help with theme development.'),
  '#default_value' => theme_get_setting('settings.enable_devel', $theme),
];

// Legacy browsers.
$form['enable_extensions']['settings_enable_legacy_browsers'] = [
  '#type' => 'checkbox',
  '#title' => t('Legacy browsers'),
  '#description' => t('Settings to support crappy old browsers like IE8. Use with caution, do not enable this unless you really, really need it.'),
  '#default_value' => theme_get_setting('settings.enable_legacy_browsers', $theme),
];

// Markup overrides.
$form['enable_extensions']['settings_enable_markup_overrides'] = [
  '#type' => 'checkbox',
  '#title' => t('Markup overrides'),
  '#description' => [
    '#theme' => 'item_list',
    '#list_type' => 'ul',
    '#attributes' => ['class' => ['markup-overrides-desc']],
    '#items' => [
      t('Responsive tables'),
      t('Breadcrumbs'),
      t('Search block'),
      t('Login block'),
      t('Comment titles'),
      t('Feed icons'),
      t('Skip link'),
      t('Attribution'),
    ],
  ],
  '#default_value' => theme_get_setting('settings.enable_markup_overrides', $theme),
];

// Extensions master toggle.
if (theme_get_setting('settings.enable_extensions', $theme) == 1) {

  // Include fonts.inc by default.
  include_once $at_core_path . '/forms/ext/fonts.inc';

  $extensions_array = [
    'responsive_menus',
    'fonts',
    'titles',
    'images',
    'touch_icons',
    'shortcodes',
    'mobile_blocks',
    'slideshows',
    'custom_css',
    'ckeditor',
    'markup_overrides',
    'devel',
    'legacy_browsers',
  ];

  // Get form values.
  $values = $form_state->getValues();

  foreach ($extensions_array as $extension) {
    $form_state_value = isset($values["settings_enable_$extension"]) ? $values["settings_enable_$extension"] : 0;
    $form_value = isset($form['enable_extensions']["settings_enable_$extension"]['#default_value']) ? $form['enable_extensions']["settings_enable_$extension"]['#default_value'] : 0;
    if (($form_state_value && $form_state_value === 1) || (!$form_state_value && $form_value === 1)) {
      include_once $at_core_path . '/forms/ext/' . $extension . '.php';
    }
  }
}

// Submit button for advanced settings.
$form['extensions']['actions'] = [
  '#type' => 'actions',
  '#attributes' => ['class' => ['submit--advanced-settings']],
];
$form['extensions']['actions']['submit'] = [
  '#type' => 'submit',
  '#value' => t('Save extension settings'),
  '#validate' => ['at_core_validate_extension_settings'],
  '#submit' => ['at_core_submit_extension_settings'],
  '#attributes' => ['class' => ['button--primary']],
];
