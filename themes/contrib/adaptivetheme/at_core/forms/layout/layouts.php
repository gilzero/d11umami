<?php

/**
 * @file
 * Generate form elements for the Layout settings.
 */

use Drupal\at_core\Layout\LayoutCompatible;
use Drupal\Component\Utility\Html;
use Drupal\Core\Extension\ThemeExtensionList;

$layout_data = new LayoutCompatible($theme);
$layout_compatible_data = $layout_data->getCompatibleLayout();

$layout_config = $layout_compatible_data['layout_config'];
$css_config = $layout_compatible_data['css_config'];

// Breakpoints.
$breakpoints_group_layout = theme_get_setting('settings.breakpoint_group_layout', $theme);
$layout_breakpoints = $breakpoints[$breakpoints_group_layout];

// Template suggestions.
$template_suggestions = [];
$template_suggestions['page'] = 'page';

// Get the suggestions from config.
// Each time a new suggestion is created we will save it to config settings during submit.
foreach ($config as $config_key => $config_value) {
  if (substr($config_key, 0, 16) == 'suggestion_page_') {
    if (!empty($config_value) && $config_value !== 'page') {
      $clean_config_value = Html::escape($config_value);
      $template_suggestions['page__' . $clean_config_value] = 'page__' . $clean_config_value;
    }
  }
}

// Checkbox setting that keeps the layouts details form open.
$layouts_form_open = theme_get_setting('settings.layouts_form_open', $theme);

$form['layouts'] = [
  '#type' => 'details',
  '#title' => t('Layouts'),
  '#open' => $layouts_form_open,
  '#attributes' => ['class' => ['clearfix']],
  '#weight' => -200,
];

// Attached required CSS and JS libraries and files.
$form['layouts']['#attached']['library'][] = $css_config['layout_provider'] . '/layout_settings';

// Enable layouts, this is a master setting that totally disables the page layout system.
$form['layouts']['layouts-enable-container'] = [
  '#type' => 'container',
  '#attributes' => ['class' => ['subsystem-enabled-container', 'layouts-column-onequarter']]
];

$form['layouts']['layouts-enable-container']['settings_layouts_form_open'] = [
  '#type' => 'checkbox',
  '#title' => t('Keep open'),
  '#default_value' => $layouts_form_open,
  '#states' => [
    'disabled' => ['input[name="settings_layouts_enable"]' => ['checked' => FALSE]],
  ],
];

$form['layouts']['layouts-enable-container']['settings_layouts_enable'] = [
  '#type' => 'checkbox',
  '#title' => t('Enable'),
  '#default_value' => theme_get_setting('settings.layouts_enable', $theme),
];

//
// Layout SELECT
// ---------------------------------------------------------------------------------.

$form['layouts']['layout_select'] = [
  '#type' => 'fieldset',
  '#title' => t('Select Layouts'),
  '#attributes' => ['class' => ['layouts-column', 'layouts-column-threequarters', 'column-select-layouts']],
  '#states' => [
    'visible' => ['input[name="settings_layouts_enable"]' => ['checked' => TRUE]],
  ],
];

// Push hidden settings into the form so they can be used during submit, to build the css output, saves us
// having to get this data again during submit.
$form['layouts']['layout_select']['settings_suggestions'] = [
  '#type' => 'hidden',
  '#value' => $template_suggestions,
];

