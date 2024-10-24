<?php

/**
 * @file
 * Generate form elements for the Shortcodes settings.
 */

use Drupal\at_core\Layout\LayoutCompatible;
use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\Xss;
use Symfony\Component\Yaml\Parser;

$layout_data = new LayoutCompatible($theme);
$layout_compatible_data = $layout_data->getCompatibleLayout();
$layout_config = $layout_compatible_data['layout_config'];

$shortcodes_yml = $subtheme_path . '/' . $theme . '.shortcodes.yml';
if (file_exists($shortcodes_yml)) {
  $shortcodes_parser = new Parser();
  $shortcodes = $shortcodes_parser->parse(file_get_contents($shortcodes_yml));
}

$page_elements = [
  'body' => 'body',
  'page' => '.page wrapper',
];

$form['shortcodes'] = [
  '#type' => 'details',
  '#title' => t('Shortcodes'),
  '#group' => 'extension_settings',
  '#description' => t('<h3>Shortcode CSS Classes</h3><p>Shortcodes are CSS classes that can add style, layout or behavior (such as an animation effect) to various page elements like blocks or regions.</p><p>To use enter comma separated lists of CSS class names in the available fields. You may need to <a href="/admin/config/development/performance" target="_blank"><b>clear the cache</b></a> after adding or removing classes</span>.</p>'),
];

// Page.
$form['shortcodes']['page_classes'] = [
  '#type' => 'details',
  '#title' => t('Body, Page'),
];
foreach ($page_elements as $page_elements_key => $page_elements_value) {
  $form['shortcodes']['page_classes']['settings_page_classes_' . $page_elements_key] = [
    '#type' => 'textfield',
    '#title' => t($page_elements_value),
    '#default_value' => Html::escape(theme_get_setting('settings.page_classes_' . $page_elements_key, $theme)),
  ];
}

// Rows.
$form['shortcodes']['row_classes'] = [
  '#type' => 'details',
  '#title' => t('Page Rows'),
];
foreach ($layout_config['rows'] as $row_data_key => $row_data_value) {
  $form['shortcodes']['row_classes'][$row_data_key] = [
    '#type' => 'details',
    '#title' => t($row_data_key),
  ];
  // Wrappers.
  $form['shortcodes']['row_classes'][$row_data_key]['settings_page_classes_row_wrapper_' . $row_data_key] = [
    '#type' => 'textfield',
    '#title' => t($row_data_key . ' wrapper'),
    '#default_value' => Html::escape(theme_get_setting('settings.page_classes_row_wrapper_' . $row_data_key, $theme)),
  ];
  // Containers.
  $form['shortcodes']['row_classes'][$row_data_key]['settings_page_classes_row_container_' . $row_data_key] = [
    '#type' => 'textfield',
    '#title' => t($row_data_key . ' container'),
    '#default_value' => Html::escape(theme_get_setting('settings.page_classes_row_container_' . $row_data_key, $theme)),
  ];
}

// Regions
// TODO check if getUntranslatedString() is really the right method to use here.
$form['shortcodes']['region_classes'] = [
  '#type' => 'details',
  '#title' => t('Regions'),
];
foreach ($theme_regions as $region_key => $region_value) {
  $form['shortcodes']['region_classes']['settings_page_classes_region_' . $region_key] = [
    '#type' => 'textfield',
    '#title' => t($region_value->getUntranslatedString()),
    '#default_value' => Html::escape(theme_get_setting('settings.page_classes_region_' . $region_key, $theme)),
  ];
}

// Blocks.
if ($block_module === TRUE) {
  if (isset($theme_blocks) && !empty($theme_blocks)) {
    $form['shortcodes']['block_classes'] = [
      '#type'  => 'details',
      '#title' => t('Blocks'),
    ];
    foreach ($theme_blocks as $block_key => $block_value) {
      $plugin_id = $block_value->getPluginId();
      $block_plugin = str_replace(':', '_', $plugin_id);
      $block_label = $block_value->label();
      // BC - use block plugin ID instead of the key, replace the new setting with
      // the old keyed default.
      $old_default_value = Html::escape(theme_get_setting('settings.block_classes_' . $block_key, $theme));
      if (!empty($old_default_value)) {
        $default_value = $old_default_value;
      }
      else {
        $default_value = theme_get_setting('settings.block_classes_' . $block_plugin, $theme) ?: '';
      }
      $form['shortcodes']['block_classes']['settings_block_classes_' . $block_plugin] = [
        '#type'          => 'textfield',
        '#title'         => $block_label,
        '#default_value' => $default_value,
        '#description'   => '<small><b>Block id:</b> ' . $block_key . '</small> <br><small><b>Plugin id:</b> ' . $plugin_id . '</small>',
      ];
    }
  }
}

