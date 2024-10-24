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

// Header region.
if (array_key_exists('header', $updated_regions['mix'])) {

  $region = 'header';
  $label = $updated_regions['mix'][$region];

  $form['solo_settings']["settings_{$region}"] = _generate_form_details($label);
  $form['solo_settings']["settings_{$region}"]["classes_{$region}"] = _generate_css_classes($region, $label);

  $form['solo_settings']["settings_{$region}"]['header_logo_image_alt'] = [
    '#type' => 'textfield',
    '#title' => t('Enter the text to be used for the Site Name Link Title and Site Logo Image Alt.'),
    '#default_value' => theme_get_setting('header_logo_image_alt'),
  ];

  $form['solo_settings']["settings_{$region}"]['header_sitename_center_default'] = [
    '#type' => 'checkbox',
    '#title' => t("Select this option to center the Site's Logo, Site's Name, and Slogan within the header. The logo will be positioned on the left, while the site name and slogan will be centered alongside it."),
    '#default_value' => theme_get_setting('header_sitename_center_default'),
  ];

  $form['solo_settings']["settings_{$region}"]['header_sitename_center'] = [
    '#type' => 'checkbox',
    '#title' => t("Select this option to align the Site's Logo, Site's Name, and Slogan centrally. The logo will be positioned at the top, followed by the site name and slogan."),
    '#default_value' => theme_get_setting('header_sitename_center'),
  ];

  $form['solo_settings']["settings_{$region}"]['header_sitename_center_small'] = [
    '#type' => 'checkbox',
    '#title' => t("For small screen, select this option to align the Site's Logo, Site's Name, and Slogan centrally. The logo will be positioned at the top, followed by the site name and slogan. This configuration will adhere to the global breakpoints set on the site."),
    '#default_value' => theme_get_setting('header_sitename_center_small'),
  ];

  $form['solo_settings']["settings_{$region}"]['header_change_icons'] = [
    '#type' => 'checkbox',
    '#title' => t("Select this option to position the Search Icon, User Menu, Sidebar Menu, and Sidebar Hamburger to the left, and make the Main Sidebar menu open from the left as well."),
    '#default_value' => theme_get_setting('header_change_icons'),
  ];

  $form['solo_settings']["settings_{$region}"]['site_name_font_size_s'] = [
    '#title' => t("Select the desired font size for the Site Name on small screen."),
    '#type' => 'select',
    '#options' => _get_site_name_font_size(),
    '#default_value' => theme_get_setting('site_name_font_size_s'),
    '#description' => t("<p>Small Screen: You can adjust the page title's font size by choosing your preferred size in pixels or rem units. <strong>This setting follows the website's breakpoints, which can be found under Global Site Settings.</strong></p>"),
  ];

  $form['solo_settings']["settings_{$region}"]['site_name_font_size_l'] = [
    '#title' => t("Select the desired font size for the Site Name on large screen."),
    '#type' => 'select',
    '#options' => _get_site_name_font_size(),
    '#default_value' => theme_get_setting('site_name_font_size_l'),
    '#description' => t("<p>Large Screen: You can adjust the page title's font size by choosing your preferred size in pixels or rem units. <strong>This setting follows the website's breakpoints, which can be found under Global Site Settings.</strong></p>"),
  ];

  $form['solo_settings']["settings_{$region}"]['site_name_google_font'] = [
    '#type' => 'select',
    '#title' => t("<span class='status'>Site Name Fonts:</span> They are not applied to any html tags by default and they are only imported. Each font library has a CSS class that you can apply to any html tag. <a target='_blank' href='https://unitedstarsofamerica.com/node/394'>Click here to view a live demo for all available fonts.</a>"),
    '#description'   => t("There are 25 fonts, each with its own CSS class that can be applied to any HTML tag. For example, if you select Eagel Lake, the CSS class will be 'eagle-lake'."),
    '#options' => _get_name_title_google_font(),
    '#default_value' => theme_get_setting('site_name_google_font'),
  ];

  $form['solo_settings']["settings_{$region}"]['site_name_animate_on'] = [
    '#type' => 'select',
    '#title' => t("Select the desired CSS text animation effects for the Site Name."),
    '#description' => t("<p>Note that the animations are sorted based on their names (e.g., 'Aurora Wave', 'Dreamy Bloom', etc.) <strong>You can also utilize the H1, H2, H3 text in the field below to apply color to the site name and site slogan.</strong></p>"),
    '#options' => _get_animate_site_name(),
    '#default_value' => theme_get_setting('site_name_animate_on'),
  ];

  foreach ($attributes as $attribute_key => $attribute_label) {
    $form['solo_settings']["settings_{$region}"]["settings_{$region}_{$attribute_key}"] = _generate_form_element($region, $label, $attribute_key, $attribute_label);
  }

}
