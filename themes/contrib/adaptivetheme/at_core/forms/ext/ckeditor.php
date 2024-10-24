<?php

/**
 * @file
 * CKEditor skin settings.
 */

$form['ckeditor'] = [
  '#type' => 'details',
  '#title' => t('CKEditor Skins'),
  '#group' => 'extension_settings',
  '#description' => t('Adaptivetheme includes a CKEditor skin called <b>Mimic</b>. Mimic automatically inherits your themes colors, backgrounds, fonts, heading and other text styles to give you are more realistic WYSIWYG experience. Mimic requires FontAwesome for icons - FontAwesome is included in every Adaptivetheme sub-theme by default, so just be aware of that if you choose to remove FA from your sub-theme.'),
];

$form['ckeditor']['settings_mimic_enabled'] = [
  '#type' => 'checkbox',
  '#title' => t('Use Mimic'),
  '#default_value' => theme_get_setting('settings.mimic_enabled'),
  '#description' => t('Uncheck to use Drupals default CKEditor skin... &#x1f631;'),
];
