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

// Popup login block region.
if (array_key_exists('popup_login_block', $updated_regions['mix'])) {

  $region = 'popup_login_block';
  $label = $updated_regions['mix'][$region];

  $form['solo_settings']["settings_{$region}"] = _generate_form_details($label);
  $form['solo_settings']["settings_{$region}"]["classes_{$region}"] = _generate_css_classes($region, $label);

  $form['solo_settings']["settings_{$region}"]['header_popup_login'] = [
    '#type' => 'checkbox',
    '#title' => t("Tick this box to login via a popup block. <strong><a class='error' href='/admin/structure/block'>The User Login Block Must Be Enabled</a></strong>"),
    '#description' => t("This feature provides an option for users to login via a popup block. When the user checks the box labeled 'Login in a Popup Block', instead of being redirected to a separate login page, a popup block will appear on the current page. This popup block will contain the necessary fields for the user to enter their login credentials."),
    '#default_value' => theme_get_setting('header_popup_login'),
  ];

  $form['solo_settings']["settings_{$region}"]['header_login_links'] = [
    '#type' => 'textfield',
    '#title' => t('You can change the login word.'),
    '#default_value' => theme_get_setting('header_login_links') ?? 'Login',
  ];

  foreach ($attributes as $attribute_key => $attribute_label) {
    $form['solo_settings']["settings_{$region}"]["settings_{$region}_{$attribute_key}"] = _generate_form_element($region, $label, $attribute_key, $attribute_label);
  }


}
