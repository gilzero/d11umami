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

if ($updated_regions['bottom']) {

  $region_label = implode(", ", $updated_regions['bottom']);

  $form['solo_settings']["settings_bottom_container"] = [
    '#type' => 'details',
    '#title' => t("<strong class='status'>Bottom Containers</strong> Settings - Solo Theme"),
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
    '#description'   => t("<p>The settings for @region_label.</p>", ['@region_label' => $region_label]),
  ];

  foreach ($updated_regions['bottom'] as $region => $label) {

    if ($region == 'container') {
      $region = 'bottom_container';
      $form['solo_settings']["settings_bottom_container"]["bottom_2col"] = _create_region_layout_radio_two('bottom', $variables);
      $form['solo_settings']["settings_bottom_container"]["bottom_3col"] = _create_region_layout_radio_three('bottom');
      $form['solo_settings']["settings_bottom_container"]["bottom_4col"] = _create_region_layout_radio_four('bottom');
      $form['solo_settings']["settings_bottom_container"]["site_regions_bottom_disable"] = _disable_region('bottom');
      $form['solo_settings']["settings_bottom_container"]["site_regions_bottom_rounded"] = _rounded_region('bottom');
      $form['solo_settings']["settings_bottom_container"]["site_regions_bottom_border"] = _border_region('bottom');
      $form['solo_settings']["settings_bottom_container"]["site_regions_bottom_animate_border"] = _animate_region_border('bottom');
      $form['solo_settings']["settings_bottom_container"]["site_regions_bottom_animate_hover"] = _animate_region_hover('bottom');
      $form['solo_settings']["settings_bottom_container"]["classes_{$region}"] = _generate_css_classes($region, $label);
      foreach ($attributes as $attribute_key => $attribute_label) {
        $form['solo_settings']["settings_bottom_container"]["settings_{$region}_{$attribute_key}"] = _generate_form_element($region, $label, $attribute_key, $attribute_label);
      }
    }
    else {
      $form['solo_settings']['settings_bottom_container']["settings_{$region}"] = _generate_form_details($label);
      foreach ($attributes as $attribute_key => $attribute_label) {
        $form['solo_settings']['settings_bottom_container']["settings_{$region}"]["classes_{$region}"] = _generate_css_classes($region, $label);
        $form['solo_settings']['settings_bottom_container']["settings_{$region}"]["settings_{$region}_{$attribute_key}"] = _generate_form_element($region, $label, $attribute_key, $attribute_label);
      }
    }
  }

}