foreach ($template_suggestions as $suggestion_key => $suggestions_name) {
  if ($suggestions_name == 'page') {
    $suggestions_name = 'page (default)';
  }
  else {
    $suggestions_name = str_replace('__', ' ', $suggestions_name);
  }

  $form['layouts']['layout_select'][$suggestion_key] = [
    '#type' => 'details',
    '#title' => t($suggestions_name),
    '#attributes' => ['class' => ['clearfix']],
    '#states' => [
      'enabled' => ['select[name="breakpoint_group_layout"]' => ['value' => $breakpoints_group_layout]],
    ],
  ];

  if ($suggestion_key !== 'page') {
    $form['layouts']['layout_select'][$suggestion_key]['delete_suggestion_' . $suggestion_key] = [
      '#type' => 'checkbox',
      '#title' => t('Delete this suggestion.'),
      '#default_value' => FALSE,
    ];
  }

  if (!empty($layout_breakpoints)) {
    foreach ($layout_breakpoints as $layout_breakpoint_id => $layout_breakpoint_value) {

      $breakpoint_layout_label = $layout_breakpoint_value->getLabel();
      $breakpoint_layout_mediaquery = $layout_breakpoint_value->getMediaQuery();

      // There is probably a way to get the bp machine name but I could not find a method.
      $breakpoint_layout_key = strtolower(preg_replace("/\W|_/", "", $breakpoint_layout_label));

      $form['layouts']['layout_select'][$suggestion_key][$breakpoint_layout_key] = [
        '#type' => 'details',
        '#title' => t($breakpoint_layout_label . ' <small>' . $breakpoint_layout_mediaquery . '</small>'),
        '#attributes' => ['class' => ['clearfix']],
      ];

      if (!empty($layout_config['rows'])) {
        $group_class = Html::cleanCssIdentifier($theme . ' ' . $suggestion_key . ' ' . $breakpoint_layout_key);
        $row_count = count($layout_config['rows']);

        // kint($group_class);

        $form['layouts']['layout_select'][$suggestion_key][$breakpoint_layout_key]['table_layout_settings'] = [
          '#type' => 'table',
          '#header' => [t('Layout'), t('Order'), t('Hide')],
          '#empty' => t('No rows to display.'),
          '#tabledrag' => [
            [
              'action' => 'order',
              'relationship' => 'sibling',
              'group' => $group_class,
              // 'hidden' => TRUE,
            ],
          ],
          '#attributes' => [
            'class' => ['row-layout-table', 'row-weight'],
            'id' => $group_class,
          ],
        ];

        // Sort rows by weight.
        $rows = [];
        $sw = -1;
        foreach ($layout_config['rows'] as $row_key => $row_values) {
          $sort_weight = theme_get_setting('settings.' . $suggestion_key . '_' . $breakpoint_layout_key . '_' . $row_key . '_weight');
          if (!empty($sort_weight)) {
            $rows[$row_key] = $sort_weight;
          }
          else {
            $rows[$row_key] = ++$sw;
          }
        }
        array_multisort($rows, SORT_ASC, $layout_config['rows']);

        $rw = -1;
        foreach ($layout_config['rows'] as $row_key => $row_values) {
          $reg_count[$row_key] = count($row_values['regions']);
          $row_weight_setting = theme_get_setting('settings.' . $suggestion_key . '_' . $breakpoint_layout_key . '_' . $row_key . '_weight');
          if (!empty($row_weight_setting)) {
            $row_weight = $row_weight_setting;
          }
          else {
            $row_weight = ++$rw;
          }

          // CSS files.
          foreach ($css_config['css'] as $css_key => $css_values) {
            if ($css_values['regions'] == $reg_count[$row_key]) {
              foreach ($css_values['files'] as $css_file) {
                // Convert to associative array, we need the key.
                $css_options[$row_key][$css_file] = str_replace('-', ' ', $css_file);
              }
            }
          }

          // Only print rows that have regions, maybe they don't...
          if ($reg_count[$row_key]) {

            // Build markup for the visual display thingee.
            $regions_markup = [];
            $markup[$row_key] = '';
            $reg_num = 1;
            $row_label = ucfirst(str_replace('_', ' ', $row_key));

            if ($reg_count[$row_key] > 1) {
              for ($i = 0; $i < $reg_count[$row_key]; $i++) {
                $regions_markup[$row_key][] = '<div class="l-r region"><span>R' . $reg_num++ . '</span></div>';
              }
              $markup[$row_key] = implode('', $regions_markup[$row_key]);
            }
            else {
              $markup[$row_key] = '<div class="l-r region"><span>R1</span></div>';
            }

            // Try to inherit the default page layout, by default.
            if (NULL !== theme_get_setting('settings.' . $suggestion_key . '_' . $breakpoint_layout_key . '_' . $row_key)) {
              $row_default_value = theme_get_setting('settings.' . $suggestion_key . '_' . $breakpoint_layout_key . '_' . $row_key);
            }
            else {
              $row_default_value = theme_get_setting('settings.page_' . $breakpoint_layout_key . '_' . $row_key);
            }

            // Mark the table row as draggable.
            $form['layouts']['layout_select'][$suggestion_key][$breakpoint_layout_key]['table_layout_settings'][$row_key]['#attributes']['class'] = ['draggable'];

            $form['layouts']['layout_select'][$suggestion_key][$breakpoint_layout_key]['table_layout_settings'][$row_key]['layout']['settings_' . $suggestion_key . '_' . $breakpoint_layout_key . '_' . $row_key] = [
              '#type' => 'select',
              '#empty_option' => '--none--',
              '#title' => t($row_label),
              '#options' => $css_options[$row_key],
              '#default_value' => $row_default_value,
              '#attributes' => ['class' => ['row-layout-select']],
              // '#states' => ['disabled' => ['input[name="table[' . $row_key . '][hide][settings_' . $suggestion_key . '_' . $breakpoint_layout_key . '_' . $row_key . '_hide]"' => ['checked' => TRUE]]],
            ];

            $form['layouts']['layout_select'][$suggestion_key][$breakpoint_layout_key]['table_layout_settings'][$row_key]['layout']['layout_preview'] = [
              '#type' => 'container',
              '#attributes' => ['class' => ['layout-preview']],
            ];

            $form['layouts']['layout_select'][$suggestion_key][$breakpoint_layout_key]['table_layout_settings'][$row_key]['layout']['layout_preview'][$suggestion_key . '-' . $breakpoint_layout_key . '-' . $row_key . '-row_region_markup'] = [
              '#type' => 'container',
              '#markup' => '<div class="l-rw regions arc--' . $reg_count[$row_key] . '">' . $markup[$row_key] . '</div>',
              '#attributes' => ['class' => ['layout-option-not-set', $row_default_value]],
            ];

            $form['layouts']['layout_select'][$suggestion_key][$breakpoint_layout_key]['table_layout_settings'][$row_key]['weight']['settings_' . $suggestion_key . '_' . $breakpoint_layout_key . '_' . $row_key . '_weight'] = [
              '#type' => 'weight',
              '#title' => t('Weight for @title', ['@title' => $row_label]),
              '#title_display' => 'invisible',
              '#delta' => $row_count,
              '#weight' => $row_weight,
              '#default_value' => $row_weight,
              '#attributes' => ['class' => [$group_class, 'row-weight']],
              '#states' => [
                'disabled' => ['input[name="table_layout_settings[' . $row_key . '][hide][settings_' . $suggestion_key . '_' . $breakpoint_layout_key . '_' . $row_key . '_hide]"' => ['checked' => TRUE]],
              ],
            ];

            $form['layouts']['layout_select'][$suggestion_key][$breakpoint_layout_key]['table_layout_settings'][$row_key]['hide']['settings_' . $suggestion_key . '_' . $breakpoint_layout_key . '_' . $row_key . '_hide'] = [
              '#type' => 'checkbox',
              '#title' => t('Hide @title in this breakpoint', ['@title' => $row_label]),
              '#title_display' => 'invisible',
              '#default_value' => theme_get_setting('settings.' . $suggestion_key . '_' . $breakpoint_layout_key . '_' . $row_key . '_hide'),
            ];
          }
        }
      }
    }
  }
}

