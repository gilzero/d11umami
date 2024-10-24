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

/**
 * The theme settings.
 */
$region = 'credit_copyright';
$label = 'credit & Copyright';

$form['solo_settings']["settings_{$region}"] = [
  '#type' => 'details',
  '#title' => t("<strong class='purple'>Copyright & Credit</strong> Settings - Solo Theme"),
  '#collapsible' => TRUE,
  '#collapsed' => FALSE,
];

$form['solo_settings']["settings_{$region}"]["classes_{$region}"] = _generate_css_classes($region, $label);

$form['solo_settings']["settings_{$region}"]['footer_copyright'] = [
  '#type' => 'textfield',
  '#title' => t('Enter Copyright Text'),
  '#description' => t("Type %year% instead of an actual number for a dynamic copyright year. (Copyright © %year% Company Name - All rights reserved)."),
  '#default_value' => theme_get_setting('footer_copyright'),
];

$form['solo_settings']["settings_{$region}"]['footer_link'] = [
  '#type' => 'textfield',
  '#title' => t('Enter Footer Link URL'),
  '#description' => t('Enter the URL for the footer link.'),
  '#default_value' => theme_get_setting('footer_link'),
];

$form['solo_settings']["settings_{$region}"]['footer_link_label'] = [
  '#type' => 'textfield',
  '#title' => t('Enter Footer Link Label'),
  '#description' => t('Enter the Label for the footer link[Footer Link Label][Footer Link Text].'),
  '#default_value' => theme_get_setting('footer_link_label'),
];

$form['solo_settings']["settings_{$region}"]['footer_link_text'] = [
  '#type' => 'textfield',
  '#title' => t('Enter Footer Link Text'),
  '#description' => t('Enter the text for the footer link. This field is required if a URL is provided.'),
  '#default_value' => theme_get_setting('footer_link_text'),
];

foreach ($attributes as $attribute_key => $attribute_label) {
  $form['solo_settings']["settings_{$region}"]["settings_{$region}_{$attribute_key}"] = _generate_form_element($region, $label, $attribute_key, $attribute_label);
}
