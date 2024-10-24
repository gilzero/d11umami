<?php

/**
 * @file
 */

use Drupal\Component\Serialization\Yaml;

$theme_info_paths['path']['#markup'] = '<strong>' . t('Theme path') . '</strong>: <span>' . $subtheme_path . '</span>';
$theme_info_paths['at_path']['#markup'] = '<strong>' . t('AT Core path') . '</strong>: <span>' . $at_core_path . '</span>';

$form['theme_info'] = [
  '#type' => 'details',
  '#title' => t('Theme Info'),
  '#weight' => 2000,
  '#open' => FALSE,
  '#attributes' => ['class' => ['theme-info-settings', 'clearfix']],
];

$form['theme_info']['info_container'] = [
  '#type' => 'fieldset',
];

$form['theme_info']['info_container']['paths'] = [
  '#type' => 'container',
  '#theme' => 'item_list',
  '#title' => t('Theme Paths'),
  '#items' => $theme_info_paths,
];

$form['theme_info']['info_container']['list_info'] = [
  '#type' => 'textarea',
  '#title' => t('Theme info'),
  '#rows' => 60,
  '#disabled' => TRUE,
  '#default_value' => Yaml::encode($getThemeInfo),
];
