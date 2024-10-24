<?php

/**
 * @file
 * Generate form elements for the Image styles settings.
 */

// Breakpoints.
$breakpoint_group_images = theme_get_setting('settings.breakpoint_group_images', $theme) ?: '';
if (empty($breakpoint_group_images)) {
  $breakpoint_group_images = theme_get_setting('settings.breakpoint_group_layout', $theme);
}
$image_breakpoints = $breakpoints[$breakpoint_group_images];

$image_alignment_options = [
  'none'   => t('None'),
  'left'   => t('Left'),
  'center' => t('Center'),
  'right'  => t('Right'),
];

$form['images'] = [
  '#type' => 'details',
  '#title' => t('Image Field Settings'),
  '#group' => 'extension_settings',
];

$form['images']['image-settings'] = [
  '#type' => 'fieldset',
  '#title' => t('Image Field Settings'),
  '#weight' => 0,
];

$form['images']['image-settings']['description'] = [
  '#markup' => '<h3>Image Field Settings</h3><p>Set alignment, caption display and image count per entity type and view mode. Entity types Node, Comment, Custom Blocks and Paragraphs are supported.</p>',
];

// Breakpoint group.
$form['images']['image-settings']['breakpoint_group'] = [
  '#type' => 'fieldset',
  '#title' => t('Image Alignment Breakpoints'),
  '#description' => t('Select the breakpoint group. You must save the extension settings for it to take effect, then reconfigure image alignment for each breakpoint.'),
];

$form['images']['image-settings']['breakpoint_group']['settings_breakpoint_group_images'] = [
  '#type' => 'select',
  '#options' => $breakpoint_options,
  '#title' => t('Breakpoint group'),
  '#default_value' => $breakpoint_group_images,
];

foreach ($breakpoints as $group_message_key => $group_message_values) {
  if ($group_message_values !== []) {
    foreach ($group_message_values as $breakpoint_message_key => $breakpoint_message_values) {
      $breakpoint_message[$group_message_key][] = '<dt>' . $breakpoint_message_values->getLabel() . ':</dt><dd>' . $breakpoint_message_values->getMediaQuery() . '</dd>';
    }
    $form['images']['image-settings']['breakpoint_group'][$group_message_key]['bygroup_breakpoints'] = [
      '#type' => 'container',
      '#markup' => '<dl class="breakpoint-group-values">' . implode("\n", $breakpoint_message[$group_message_key]) . '</dl>',
      '#states' => [
        'visible' => ['select[name="settings_breakpoint_group_images"]' => ['value' => $group_message_key]],
      ],
    ];
  }
}

