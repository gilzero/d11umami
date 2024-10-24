<?php

/**
 * @file
 * Add custom theme settings to the IU Framework theme.
 */

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hook_form_system_theme_settings_alter().
 */
function iu_form_system_theme_settings_alter(&$form, FormStateInterface &$form_state, $form_id = NULL) {
  // Work-around for a core bug affecting admin themes. See issue #943212.
  if (isset($form_id)) {
    return;
  }

  $form['breadcrumbs'] = [
    '#type' => 'details',
    '#title' => t('Breadcrumb'),
    '#open' => TRUE,
  ];

  $form['breadcrumbs']['hide_home_breadcrumb'] = [
    '#type' => 'radios',
    '#options' => [
      '0' => t('Show %home in breadcrumb trail (Drupal’s default behavior)', ['%home' => 'Home']),
      '1' => t('Remove %home from breadcrumb trail', ['%home' => 'Home']),
      '2' => t('Remove %home when it is the only breadcrumb in trail', ['%home' => 'Home']),
    ],
    '#default_value' => theme_get_setting('hide_home_breadcrumb'),
  ];

  $form['branding_bar'] = [
    '#type' => 'details',
    '#title' => t('Branding bar'),
    '#open' => TRUE,
    '#description' => '<p>' . t('Specify which campus should be linked in the header.') . '</p>',
  ];

  $form['branding_bar']['campus'] = [
    '#type' => 'radios',
    '#options' => [
      'iu'    => 'Indiana University (iu.edu)',
      'iub'   => 'IU Bloomington (indiana.edu)',
      'iue'   => 'IU East (iue.edu)',
      'iuk'   => 'IU Kokomo (iuk.edu)',
      'iun'   => 'IU Northwest (iun.edu)',
      'iupui' => 'IUPUI (iupui.edu)',
      'ius'   => 'IU Southeast (ius.edu)',
      'iusb'  => 'IU South Bend (iusb.edu)',
      'iufw'  => 'IU Fort Wayne (iufw.edu)',
      'iupuc' => 'IUPUC (iupuc.edu)',
    ],
    '#description' => t('The header branding bar features the Block IU in a red bar and goes at the top of every page of your website. Each campus has its own branding bar, but only the name and link change.'),
    '#default_value' => theme_get_setting('campus'),
  ];

  $form['off_canvas'] = [
    '#type' => 'details',
    '#title' => t('Mobile menu position'),
    '#open' => TRUE,
  ];

  $off_canvas_position = theme_get_setting('off_canvas_position') ?: 'right';
  $form['off_canvas']['off_canvas_position'] = [
    '#type' => 'radios',
    '#options' => [
      'right' => t('Slide in from right side (IU Framework’s default behavior)'),
      'left' => t('Slide in from left side'),
    ],
    '#default_value' => $off_canvas_position,
  ];

  $colors = _iu_secondary_color_palette_options();
  $options = [];

  foreach ($colors as $key => $color) {
    $options[$key] = '
<div class="color-block ' . $color['class'] . '">
  <div class="color-name">' . $color['name'] . '</div>
  <div class="color-details">HEX: ' . $color['hex'] . '</div>
</div>
    ';
  }

  $form['color_palette'] = [
    '#type' => 'details',
    '#title' => t('Color palette'),
    '#open' => TRUE,
  ];

  $form['color_palette']['secondary_color'] = [
    '#type' => 'radios',
    '#title' => t('Secondary background color (for sections)'),
    '#options' => $options,
    '#attributes' => ['class' => ['color-block-radios']],
    '#default_value' => theme_get_setting('secondary_color'),
    '#description'   => t('In addition to the two primary background colors (crimson and light gray) available for use in your site sections, you may also select one additional secondary background color from the expanded IU Color Palette. Note: Crimson is Indiana University’s main brand color and should always be used in greater frequency than any secondary color.'),
    '#attached' => ['library' => ['iu/color-block']],
  ];

  $form['#validate'][] = 'iu_form_system_theme_settings_validate';
  $form['#submit'][] = 'iu_form_system_theme_settings_submit';
}

/**
 * Form vallidation handler for system_theme_settings form.
 */
function iu_form_system_theme_settings_validate($form, FormStateInterface $form_state) {
  // Set a flag to indicate whether caches need to be cleared.
  $flush_caches = FALSE;
  $settings = [
    [
      'branding_bar',
      'campus',
    ],
    [
      'color_palette',
      'secondary_color',
    ],
    [
      'off_canvas',
      'off_canvas_position',
    ],
  ];
  foreach ($settings as $setting) {
    $original_value = NestedArray::getValue($form, array_merge($setting, ['#default_value']), $key_exists);
    if ($key_exists) {
      $submitted_value = $form_state->getValue(array_pop($setting));
      if ($submitted_value !== $original_value) {
        $flush_caches = TRUE;
        break;
      }
    }
  }
  $form_state->set('flush_caches', $flush_caches);
}

/**
 * Form submission handler for system_theme_settings form.
 */
function iu_form_system_theme_settings_submit($form, FormStateInterface $form_state) {
  // Clear cached data so a change will take effect.
  if ($form_state->get('flush_caches')) {
    drupal_flush_all_caches();
  }
}
