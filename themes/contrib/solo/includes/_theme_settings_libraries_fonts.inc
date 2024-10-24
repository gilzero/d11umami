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

$form['solo_settings']['settings_libraries_fonts'] = [
  '#type' => 'details',
  '#title' => t("<strong class='purple'>Libraries & Fonts</strong> Settings - Solo Theme"),
  '#collapsible' => TRUE,
  '#collapsed' => FALSE,
];

$form['solo_settings']['settings_libraries_fonts']['site_load_w3css_extended'] = [
  '#type' => 'details',
  '#title' => t("<strong>Extra Predefined CSS Classes</strong>"),
  '#collapsible' => TRUE,
  '#collapsed' => FALSE,
  '#description'   => t("<table style='width: 100%; line-height:1.6;  color: LightSlateGray; font-size: 16px; font-family: Helvetica;'>
<tr>
  <td> </td>
  <th><strong>To utilize the following classes, the w3.css library needs to be activated. Any of the CSS classes can be copied and pasted into the text field. It's possible to paste multiple classes, just ensure there is a space separating each one.</strong></th>
 </tr>

  <tr>
    <th>Background Colors w3-<em>color</em>:</th>
    <td>w3-red, w3-pink, w3-purple, w3-deep-purple, w3-indigo, w3-blue, w3-light-blue, w3-cyan, w3-aqua, w3-teal, w3-green, w3-light-green, w3-lime, w3-sand, w3-khaki, w3-yellow, w3-amber, w3-orange, w3-deep-orange, w3-blue-grey, w3-brown, w3-light-grey, w3-grey, w3-dark-grey, w3-black, w3-white, w3-pale-red, w3-pale-yellow, w3-pale-green and w3-pale-blue. <a target='_blank' href='https://flex6.com/node/169'>Click here to view a live demo for the CSS classes</a></td>
  </tr>
  <tr>
    <th>Background Hover Colors w3-hover-<em>color</em>:</th>
    <td>w3-hover-red, w3-hover-pink, w3-hover-purple, w3-hover-deep-purple, w3-hover-indigo, w3-hover-blue, w3-hover-light-blue, w3-hover-cyan, w3-hover-aqua, w3-hover-teal, w3-hover-green, w3-hover-light-green, w3-hover-lime, w3-hover-sand, w3-hover-khaki, w3-hover-yellow, w3-hover-amber, w3-hover-orange, w3-hover-deep-orange, w3-hover-blue-grey, w3-hover-brown, w3-hover-light-grey, w3-hover-grey, w3-hover-dark-grey, w3-hover-black, w3-hover-white, w3-hover-pale-red, w3-hover-pale-yellow, w3-hover-pale-green and w3-hover-pale-blue. <a target='_blank' href='https://flex6.com/node/168'>Click here to view a live demo for the CSS classes</a></td>
  </tr>

  <tr>
    <th>Text Colors w3-text-<em>color</em>:</th>
    <td>w3-text-red, w3-text-pink, w3-text-purple, w3-text-deep-purple, w3-text-indigo, w3-text-blue, w3-text-light-blue, w3-text-cyan, w3-text-aqua, w3-text-teal, w3-text-green, w3-text-light-green, w3-text-lime, w3-text-sand, w3-text-khaki, w3-text-yellow, w3-text-amber, w3-text-orange, w3-text-deep-orange, w3-text-blue-grey, w3-text-brown, w3-text-light-grey, w3-text-grey, w3-text-dark-grey, w3-text-black, w3-text-white , w3-text-pale-red, w3-text-pale-yellow, w3-text-pale-green and w3-text-pale-blue. <a target='_blank' href='https://flex6.com/node/124'>Click here to view a live demo for the CSS classes</a></td>
  </tr>
  <tr>
    <th>Text Hover Colors w3-hover-text-<em>color</em>:</th>
    <td>w3-hover-text-red, w3-hover-text-pink, w3-hover-text-purple, w3-hover-text-deep-purple, w3-hover-text-indigo, w3-hover-text-blue, w3-hover-text-light-blue, w3-hover-text-cyan, w3-hover-text-aqua, w3-hover-text-teal, w3-hover-text-green, w3-hover-text-light-green, w3-hover-text-lime, w3-hover-text-sand, w3-hover-text-khaki, w3-hover-text-yellow, w3-hover-text-amber, w3-hover-text-orange, w3-hover-text-deep-orange, w3-hover-text-blue-grey, w3-hover-text-brown, w3-hover-text-light-grey, w3-hover-text-grey, w3-hover-text-dark-grey, w3-hover-text-black and w3-hover-text-white, w3-hover-text-pale-red, w3-hover-text-pale-yellow, w3-hover-text-pale-green and w3-hover-text-pale-blue. <a target='_blank' href='https://flex6.com/node/143'>Click here to view a live demo for the CSS classes</a></td>
  </tr>

  <tr>
    <th>Link Text Colors w3-link-<em>color</em>:</th>
    <td>w3-link-red, w3-link-pink, w3-link-purple, w3-link-deep-purple, w3-link-indigo, w3-link-blue, w3-link-light-blue, w3-link-cyan, w3-link-aqua, w3-link-teal, w3-link-green, w3-link-light-green, w3-link-lime, w3-link-sand, w3-link-khaki, w3-link-yellow, w3-link-amber, w3-link-orange, w3-link-deep-orange, w3-link-blue-grey, w3-link-brown, w3-link-light-grey, w3-link-grey, w3-link-dark-grey, w3-link-black and w3-link-white, w3-link-pale-red, w3-link-pale-yellow, w3-link-pale-green and w3-link-pale-blue. <a target='_blank' href='https://flex6.com/node/135'>Click here to view a live demo for the CSS classes</a></td>
  </tr>

  <tr>
    <th>Link Background Colors w3-link-bg-<em>color</em>:</th>
    <td>w3-link-bg-red, w3-link-bg-green, w3-link-bg-blue, w3-link-bg-yellow, w3-link-bg-white, w3-link-bg-black, w3-link-bg-grey, w3-link-bg-amber, w3-link-bg-aqua, w3-link-bg-light-blue, w3-link-bg-brown, w3-link-bg-cyan, w3-link-bg-blue-grey, w3-link-bg-blue-gray, w3-link-bg-light-green, w3-link-bg-indigo, w3-link-bg-khaki, w3-link-bg-lime, w3-link-bg-orange, w3-link-bg-deep-orange, w3-link-bg-pink, w3-link-bg-purple, w3-link-bg-deep-purple, w3-link-bg-sand, w3-link-bg-teal, w3-link-bg-light-gray, w3-link-bg-light-grey, w3-link-bg-dark-gray, w3-link-bg-dark-grey, w3-link-bg-pale-red, w3-link-bg-pale-yellow, w3-ling-bg-pale-green and w3-link-bg-pale-blue. <a target='_blank' https://flex6.com/node/137'>Click here to view a live demo for the CSS classes</a></td>
  </tr>

  <tr>
    <th>Link Background Hover Colors w3-link-hover-<em>color</em>:</th>
    <td>w3-link-hover-red, w3-link-hover-green, w3-link-hover-blue, w3-link-hover-yellow, w3-link-hover-white, w3-link-hover-black, w3-link-hover-grey, w3-link-hover-amber, w3-link-hover-aqua, w3-link-hover-light-blue, w3-link-hover-brown, w3-link-hover-cyan, w3-link-hover-blue-grey, w3-link-hover-blue-gray, w3-link-hover-light-green, w3-link-hover-indigo, w3-link-hover-khaki, w3-link-hover-lime, w3-link-hover-orange, w3-link-hover-deep-orange, w3-link-hover-pink, w3-link-hover-purple, w3-link-hover-deep-purple, w3-link-hover-sand, w3-link-hover-teal, w3-link-hover-light-gray, w3-link-hover-light-grey, w3-link-hover-dark-gray, w3-link-hover-dark-grey, w3-link-hover-pale-red, w3-link-hover-pale-yellow, w3-link-hover-pale-green and w3-link-hover-pale-blue. <a target='_blank' href='https://flex6.com/node/136'>Click here to view a live demo for the CSS classes</a></td>
  </tr>

  <tr>
    <th>Button Colors w3-button-<em>color</em>:</th>
    <td>w3-button-red, w3-button-green, w3-button-blue, w3-button-yellow, w3-button-white, w3-button-black, w3-button-gray, w3-button-grey, w3-button-amber, w3-button-aqua, w3-button-light-blue, w3-button-brown, w3-button-cyan, w3-button-blue-grey, w3-button-blue-gray, w3-button-light-green, w3-button-indigo, w3-button-khaki, w3-button-lime, w3-button-orange, w3-button-deep-orange, w3-button-pink, w3-button-purple, w3-button-deep-purple, w3-button-sand, w3-button-teal, w3-button-light-gray, w3-button-light-grey, w3-button-dark-gray, w3-button-dark-grey, w3-button-pale-red, w3-button-pale-yellow, w3-button-pale-green and w3-button-pale-blue. <a target='_blank' href='https://flex6.com/node/162'>Click here to view a live demo for the CSS classes</a></td>
  </tr>

  <tr>
    <th>Button Hover Colors w3-button-hover-<em>color</em>:</th>
    <td>w3-button-hover-red, w3-button-hover-green, w3-button-hover-blue, w3-button-hover-yellow, w3-button-hover-white, w3-button-hover-black, w3-button-hover-gray, w3-button-hover-grey, w3-button-hover-amber, w3-button-hover-aqua, w3-button-hover-light-blue, w3-button-hover-brown, w3-button-hover-cyan, w3-button-hover-blue-grey, w3-button-hover-blue-gray, w3-button-hover-light-green, w3-button-hover-indigo, w3-button-hover-khaki, w3-button-hover-lime, w3-button-hover-orange, w3-button-hover-deep-orange, w3-button-hover-pink, w3-button-hover-purple, w3-button-hover-deep-purple, w3-button-hover-sand, w3-button-hover-teal, w3-button-hover-light-gray, w3-button-hover-light-grey, w3-button-hover-dark-gray, w3-button-hover-dark-grey, w3-button-hover-pale-red, w3-button-hover-pale-yellow, w3-button-hover-pale-green and w3-button-hover-pale-blue. <a target='_blank' href='https://flex6.com/node/161'>Click here to view a live demo for the CSS classes</a></td>
  </tr>
</table>"),
];

$form['solo_settings']['settings_libraries_fonts']['site_load_w3css_on'] = [
  '#type' => 'checkbox',
  '#title' => t('<strong>Tick this box to load the W3.CSS library locally</strong>.'),
  '#default_value' => theme_get_setting('site_load_w3css_on'),
];

$form['solo_settings']['settings_libraries_fonts']['site_font_google_local_on'] = [
  '#type' => 'checkbox',
  '#title' => t("Tick this box to load Google fonts locally. (<span class='status'>Global and Heading</span> Fonts only)"),
  '#description'   => t("<p>Any font you choose for the site, heading or special will have a CSS class that you can apply to any html tag. <a target='_blank' href='https://www.drupal.org/node/2866181#change-font'>click here to view the documentaion on drupal.org</a></p>"),
  '#default_value' => theme_get_setting('site_font_google_local_on'),
];

$form['solo_settings']['settings_libraries_fonts']['site_font_awesome_on'] = [
  '#type' => 'select',
  '#title' => t('Select Font Awesome Library'),
  '#default_value' => theme_get_setting('site_font_awesome_on'),
  '#options' => [
    0 => t('None'),
    1 => t('Load Font Awesome 4.7 locally.'),
    2 => t('Load Font Awesome 4.7 from the CDN.'),
    3 => t('Load Font Awesome 5.15.4 from the CDN.'),
    4 => t('Load Font Awesome 6.5.1 from the CDN.'),
  ],
];

$form['solo_settings']['settings_libraries_fonts']['site_font_google_global_on'] = [
  '#type' => 'select',
  '#title' => t("<span class='status'>Global Site Fonts:</span> Choose the site font - The font will be applied to the whole website.<a target='_blank' href='https://unitedstarsofamerica.com/node/192'> Click here to view a live demo for all available fonts.</a>"),
  '#description'   => t("There are 50 Global Site Fonts, each with its own CSS class that can be applied to any HTML tag. For example, if you select Open Sans, the CSS class will be 'open-sans'."),
  '#options' => _get_global_font_options(),
  '#default_value' => theme_get_setting('site_font_google_global_on'),
];

$form['solo_settings']['settings_libraries_fonts']['site_font_google_heading_on'] = [
  '#type' => 'select',
  '#title' => t("<span class='status'>Heading Fonts:</span> Choose the heading font - The font will be applied to h1, h2 and h3 only.<a target='_blank' href='https://unitedstarsofamerica.com/node/193'> Click here to view a live demo for all available fonts.</a>"),
  '#description'   => t("There are 26 Heading Fonts, each with its own CSS class that can be applied to any h1, h2 or h3 tag. For example, if you select Open Sans, the CSS class will be 'open-sans'."),
  '#options' => _get_heading_font_options(),
  '#default_value' => theme_get_setting('site_font_google_heading_on'),
];

$form['solo_settings']['settings_libraries_fonts']['site_font_google_special_on'] = [
  '#type' => 'select',
  '#title' => t("<span class='status'>Special Fonts:</span> They are not applied to any html tags by default and they are only imported. Each font library has a CSS class that you can apply to any html tag. <a target='_blank' href='https://unitedstarsofamerica.com/node/191'>Click here to view a live demo for all available fonts.</a>"),
  '#description'   => t("There are 25 Special fonts, each with its own CSS class that can be applied to any HTML tag. For example, if you select Open Sans, the CSS class will be 'open-sans'."),
  '#options' => _get_special_font_options(),
  '#default_value' => theme_get_setting('site_font_google_special_on'),
];
