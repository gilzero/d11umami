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

// System message region.
if (array_key_exists('system_messages', $updated_regions['mix'])) {

  $region = 'system_messages';
  $label = $updated_regions['mix'][$region];

  $form['solo_settings']["settings_{$region}"] = _generate_form_details($label);
  $form['solo_settings']["settings_{$region}"]["classes_{$region}"] = _generate_css_classes($region, $label);

  $form['solo_settings']["settings_{$region}"]['system_messages_disable_dark'] = [
    '#type' => 'checkbox',
    '#title' => t("Tick this box if you want to personalize the background of your system messages to match your brand. This will disable the default background."),
    '#default_value' => theme_get_setting('system_messages_disable_dark'),
  ];

  foreach ($attributes as $attribute_key => $attribute_label) {
    $form['solo_settings']["settings_{$region}"]["settings_{$region}_{$attribute_key}"] = _generate_form_element($region, $label, $attribute_key, $attribute_label);
  }

}
