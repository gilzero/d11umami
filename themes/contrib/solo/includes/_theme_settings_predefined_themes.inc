<?php

/**
 * @file
 * Solo Theme.
 *
 * Filename:     solo.theme
 * Website:      http://www.flashwebcenter.com
 * Description:  template
 * Author:       Alaa Haddad http://www.alaahaddad.com.
 */

use Drupal\Core\StringTranslation\TranslatableMarkup;

$form['solo_settings']['settings_predefined_theme'] = [
  '#type' => 'details',
  '#title' => new TranslatableMarkup('<strong class="warning">Predefined Color Schemes</strong> Settings - Solo Theme'),
  '#collapsible' => TRUE,
  '#collapsed' => FALSE,
  '#description' => \Drupal::moduleHandler()->moduleExists('solo_utilities') && solo_utilities__check_solo_theme_hierarchy_active()
    ? new TranslatableMarkup('<p>The Solo theme allows you to apply color schemes dynamically based on various site elements, such as content types, taxonomies, user roles, and specific URLs. This flexibility enables you to create a visually cohesive and customized experience for your visitors. <strong><a href="/admin/config/system/solo_utilities">Click here to manage Color Schemes Rules and start customizing your site’s appearance.</a></strong></p> <p><strong><a target="_blank" href="https://unitedstarsofamerica.com/color-schemes">The demo site provides examples for all of the color schemes</a></strong></p>')
    : new TranslatableMarkup('<p>The Solo theme comes with 50 different color schemes. You can now select a specific color scheme to be applied to various parts of your website, such as content types, taxonomies, user roles, and specific URLs. To use this feature, ensure that the <a target="_blank" href="https://www.drupal.org/project/solo_utilities">Solo Utilities</a> module is enabled. <strong>This feature is only available when the Solo theme, or any of its sub-themes, is part of the active theme hierarchy. Simply installing the Solo theme or its sub-themes is not enough. To use this feature, the Solo theme itself must be the default theme, or it must be a parent, grandparent, or anywhere in the hierarchy of the currently active default theme.</strong></p><p><strong><a target="_blank" href="https://unitedstarsofamerica.com/color-schemes">The demo site provides examples for all of the color schemes</a></strong></p>'),
];

$form['solo_settings']['settings_predefined_theme']['theme_category'] = [
  '#type' => 'select',
  '#title' => t('Select a category'),
  '#options' => _get_theme_category(),
  '#default_value' => theme_get_setting('theme_category'),
];

$form['solo_settings']['settings_predefined_theme']['predefined_current_theme'] = [
  '#type' => 'select',
  '#title' => t('Choose the desired color scheme'),
  '#options' => _get_predefined_theme(),
  '#states' => [
    'visible' => [
      ':input[name="theme_category"]' => ['!value' => 'none'],
    ],
  ],
  '#default_value' => theme_get_setting('predefined_current_theme'),
];