// Node types.
if ($node_module === TRUE) {
  $form['shortcodes']['nodetype_classes'] = [
    '#type' => 'details',
    '#title' => t('Content types'),
  ];
  if (isset($node_types) && !empty($node_types)) {
    foreach ($node_types as $nt) {
      $node_type = $nt->get('type');
      $node_type_name = $nt->get('name');

      $form['shortcodes']['nodetype_classes']['settings_nodetype_classes_' . $node_type] = [
        '#type'          => 'textfield',
        '#title'         => t($node_type_name),
        '#default_value' => Html::escape(theme_get_setting('settings.nodetype_classes_' . $node_type, $theme)),
      ];
    }
  }
}

// Comment types.
if ($comment_module === TRUE) {
  $form['shortcodes']['commenttype_classes'] = [
    '#type'  => 'details',
    '#title' => t('Comment types'),
  ];
  if (isset($comment_types) && !empty($comment_types)) {
    foreach ($comment_types as $ct) {
      $comment_type = $ct->id();
      $comment_type_name = $ct->label();

      $form['shortcodes']['commenttype_classes']['settings_commenttype_classes_' . $comment_type] = [
        '#type'          => 'textfield',
        '#title'         => t($comment_type_name),
        '#default_value' => Html::escape(theme_get_setting('settings.commenttype_classes_' . $comment_type, $theme)),
      ];
    }
  }
}

// Actual classes you can apply that are included in the theme.
$form['shortcodes']['title'] = [
  '#type' => 'container',
  '#markup' => '<h3>' . t('Available shortcode classes') . '</h3>',
];
if (!empty($shortcodes)) {
  $form['shortcodes']['available_classes'] = [
    '#type' => 'vertical_tabs',
    '#attributes' => ['class' => ['clearfix']],
  ];
  $class_output = [];
  $class_image = '';
  foreach ($shortcodes as $class_type => $class_values) {

    if (isset($class_values['description'])) {
      $class_type_description = $class_values['description'];
    }
    else {
      $class_type_description = 'No description provided.';
    }

    if (isset($class_values['elements'])) {
      $class_elements = implode(', ', $class_values['elements']);
    }
    else {
      $class_elements = 'Any';
    }

    $form['shortcodes']['classes'][$class_type] = [
      '#type' => 'details',
      '#group' => 'available_classes',
      '#title' => t($class_values['name']),
      '#markup' => t('<h3>' . $class_values['name'] . '</h3><p>' . $class_type_description . '</p><p><b>Use for:</b> <i>' . $class_elements . '</i></p>'),
    ];

    // Use this setting to conditionally load only the CSS we need for this theme.
    $form['shortcodes']['classes'][$class_type]['settings_shortcodes_' . $class_type . '_enable'] = [
      '#type' => 'checkbox',
      '#title' => t('Use ' . $class_values['name'] . ' classes'),
      '#default_value' => theme_get_setting('settings.shortcodes_' . $class_type . '_enable'),
    ];

    // Hide the class names by default to de-clutter the UI.
    $form['shortcodes']['classes'][$class_type][$class_type . '_wrapper'] = [
      '#type' => 'container',
      '#states' => [
        'visible' => ['input[name="settings_shortcodes_' . $class_type . '_enable"]' => ['checked' => TRUE]],
      ],
    ];

    foreach ($class_values['classes'] as $class_key => $class_data) {
      $class_name = Xss::filterAdmin($class_data['class']);
      $class_description = isset($class_data['description']) ? t($class_data['description']) : '';

      // This is a test, very rough and should be generalized to allow any shortcode to supply an image.
      if (isset($class_data['image']) && $class_type == 'patterns') {
        $class_image = $subtheme_path . '/' . $class_data['image'];
        $class_output[$class_type][] = '<dt>' . $class_name . '</dt><dd>' . $class_description . '<div class="pattern-image-clip"><img class="pattern-image" src="/' . $class_image . '" alt="Background image for the ' . $class_name . ' pattern." /></div></dd>';
      }
      else {
        $class_output[$class_type][] = '<dt>' . $class_name . '</dt><dd>' . $class_description . '</dd>';
      }
    }

    $form['shortcodes']['classes'][$class_type][$class_type . '_wrapper'][$class_type . '_classlist'] = [
      '#markup' => '<dl class="class-list ' . $class_type . '">' . implode('', $class_output[$class_type]) . '</dl>',
    ];
  }
}