// Suggestions container.
$form['layouts']['layout_select']['suggestions'] = [
  '#type' => 'details',
  '#title' => t('Add new suggestion'),
];

// Suggestions input and help.
// $suggestion_plugin_message = isset($default_plugin) ? $default_plugin : '-- not set --';.
$form['layouts']['layout_select']['suggestions']['ts_name'] = [
  '#type' => 'textfield',
  '#size' => 20,
  '#field_prefix' => 'page--',
  '#field_suffix' => '.html.twig',
  '#description' => [
    '#theme' => 'item_list',
    '#list_type' => 'ol',
    '#attributes' => ['class' => ['suggestions-ts-name-desc']],
    '#items' => [
      t('Enter the template suggestion. Only enter the modifier, e.g. for "page--front" enter "front" (without quotes).'),
      t('Save the layout settings.'),
      t('After saving the suggestion configure a layout for it. If no layout is set it will use the default layout.'),
    ],
    '#suffix' => t('<p>Find page suggestions by turning on the Devel extension in Advanced settings and enable the option: <em>Show Page Suggestions</em>. Reload a page in the site and the suggestions will be shown in the messages area.</p>'),
  ],
];

// Layout OPTIONS
// ---------------------------------------------------------------------------------.
$form['layouts']['adv_options'] = [
  '#type' => 'fieldset',
  '#title' => t('Options'),
  '#attributes' => ['class' => ['layouts-column', 'layouts-column-onequarter']],
  '#states' => [
    'visible' => ['input[name="settings_layouts_enable"]' => ['checked' => TRUE]],
  ],
];

