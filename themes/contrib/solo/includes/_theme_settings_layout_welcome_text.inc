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

// Welcome region.
if (array_key_exists('welcome_text', $updated_regions['mix'])) {

  $region = 'welcome_text';
  $label = $updated_regions['mix'][$region];

  $form['solo_settings']["settings_{$region}"] = _generate_form_details($label);

  $form['solo_settings']["settings_{$region}"]["classes_{$region}"] = _generate_css_classes($region, $label);
  $form['solo_settings']["settings_{$region}"]["site_regions_welcome_disable"] = _disable_region('welcome');

  $form['solo_settings']["settings_{$region}"]['site_regions_welcome_width'] = [
    '#type' => 'checkbox',
    '#title' => t('Tick this box to enable full width for this region (recommended for hero sections).'),
    '#default_value' => theme_get_setting('site_regions_welcome_width'),
  ];

  foreach ($attributes as $attribute_key => $attribute_label) {
    $form['solo_settings']["settings_{$region}"]["settings_{$region}_{$attribute_key}"] = _generate_form_element($region, $label, $attribute_key, $attribute_label);
  }

}