if (!empty($entity_types)) {
  foreach ($entity_types as $entity_type_key => $entity_type_values) {

    $form['images']['image-settings'][$entity_type_key] = [
      '#type' => 'details',
      '#title' => t($entity_type_key),
      '#collapsed' => TRUE,
    ];

    foreach ($entity_type_values as $evk => $etv) {
      if ($entity_type_key === 'paragraphs' || $entity_type_key === 'comment' || $entity_type_key === 'block_content') {
        $entity_type_id = $etv->id();
        $entity_type_label = $etv->label();
      }
      elseif ($entity_type_key === 'node') {
        $entity_type_id = $etv->get('type');
        $entity_type_label = $etv->get('name');
      }

      $form['images']['image-settings'][$entity_type_key][$entity_type_id]['entity_type_' . $entity_type_id] = [
        '#type'      => 'details',
        '#title'     => t($entity_type_label),
        '#collapsed' => TRUE,
        '#attributes' => ['class' => ['image-alignment-entity-type-details']],
      ];

      // Alignment settings.
      $form['images']['image-settings'][$entity_type_key][$entity_type_id]['entity_type_' . $entity_type_id]['alignment'] = [
        '#type'      => 'details',
        '#title'     => t('Alignment'),
        '#collapsed' => TRUE,
        '#description' => t('Set image field alignment per view mode and breakpoint.'),
      ];

      if (!empty($image_breakpoints)) {
        foreach ($image_breakpoints as $image_breakpoint_id => $image_breakpoint_value) {

          $breakpoint_ia_label = $image_breakpoint_value->getLabel();
          $breakpoint_ia_mediaquery = $image_breakpoint_value->getMediaQuery();

          // There is probably a way to get the bp machine name but I could not find a method.
          $breakpoint_ia_key = strtolower(preg_replace("/\W|_/", "", $breakpoint_ia_label));

          $form['images']['image-settings'][$entity_type_key][$entity_type_id]['entity_type_' . $entity_type_id]['alignment'][$breakpoint_ia_key] = [
            '#type' => 'details',
            '#title' => t($breakpoint_ia_label . ' <small>' . $breakpoint_ia_mediaquery . '</small>'),
            '#attributes' => ['class' => ['clearfix', 'image-alignment-details']],
          ];

          $form['images']['image-settings'][$entity_type_key][$entity_type_id]['entity_type_' . $entity_type_id]['alignment'][$breakpoint_ia_key]['table_image_align'] = [
            '#type' => 'table',
            '#header' => [t('Display mode'), t('Align'), t('Margin (px)')],
            // '#header' => [t('Display mode'), t('Align'), t('Margin')],
            '#empty' => t('No view modes to display'),
            '#attributes' => ['class' => ['image-align-options-table']],
          ];

          foreach ($view_modes[$entity_type_key] as $display_mode) {
            // View mode labels.
            $display_mode_label = t('Display mode: ') . $display_mode['label'];
            $display_mode_id = str_replace('.', '_', $display_mode['id']);

            // We need BC. Set all breakpoints to use the old settings unless the
            // new setting is isset...
            $old_setting = theme_get_setting('settings.image_alignment_' . $entity_type_id . '_' . $display_mode_id);
            $new_setting = theme_get_setting('settings.image_alignment_' . $entity_type_id . '_' . $breakpoint_ia_key . '_' . $display_mode_id);
            if (isset($new_setting)) {
              $setting = $new_setting;
            }
            else {
              $setting = $old_setting;
            }

            // View mode.
            $form['images']['image-settings'][$entity_type_key][$entity_type_id]['entity_type_' . $entity_type_id]['alignment'][$breakpoint_ia_key]['table_image_align'][$display_mode_id]['view_mode'] = [
              '#type' => 'container',
              '#markup' => '<span>' . $display_mode['label'] . '</span>',
            ];

            // Align options.
            $form['images']['image-settings'][$entity_type_key][$entity_type_id]['entity_type_' . $entity_type_id]['alignment'][$breakpoint_ia_key]['table_image_align'][$display_mode_id]['options']['settings_image_alignment_' . $entity_type_id . '_' . $breakpoint_ia_key . '_' . $display_mode_id] = [
              '#type'          => 'radios',
              '#default_value' => $setting,
              '#options'       => $image_alignment_options,
              '#attributes'    => ['class' => ['clearfix']],
            ];

            // Margins.
            $form['images']['image-settings'][$entity_type_key][$entity_type_id]['entity_type_' . $entity_type_id]['alignment'][$breakpoint_ia_key]['table_image_align'][$display_mode_id]['margins']['settings_image_alignment_' . $entity_type_id . '_' . $breakpoint_ia_key . '_' . $display_mode_id . '_top'] = [
              '#type' => 'number',
              '#title' => t('Top'),
              // '#field_suffix' => 'px <small>(coverts to em)</small>',
              '#default_value' => theme_get_setting('settings.image_alignment_' . $entity_type_id . '_' . $breakpoint_ia_key . '_' . $display_mode_id . '_top') ?: 0,
              '#attributes' => [
                'min' => 0,
                'max' => 999,
                'step' => 1,
                'class' => ['margin-option']
              ],
            ];
            $form['images']['image-settings'][$entity_type_key][$entity_type_id]['entity_type_' . $entity_type_id]['alignment'][$breakpoint_ia_key]['table_image_align'][$display_mode_id]['margins']['settings_image_alignment_' . $entity_type_id . '_' . $breakpoint_ia_key . '_' . $display_mode_id . '_right'] = [
              '#type' => 'number',
              '#title' => t('Right'),
              '#default_value' => theme_get_setting('settings.image_alignment_' . $entity_type_id . '_' . $breakpoint_ia_key . '_' . $display_mode_id . '_right') ?: 0,
              '#attributes' => [
                'min' => 0,
                'max' => 999,
                'step' => 1,
                'class' => ['margin-option']
              ],
              '#states' => [
                '!visible' => ['input[name="table_image_align[' . $display_mode_id . '][options][settings_image_alignment_' . $entity_type_id . '_' . $breakpoint_ia_key . '_' . $display_mode_id . ']' => ['checked' => TRUE, 'value' => 'center']],
              ],
            ];
            $form['images']['image-settings'][$entity_type_key][$entity_type_id]['entity_type_' . $entity_type_id]['alignment'][$breakpoint_ia_key]['table_image_align'][$display_mode_id]['margins']['settings_image_alignment_' . $entity_type_id . '_' . $breakpoint_ia_key . '_' . $display_mode_id . '_bottom'] = [
              '#type' => 'number',
              '#title' => t('Bottom'),
              // '#field_suffix' => 'px <small>(coverts to em)</small>',
              '#default_value' => theme_get_setting('settings.image_alignment_' . $entity_type_id . '_' . $breakpoint_ia_key . '_' . $display_mode_id . '_bottom') ?: 0,
              '#attributes' => [
                'min' => 0,
                'max' => 999,
                'step' => 1,
                'class' => ['margin-option']
              ],
            ];
            $form['images']['image-settings'][$entity_type_key][$entity_type_id]['entity_type_' . $entity_type_id]['alignment'][$breakpoint_ia_key]['table_image_align'][$display_mode_id]['margins']['settings_image_alignment_' . $entity_type_id . '_' . $breakpoint_ia_key . '_' . $display_mode_id . '_left'] = [
              '#type' => 'number',
              '#title' => t('Left'),
              // '#field_suffix' => 'px <small>(coverts to em)</small>',
              '#default_value' => theme_get_setting('settings.image_alignment_' . $entity_type_id . '_' . $breakpoint_ia_key . '_' . $display_mode_id . '_left') ?: 0,
              '#attributes' => [
                'min' => 0,
                'max' => 999,
                'step' => 1,
                'class' => ['margin-option']
              ],
              '#states' => [
                '!visible' => ['input[name="table_image_align[' . $display_mode_id . '][options][settings_image_alignment_' . $entity_type_id . '_' . $breakpoint_ia_key . '_' . $display_mode_id . ']' => ['checked' => TRUE, 'value' => 'center']],
              ],
            ];
          }
        }
      }

      // Caption setting.
      $form['images']['image-settings'][$entity_type_key][$entity_type_id]['entity_type_' . $entity_type_id]['captions'] = [
        '#type'        => 'details',
        '#title'       => t('Captions'),
        '#collapsed'   => TRUE,
        '#description' => t('Show captions per display mode. Captions use the "Title" option and must be enabled in the image field settings.'),
      ];
      foreach ($view_modes[$entity_type_key] as $display_mode) {
        // View mode labels.
        $display_mode_label = $display_mode['label'];
        $display_mode_id = str_replace('.', '_', $display_mode['id']);

        $form['images']['image-settings'][$entity_type_key][$entity_type_id]['entity_type_' . $entity_type_id]['captions'][$display_mode_id]['settings_image_captions_' . $entity_type_id . '_' . $display_mode_id] = [
          '#type'          => 'checkbox',
          '#title'         => $display_mode_label,
          '#default_value' => theme_get_setting('settings.image_captions_' . $entity_type_id . '_' . $display_mode_id),
        ];
      }

      // Image count settings.
      $form['images']['image-settings'][$entity_type_key][$entity_type_id]['entity_type_' . $entity_type_id]['count'] = [
        '#type'        => 'details',
        '#title'       => t('Image Count'),
        '#collapsed'   => TRUE,
        '#description' => t('Restrict to <b>one image</b> only in certain display modes - useful for teaser mode when you have multi-value or unlimited images.'),
      ];
      foreach ($view_modes[$entity_type_key] as $display_mode) {
        // View mode labels.
        $display_mode_label = $display_mode['label'];
        $display_mode_id = str_replace('.', '_', $display_mode['id']);

        $form['images']['image-settings'][$entity_type_key][$entity_type_id]['entity_type_' . $entity_type_id]['count'][$display_mode_id]['settings_image_count_' . $entity_type_id . '_' . $display_mode_id] = [
          '#type'          => 'checkbox',
          '#title'         => $display_mode_label,
          '#default_value' => theme_get_setting('settings.image_count_' . $entity_type_id . '_' . $display_mode_id),
        ];
      }
    }
  }
}