$form['layouts']['adv_options']['description'] = [
  '#markup' => t('<h3>Options</h3>'),
];

// Breakpoint group.
$form['layouts']['adv_options']['breakpoint_group'] = [
  '#type' => 'details',
  '#title' => t('Breakpoints'),
  '#description' => t('Select the breakpoint group. You must save the layout settings for it to take effect, then reconfigure your layouts.'),
];

$form['layouts']['adv_options']['breakpoint_group']['settings_breakpoint_group_layout'] = [
  '#type' => 'select',
  '#options' => $breakpoint_options,
  '#title' => t('Breakpoint group'),
  '#default_value' => $breakpoints_group_layout,
];

foreach ($breakpoints as $group_message_key => $group_message_values) {
  if ($group_message_values !== []) {
    foreach ($group_message_values as $breakpoint_message_key => $breakpoint_message_values) {
      $breakpoint_message[$group_message_key][] = '<dt>' . $breakpoint_message_values->getLabel() . ':</dt><dd>' . $breakpoint_message_values->getMediaQuery() . '</dd>';
    }
    $form['layouts']['adv_options']['breakpoint_group'][$group_message_key]['bygroup_breakpoints'] = [
      '#type' => 'container',
      '#markup' => '<dl class="breakpoint-group-values">' . implode("\n", $breakpoint_message[$group_message_key]) . '</dl>',
      '#states' => [
        'visible' => ['select[name="settings_breakpoint_group_layout"]' => ['value' => $group_message_key]],
      ],
    ];
  }
}

// Change message.
$form['layouts']['adv_options']['breakpoint_group']['layouts_breakpoint_group_haschanged'] = [
  '#type' => 'container',
  '#markup' => t('<em>Save the layout settings to change the breakpoint group and update the layout breakpoints.</em>'),
  '#attributes' => ['class' => ['warning', 'messages', 'messages--warning']],
  '#states' => [
    'invisible' => ['select[name="settings_breakpoint_group_layout"]' => ['value' => $breakpoints_group_layout]],
  ],
];

// Max width.
$max_width_units = [
  'em'  => 'em',
  'rem' => 'rem',
  '%'   => '%',
  'vw'  => 'vw',
  'px'  => 'px',
];

$form['layouts']['adv_options']['select']['max_width'] = [
  '#type' => 'details',
  '#title' => t('Max width'),
  '#collapsed' => TRUE,
  '#collapsible' => TRUE,
  '#description' => t('Override the global max-width and per row.'),
];

$form['layouts']['adv_options']['select']['max_width']['settings_max_width_enable'] = [
  '#type' => 'checkbox',
  '#title' => t('Override max-widths'),
  '#default_value' => theme_get_setting('settings.max_width_enable'),
];

