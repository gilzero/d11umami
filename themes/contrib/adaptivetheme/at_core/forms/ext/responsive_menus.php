<?php

/**
 * @file
 * Generate form elements for the Responsive Menu settings.
 */

$responsive_menu_breakpoint_group = theme_get_setting('settings.responsive_menu_breakpoint_group', $theme);
$responsive_menu_breakpoints = $breakpoints[$responsive_menu_breakpoint_group];

// Breakpoint options.
$rmb_group_options = [];
foreach ($responsive_menu_breakpoints as $rmb_key => $rmb_value) {
  $rmb_group_options[$rmb_value->getMediaQuery()] = $rmb_value->getLabel() . ': ' . $rmb_value->getMediaQuery();
}

// Menu blocks.
if (!empty($theme_blocks)) {
  $default_value = theme_get_setting('settings.responsive_menu_block', $theme);
  foreach ($theme_blocks as $block_key => $block_values) {
    // Support System module and Menu Block module blocks.
    if ($block_values->getPlugin()->getPluginDefinition()['id'] === 'system_menu_block' || $block_values->getPlugin()->getPluginDefinition()['id'] === 'menu_block') {
      $menu_blocks[$block_values->id()] = $block_values->label() . ' (' . $block_values->id() . ')';
    }
  }
}
else {
  $menu_blocks['bummer'] = '-- no menu blocks available --';
}

// Menu style options.
$responsive_menu_options = [
  'none' => t('-- none --'),
  'horizontal' => t('Horizontal'),
  'vertical'   => t('Vertical'),
  'dropmenu'   => t('Drop menu'),
  'slidedown'  => t('Slide down'),
  'meganav'    => t('Mega nav'),
  'offcanvas'  => t('Off canvas'),
  'overlay'    => t('Overlay'),
  'tiles'      => t('Tiles'),
];

$tiles_count = [
  'two' => 2,
  'three' => 3,
  'four' => 4,
];

$form['responsive_menus'] = [
  '#type' => 'details',
  '#title' => t('Responsive Menus'),
  '#group' => 'extension_settings',
  '#description' => t('<h3>Responsive Menus</h3><p>Select a menu and breakpoint group, then a specific breakpoint for the responsive style. You can configure one default style and optionally a responsive style.</p><p>It is recommended to follow a mobile first approach where the <i>responsive style</i> is the one you typically associate with <i>desktop view</i>, and the <i>default style</i> is for small screens such as mobile touch devices.</p>'),
];

$form['responsive_menus']['default_settings'] = [
  '#type' => 'fieldset',
  '#attributes' => ['class' => ['clearfix']],
];

// Menu.
$form['responsive_menus']['default_settings']['settings_responsive_menu_block'] = [
  '#type' => 'select',
  '#title' => t('Menu'),
  '#options' => $menu_blocks,
  // '#default_value' => theme_get_setting('settings.responsive_menu_block', $theme),
  '#default_value' => $default_value,

];

// Breakpoints group.
$form['responsive_menus']['default_settings']['settings_responsive_menu_breakpoint_group'] = [
  '#type' => 'select',
  '#title' => t('Breakpoint group'),
  '#options' => $breakpoint_options,
  '#default_value' => $responsive_menu_breakpoint_group,
];

// Breakpoint.
$form['responsive_menus']['default_settings']['settings_responsive_menu_breakpoint'] = [
  '#type' => 'select',
  '#title' => t('Breakpoint'),
  '#options' => $rmb_group_options,
  '#default_value' => theme_get_setting('settings.responsive_menu_breakpoint', $theme),
  '#states' => [
    'enabled' => ['select[name="settings_responsive_menu_breakpoint_group"]' => ['value' => $responsive_menu_breakpoint_group]],
  ],
];

// Change message.
$form['responsive_menus']['default_settings']['responsive_menu_breakpoint_group_haschanged'] = [
  '#type' => 'container',
  '#markup' => t('<em>Save the extension settings to change the breakpoint group and update breakpoint options.</em>'),
  '#attributes' => ['class' => ['warning', 'messages', 'messages--warning']],
  '#states' => [
    'invisible' => ['select[name="settings_responsive_menu_breakpoint_group"]' => ['value' => $responsive_menu_breakpoint_group]],
  ],
];

// Menu styles.
$form['responsive_menus']['styles'] = [
  '#type' => 'fieldset',
  '#attributes' => ['class' => ['responsive-menu-styles']],
  '#states' => [
    'enabled' => ['select[name="settings_responsive_menu_breakpoint_group"]' => ['value' => $responsive_menu_breakpoint_group]],
  ],
];

// Default.
$form['responsive_menus']['styles']['settings_responsive_menu_default_style'] = [
  '#type' => 'select',
  '#title' => t('Default style'),
  '#options' => $responsive_menu_options,
  '#default_value' => theme_get_setting('settings.responsive_menu_default_style', $theme),
];

// Responsive.
$form['responsive_menus']['styles']['settings_responsive_menu_responsive_style'] = [
  '#type' => 'select',
  '#title' => t('Responsive style'),
  '#options' => $responsive_menu_options,
  '#default_value' => theme_get_setting('settings.responsive_menu_responsive_style', $theme),
];

// Click menus.
$form['responsive_menus']['click_menus'] = [
  '#type' => 'details',
  '#title' => t('Click Menus'),
  // '#description' => t('These settings only apply to the responsive style (i.e. the "desktop style").'),
];

$form['responsive_menus']['click_menus']['settings_click_menus_enabled'] = [
  '#type' => 'checkbox',
  '#title' => t('Use folding click menus for Off Canvas, Slide Down, Overlay and Vertical menu items.'),
  '#default_value' => theme_get_setting('settings.click_menus_enabled', $theme),
];

if (!isset($getThemeInfo['base theme original'])) {
  $form['responsive_menus']['click_menus']['upgrade_message'] = [
    '#type' => 'container',
    '#markup' => t('Themes generated with AT RC1 and prior are not compatible with click menus, <a href=":upgrade_message" target="_blank"><b>click here for upgrade instructions</b></a>.', [':upgrade_message' => 'https://www.drupal.org/node/2753187']),
  ];
  $form['responsive_menus']['click_menus']['settings_click_menus_enabled']['#disabled'] = TRUE;
}
