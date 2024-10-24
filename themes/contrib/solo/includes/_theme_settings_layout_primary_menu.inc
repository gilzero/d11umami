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

// Primary Menu region.
if (array_key_exists('primary_menu', $updated_regions['mix'])) {

  $region = 'primary_menu';
  $label = $updated_regions['mix'][$region];

  $form['solo_settings']["settings_{$region}"] = _generate_form_details($label);
  $form['solo_settings']["settings_{$region}"]["classes_{$region}"] = _generate_css_classes($region, $label);

  $form['solo_settings']["settings_{$region}"]['primary_menu_on_hover'] = [
    '#type' => 'checkbox',
    '#title' => t('Tick this box if you prefer to show the main menu dropdowns when hovering over them with the mouse, instead of clicking. (Big screen only)'),
    '#default_value' => theme_get_setting('primary_menu_on_hover'),
  ];

  $form['solo_settings']["settings_{$region}"]['primary_menu_border'] = [
    '#type' => 'checkbox',
    '#title' => t('Tick this box if you prefer to add border for each menu item'),
    '#default_value' => theme_get_setting('primary_menu_border'),
  ];

  $form['solo_settings']["settings_{$region}"]['primary_menu_align_center'] = [
    '#type' => 'checkbox',
    '#title' => t('Tick this box if you prefer the main menu to be centered.'),
    '#default_value' => theme_get_setting('primary_menu_align_center'),
  ];

  $form['solo_settings']["settings_{$region}"]['primary_menu_branding'] = [
    '#type' => 'checkbox',
    '#title' => t('Tick this box to display a clickable logo next to the Main Menu. To also display the site name, add the class show-sitename to the field (CSS Classes Only).'),
    '#default_value' => theme_get_setting('primary_menu_branding'),
  ];

  $form['solo_settings']["settings_{$region}"]['primary_menu_justify_content'] = [
    '#type' => 'checkbox',
    '#title' => t('Tick this box to distributes the space evenly between the menu items. The first item is positioned at the start of the line, and the last item is positioned at the end of the line.'),
    '#default_value' => theme_get_setting('primary_menu_justify_content'),
  ];

  $form['solo_settings']["settings_{$region}"]['primary_menu_expand_left'] = [
    '#type' => 'checkbox',
    '#title' => t('Tick this box if you want the dropdown menu to open on the left side instead of its default behavior, which is to open on the right side upon hover or click. (Big screen only)'),
    '#default_value' => theme_get_setting('primary_menu_expand_left'),
  ];

  $form['solo_settings']["settings_{$region}"]['primary_menu_keyboard'] = [
    '#type' => 'checkbox',
    '#title' => t('Tick this box to enable Keyboard Compatibility for the main menu.'),
    '#default_value' => theme_get_setting('primary_menu_keyboard'),
  ];

  $form['solo_settings']["settings_{$region}"]['primary_menu_megamenu'] = [
    '#type' => 'checkbox',
    '#title' => t('Tick this box to transform the main menu dropdowns into a Mega Menu.'),
    '#description' => t('Megamenus necessitate the implementation of hierarchical three-level menus. In this structure, the second level of menu items is instrumental in defining the "columns" for the megamenu. If the option "Tick this box to display the second level as headers" is selected, the second level will be exhibited as a header for the third-level menu items.'),
    '#default_value' => theme_get_setting('primary_menu_megamenu'),
  ];

  $form['solo_settings']["settings_{$region}"]['primary_menu_megamenu_layout'] = _create_megamenu_layout();

  $form['solo_settings']["settings_{$region}"]['primary_menu_submenu_header'] = [
    '#type' => 'checkbox',
    '#title' => t('Tick this box to display the second level as headers.'),
    '#default_value' => theme_get_setting('primary_menu_submenu_header'),
    '#states' => [
      'visible' => [
        ':input[name="primary_menu_megamenu"]' => ['checked' => TRUE],
      ],
    ],
  ];

  foreach ($attributes as $attribute_key => $attribute_label) {
    $form['solo_settings']["settings_{$region}"]["settings_{$region}_{$attribute_key}"] = _generate_form_element($region, $label, $attribute_key, $attribute_label);
  }

}
