<?php

/**
 * @file
 * Generate form elements for the $titles Styles settings.
 */

$form['titles'] = [
  '#type' => 'details',
  '#title' => t('Titles'),
  '#group' => 'extension_settings',
];

$form['titles']['description'] = [
  '#markup' => t('<h3>Title Styles</h3><p>Set title case, weight, alignment and letter-spacing.</p><p>Semi-bold and light font-weight options will only work if the font supports those weights, otherwise these typically render as bold and normal respectively.</p>'),
];

// Array of valid title types.
$titles_valid_types = title_valid_type_options();

// Get the fonts list.
$font_elements = font_elements();

// Build form elements for each selector and style.
foreach ($font_elements as $font_element_key => $font_element_value) {
  if (in_array($font_element_key, $titles_valid_types)) {
    // Title element.
    $form['titles'][$font_element_key . '_element'] = [
      '#type' => 'details',
      '#title' => t($font_element_value['label']),
    ];
    // Case.
    $form['titles'][$font_element_key . '_element']['settings_titles_' . $font_element_key . '_case'] = [
      '#type' => 'select',
      '#title' => t('Case'),
      '#default_value' => theme_get_setting('settings.titles_' . $font_element_key . '_case'),
      '#options' => title_style_options('case'),
    ];
    // Weight.
    $form['titles'][$font_element_key . '_element']['settings_titles_' . $font_element_key . '_weight'] = [
      '#type' => 'select',
      '#title' => t('Weight'),
      '#default_value' => theme_get_setting('settings.titles_' . $font_element_key . '_weight'),
      '#options' => title_style_options('weight'),
    ];
    // Alignment.
    $form['titles'][$font_element_key . '_element']['settings_titles_' . $font_element_key . '_alignment'] = [
      '#type' => 'select',
      '#title' => t('Alignment'),
      '#default_value' => theme_get_setting('settings.titles_' . $font_element_key . '_alignment'),
      '#options' => title_style_options('alignment'),
    ];
    // Letter spacing.
    $form['titles'][$font_element_key . '_element']['settings_titles_' . $font_element_key . '_letterspacing'] = [
      '#type' => 'number',
      '#title' => t('Letter spacing'),
      '#max-length' => 2,
      '#step' => 0.1,
      '#field_suffix' => 'px',
      '#default_value' => theme_get_setting('settings.titles_' . $font_element_key . '_letterspacing'),
      '#attributes' => [
        'min' => -10,
        'max' => 10,
        'step' => 0.1,
        'class' => ['font-option']
      ],
    ];
  }
}