$form['layouts']['adv_options']['select']['max_width']['global'] = [
  '#type' => 'details',
  '#title' => t('Global max width'),
  '#collapsed' => FALSE,
  '#collapsible' => TRUE,
  '#states' => [
    'invisible' => ['input[name="settings_max_width_enable"]' => ['checked' => FALSE]],
  ],
];

$form['layouts']['adv_options']['select']['max_width']['global']['settings_max_width_value'] = [
  '#type' => 'number',
  '#title' => t('Value'),
  '#default_value' => Html::escape(theme_get_setting('settings.max_width_value')),
  '#attributes' => [
    'min' => 0,
    'max' => 9999,
    'step' => 1,
  ],
];

$form['layouts']['adv_options']['select']['max_width']['global']['settings_max_width_unit'] = [
  '#type' => 'select',
  '#title' => t('Unit'),
  '#options' => $max_width_units,
  '#default_value' => theme_get_setting('settings.max_width_unit'),
];

$form['layouts']['adv_options']['select']['max_width']['settings_max_width_enable_rows'] = [
  '#type' => 'checkbox',
  '#title' => t('Override max-width per row'),
  '#default_value' => theme_get_setting('settings.max_width_enable_rows'),
  '#states' => [
    'invisible' => ['input[name="settings_max_width_enable"]' => ['checked' => FALSE]],
  ],
];

if (!empty($layout_config['rows'])) {
  foreach ($layout_config['rows'] as $row_key => $row_values) {

    $form['layouts']['adv_options']['select']['max_width'][$row_key] = [
      '#type' => 'details',
      '#title' => t(str_replace('_', ' ', $row_key)),
      '#collapsed' => TRUE,
      '#collapsible' => TRUE,
      '#states' => [
        'invisible' => ['input[name="settings_max_width_enable_rows"]' => ['checked' => FALSE]],
      ],
    ];

    $form['layouts']['adv_options']['select']['max_width'][$row_key]['settings_max_width_value_' . $row_key] = [
      '#type' => 'number',
      '#title' => t('Value'),
      '#default_value' => Html::escape(theme_get_setting('settings.max_width_value_' . $row_key)),
      '#attributes' => [
        'min' => 0,
        'max' => 9999,
        'step' => 1,
      ],
    ];

    $form['layouts']['adv_options']['select']['max_width'][$row_key]['settings_max_width_unit_' . $row_key] = [
      '#type' => 'select',
      '#title' => t('Unit'),
      '#options' => $max_width_units,
      '#default_value' => theme_get_setting('settings.max_width_unit_' . $row_key),
    ];
  }
}

// Backups.
$form['layouts']['adv_options']['backups'] = [
  '#type' => 'details',
  '#title' => t('Backups'),
  '#description' => t('Adaptivetheme can automatically save backups for page templates and your themes info.yml file, since both of these can change when you save a layout. Backups are saved to your themes "backup" folder.'),
  '#collapsed' => TRUE,
  '#collapsible' => TRUE,
];

// Disable backups.
$form['layouts']['adv_options']['backups']['settings_enable_backups'] = [
  '#type' => 'checkbox',
  '#title' => t('Enable backups'),
  '#default_value' => theme_get_setting("settings.enable_backups", $theme),
  // '#description' => t('Warning: un-checking this option will disable backups.'),
];

// Submit button for layouts.
$form['layouts']['actions'] = [
  '#type' => 'actions',
  '#attributes' => ['class' => ['submit--layout']],
];

$form['layouts']['actions']['submit'] = [
  '#type' => 'submit',
  '#value' => t('Save layout settings'),
  '#validate' => ['at_core_validate_layouts'],
  '#submit' => ['at_core_submit_layouts'],
  '#button_type' => 'primary',
];

// Layout submit handlers.
include_once \Drupal::service('extension.list.theme')->getPath('at_core') . '/forms/layout/layouts_validate.php';
include_once \Drupal::service('extension.list.theme')->getPath('at_core') . '/forms/layout/layouts_submit.php';
