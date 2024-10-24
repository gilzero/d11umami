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

if ($updated_regions['main']) {

  $region_label = implode(", ", $updated_regions['main']);

  $form['solo_settings']["settings_main_container"] = [
    '#type' => 'details',
    '#title' => t("<strong class='status'>Main Containers</strong> Settings - Solo Theme"),
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
    '#description'   => t("<p>The settings for @region_label.</p>", ['@region_label' => $region_label]),
  ];

  foreach ($updated_regions['main'] as $region => $label) {

    if ($region == 'container') {
      $region = 'main_container';
      $form['solo_settings']["settings_main_container"]["main_2col"] = _create_region_layout_radio_two('main', $variables);
      $form['solo_settings']["settings_main_container"]["main_3col"] = _create_region_layout_radio_three('main');
      $form['solo_settings']["settings_main_container"]["site_regions_main_rounded"] = _rounded_region('main');
      $form['solo_settings']['settings_main_container']['site_mobile_layout_order'] = [
        '#type' => 'select',
        '#title' => t('Select the layout order for mobile view'),
        '#description' => t('<p>This setting allows you to choose the order in which the main content and sidebars are displayed on smaller screens.</p>'),
        '#options' => [
          'left_main_right' => t('Default Layout (Left, Main, Right)'),
          'main_left_right' => t('Main Content First, then Left and Right Sidebars'),
          'main_right_left' => t('Main Content First, then Right and Left Sidebars'),
          'left_right_main' => t('Left Sidebar First, then Right Sidebar and Main Content'),
          'right_left_main' => t('Right Sidebar First, then Left Sidebar and Main Content'),
          'left_right_main' => t('Left and Right Sidebars First, then Main Content'),
          'right_left_main' => t('Right and Left Sidebars First, then Main Content'),
        ],
        '#default_value' => theme_get_setting('site_mobile_layout_order'),
      ];
      $form['solo_settings']["settings_main_container"]["site_regions_main_border"] = _border_region('main');
      $form['solo_settings']["settings_main_container"]["classes_{$region}"] = _generate_css_classes($region, $label);

      foreach ($attributes as $attribute_key => $attribute_label) {
        $form['solo_settings']["settings_main_container"]["settings_{$region}_{$attribute_key}"] = _generate_form_element($region, $label, $attribute_key, $attribute_label);
      }
    }
    else {
      $form['solo_settings']['settings_main_container']["settings_{$region}"] = _generate_form_details($label);
      foreach ($attributes as $attribute_key => $attribute_label) {
        $form['solo_settings']['settings_main_container']["settings_{$region}"]["classes_{$region}"] = _generate_css_classes($region, $label);
        $form['solo_settings']['settings_main_container']["settings_{$region}"]["settings_{$region}_{$attribute_key}"] = _generate_form_element($region, $label, $attribute_key, $attribute_label);
      }
    }
  }

}
