<?php

/**
 * @file
 * Generate form elements for the touch icons settings.
 */

use Drupal\Component\Utility\Html;

$form['touch_icons'] = [
  '#type' => 'details',
  '#title' => t('Touch Icons'),
  '#group' => 'extension_settings',
];

$form['touch_icons']['touch_icons_settings'] = [
  '#type' => 'fieldset',
  '#title' => t('Touch Icons'),
  '#weight' => 10,
];

$form['touch_icons']['touch_icons_settings']['description'] = [
  '#markup' => t('<h3>Touch Icons</h3><p>Different devices can support different sized touch icons - see <a href=":mathiasbynens" target="_blank">Everything you always wanted to know about touch icons</a>.</p><p>Enter the path to each icon as required. Paths must be relative to your theme root. Leave fields blank to omit the icon.</p>', [':mathiasbynens' => 'https://mathiasbynens.be/notes/touch-icons']),
];

$form['touch_icons']['touch_icons_settings']['icon-paths'] = [
  '#type' => 'fieldset',
  '#title' => t('Touch Icon Paths'),
];

// For non-Retina iPhone, iPod Touch, and Android 2.1+ devices (60x60)
$form['touch_icons']['touch_icons_settings']['icon-paths']['settings_icon_path_default'] = [
  '#type' => 'textfield',
  '#title' => t('iPhone @1x'),
  '#description' => t('For non-Retina iPhone, iPod Touch, and Android 2.1+ devices (60x60).'),
  '#field_prefix' => $theme . '/',
  '#default_value' => Html::escape(theme_get_setting('settings.icon_path_default')),
];

// For iPhone with @2× display running iOS ≥ 7 (120x120)
$form['touch_icons']['touch_icons_settings']['icon-paths']['settings_apple_touch_icon_path_iphone_retina'] = [
  '#type' => 'textfield',
  '#title' => t('iPhone @2x'),
  '#description' => t('For iPhone with @2× display running iOS ≥ 7 (120x120).'),
  '#field_prefix' => $theme . '/',
  '#default_value' => Html::escape(theme_get_setting('settings.apple_touch_icon_path_iphone_retina')),
];

// For iPhone 6 Plus with @3× display (180x180)
$form['touch_icons']['touch_icons_settings']['icon-paths']['settings_apple_touch_icon_path_ipad_retina_3x'] = [
  '#type' => 'textfield',
  '#title' => t('iPhone @3x'),
  '#description' => t('For iPhone 6 Plus with @3× display (180x180).'),
  '#field_prefix' => $theme . '/',
  '#default_value' => Html::escape(theme_get_setting('settings.apple_touch_icon_path_ipad_retina_3x')),
];

// For the iPad mini and the first- and second-generation iPad (@1× display) on iOS ≥ 7 (76x76)
$form['touch_icons']['touch_icons_settings']['icon-paths']['settings_apple_touch_icon_path_ipad'] = [
  '#type' => 'textfield',
  '#title' => t('iPad @1x'),
  '#description' => t('For the iPad mini and the first- and second-generation iPad (@1× display) on iOS ≥ 7 (76x76).'),
  '#field_prefix' => $theme . '/',
  '#default_value' => Html::escape(theme_get_setting('settings.apple_touch_icon_path_ipad')),
];

// For iPad with @2× display running iOS ≥ 7 (152x152)
$form['touch_icons']['touch_icons_settings']['icon-paths']['settings_apple_touch_icon_path_ipad_retina'] = [
  '#type' => 'textfield',
  '#title' => t('iPad @2x'),
  '#description' => t('For iPad with @2× display running iOS ≥ 7 (152x152).'),
  '#field_prefix' => $theme . '/',
  '#default_value' => Html::escape(theme_get_setting('settings.apple_touch_icon_path_ipad_retina')),
];

// For Chrome on Android (192x192)
$form['touch_icons']['touch_icons_settings']['icon-paths']['settings_apple_touch_icon_path_chrome_android'] = [
  '#type' => 'textfield',
  '#title' => t('Android Chrome'),
  '#description' => t('For Chrome on Android (192x192).'),
  '#field_prefix' => $theme . '/',
  '#default_value' => Html::escape(theme_get_setting('settings.apple_touch_icon_path_chrome_android')),
];

$form['touch_icons']['touch_icons_settings']['icon-paths']['settings_apple_touch_icon_precomposed'] = [
  '#type' => 'checkbox',
  '#title' => t('Use apple-touch-icon-precomposed'),
  '#description' => t('Use precomposed if you want to remove icon effects in iOS6. The default is <code>apple-touch-icon</code>. '),
  '#default_value' => Html::escape(theme_get_setting('settings.apple_touch_icon_precomposed')),
];
