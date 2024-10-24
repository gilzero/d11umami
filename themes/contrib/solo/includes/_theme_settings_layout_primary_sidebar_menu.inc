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

// Primary Sidebar menu region.
if (array_key_exists('primary_sidebar_menu', $updated_regions['mix'])) {

  $region = 'primary_sidebar_menu';
  $label = $updated_regions['mix'][$region];

  $form['solo_settings']["settings_{$region}"] = _generate_form_details($label);
  $form['solo_settings']["settings_{$region}"]["classes_{$region}"] = _generate_css_classes($region, $label);

  $form['solo_settings']["settings_{$region}"]['primary_sidebar_menu_hover'] = [
    '#type' => 'checkbox',
    '#title' => t('Tick this box if you prefer to show the sidebar main menu dropdowns when hovering over them with the mouse, instead of clicking. (Big screen only)'),
    '#default_value' => theme_get_setting('primary_sidebar_menu_hover'),
  ];

  $form['solo_settings']["settings_{$region}"]['primary_sidebar_menu_border'] = [
    '#type' => 'checkbox',
    '#title' => t('Tick this box if you prefer to add border for each menu item'),
    '#default_value' => theme_get_setting('primary_sidebar_menu_border'),
  ];

  $form['solo_settings']["settings_{$region}"]['primary_sidebar_menu_branding'] = [
    '#type' => 'checkbox',
    '#title' => t('Tick this box to display a clickable logo next to the close button. To also display the site name, add the class show-sitename to the field (CSS Classes Only).'),
    '#default_value' => theme_get_setting('primary_sidebar_menu_branding'),
  ];

  foreach ($attributes as $attribute_key => $attribute_label) {
    $form['solo_settings']["settings_{$region}"]["settings_{$region}_{$attribute_key}"] = _generate_form_element($region, $label, $attribute_key, $attribute_label);
  }

}
