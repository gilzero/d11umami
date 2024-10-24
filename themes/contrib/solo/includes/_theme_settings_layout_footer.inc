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

// Footer region.
if ($updated_regions['footer']) {

  $region_label = implode(", ", $updated_regions['footer']);

  $form['solo_settings']["settings_footer_container"] = [
    '#type' => 'details',
    '#title' => t("<strong class='status'>Footer Containers</strong> Settings - Solo Theme"),
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
    '#description'   => t("<p>The settings for @region_label.</p>", ['@region_label' => $region_label]),
  ];

  foreach ($updated_regions['footer'] as $region => $label) {

    if ($region == 'container') {
      $region = 'footer_container';
      $form['solo_settings']["settings_footer_container"]["footer_2col"] = _create_region_layout_radio_two('footer', $variables);
      $form['solo_settings']["settings_footer_container"]["footer_3col"] = _create_region_layout_radio_three('footer');
      $form['solo_settings']["settings_footer_container"]["site_regions_footer_disable"] = _disable_region('footer');
      $form['solo_settings']["settings_footer_container"]["site_regions_footer_rounded"] = _rounded_region('footer');
      $form['solo_settings']["settings_footer_container"]["site_regions_footer_border"] = _border_region('footer');
      $form['solo_settings']["settings_footer_container"]["site_regions_footer_animate_border"] = _animate_region_border('footer');
      $form['solo_settings']["settings_footer_container"]["site_regions_footer_animate_hover"] = _animate_region_hover('footer');
      $form['solo_settings']["settings_footer_container"]["classes_{$region}"] = _generate_css_classes($region, $label);
      foreach ($attributes as $attribute_key => $attribute_label) {
        $form['solo_settings']["settings_footer_container"]["settings_{$region}_{$attribute_key}"] = _generate_form_element($region, $label, $attribute_key, $attribute_label);
      }
    }
    else {
      $form['solo_settings']['settings_footer_container']["settings_{$region}"] = _generate_form_details($label);
      foreach ($attributes as $attribute_key => $attribute_label) {
        $form['solo_settings']['settings_footer_container']["settings_{$region}"]["classes_{$region}"] = _generate_css_classes($region, $label);
        $form['solo_settings']['settings_footer_container']["settings_{$region}"]["settings_{$region}_{$attribute_key}"] = _generate_form_element($region, $label, $attribute_key, $attribute_label);
      }
    }
  }

}
