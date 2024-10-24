<?php

/**
 * @file
 * Generate settings for Mobile blocks.
 */

$mobile_blocks_breakpoint_group = theme_get_setting('settings.mobile_blocks_breakpoint_group', $theme) ?: 'at_core.simple';
$mobile_blocks_breakpoints = $breakpoints[$mobile_blocks_breakpoint_group];

// Breakpoints.
foreach ($mobile_blocks_breakpoints as $mbs_key => $mbs_value) {
  $mbs_query = $mbs_value->getMediaQuery();
  $mbs_group_options[$mbs_query] = $mbs_value->getLabel() . ': ' . $mbs_query;
}

$form['mobile-blocks'] = [
  '#type' => 'details',
  '#title' => t('Mobile Blocks'),
  '#group' => 'extension_settings',
  '#description' => t('<h3>Mobile Blocks</h3><p>Hide or show blocks in breakpoints (wide, tablet, mobile etc).</p><ol><li>Select a breakpoint group. Stepped breakpoints are recommended.</li><li>Check hide or show to set the blocks visibility in that breakpoint. If using cascading breakpoints you may need to explicitly set visibility for a block in each breakpoint, otherwise unchecked blocks always show.</li></ol></p>'),
];

// Breakpoints group.
$form['mobile-blocks']['settings_mobile_blocks_breakpoint_group'] = [
  '#type' => 'select',
  '#title' => t('Breakpoint group'),
  '#options' => $breakpoint_options,
  '#default_value' => $mobile_blocks_breakpoint_group,
];

// Change message.
$form['mobile-blocks']['mobile_blocks_breakpoint_group_haschanged'] = [
  '#type' => 'container',
  '#markup' => t('<em>Save the extension settings to change the breakpoint group and update breakpoint options.</em>'),
  '#attributes' => ['class' => ['warning', 'messages', 'messages--warning']],
  '#states' => [
    'invisible' => ['select[name="settings_mobile_blocks_breakpoint_group"]' => ['value' => $mobile_blocks_breakpoint_group]],
  ],
];

foreach ($mobile_blocks_breakpoints as $mbs_key => $mbs_value) {
  $mbs_query = $mbs_value->getMediaQuery();
  $mbs_label = strtolower($mbs_value->getLabel());
  $mbs_group_options[$mbs_query] = $mbs_value->getLabel() . ': ' . $mbs_query;

  $form['mobile-blocks']['breakpoints']['bp' . $mbs_label] = [
    '#type' => 'details',
    '#title' => t($mbs_label . ' <small>' . $mbs_query . '</small>'),
  ];

  // Blocks.
  if (!empty($theme_blocks)) {
    foreach ($theme_blocks as $block_key => $block_values) {
      $block_id = $block_values->id();
      $plugin_id = $block_values->getPluginId();
      $block_plugin = str_replace(':', '_', $plugin_id);
      $block_label = $block_values->label();
      $old_default_value_show = theme_get_setting('settings.mobile_block_show_' . 'bp' . $mbs_label . '_' . $block_id, $theme);
      $old_default_value_hide = theme_get_setting('settings.mobile_block_hide_' . 'bp' . $mbs_label . '_' . $block_id, $theme);

      if (!empty($old_default_value_show)) {
        $default_value_show = $old_default_value_show;
      }
      else {
        $default_value_show = theme_get_setting('settings.mobile_block_show_' . 'bp' . $mbs_label . '_' . $block_plugin, $theme) ?: '';
      }

      if (!empty($old_default_value_hide)) {
        $default_value_hide = $old_default_value_hide;
      }
      else {
        $default_value_hide = theme_get_setting('settings.mobile_block_hide_' . 'bp' . $mbs_label . '_' . $block_plugin, $theme) ?: '';
      }

      $form['mobile-blocks']['breakpoints']['bp' . $mbs_label][$block_plugin] = [
        '#type' => 'fieldset',
        '#title' => $block_label,
        '#markup' => '<div class="mobile-blocks-title layouts-column-threequarters align-left"><h4 class="h4">' . $block_label . '</h4><small><b>Block id:</b> ' . $block_key . '</small> <br><small><b>Plugin id:</b> ' . $plugin_id . '</small></div>',
        '#attributes' => ['class' => ['clearfix']],
      ];

      $form['mobile-blocks']['breakpoints']['bp' . $mbs_label][$block_plugin]['container'] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['align-right']],
      ];

      $form['mobile-blocks']['breakpoints']['bp' . $mbs_label][$block_plugin]['container']['settings_mobile_block_show_' . 'bp' . $mbs_label . '_' . $block_plugin] = [
        '#type' => 'checkbox',
        '#title' => t('Show'),
        '#default_value' => $default_value_show,
        '#states' => [
          'disabled' => ['input[name="settings_mobile_block_hide_' . 'bp' . $mbs_label . '_' . $block_plugin . '"]' => ['checked' => TRUE]],
        ],
      ];

      $form['mobile-blocks']['breakpoints']['bp' . $mbs_label][$block_plugin]['container']['settings_mobile_block_hide_' . 'bp' . $mbs_label . '_' . $block_plugin] = [
        '#type' => 'checkbox',
        '#title' => t('Hide'),
        '#default_value' => $default_value_hide,
        '#states' => [
          'disabled' => ['input[name="settings_mobile_block_show_' . 'bp' . $mbs_label . '_' . $block_plugin . '"]' => ['checked' => TRUE]],
        ],
      ];
    }
  }
}
