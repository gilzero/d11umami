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

$form['solo_settings']['settings_global_misc'] = [
  '#type' => 'details',
  '#title' => t("<strong class='error'>Global Site</strong> Settings - Solo Theme"),
  '#collapsible' => TRUE,
  '#collapsed' => FALSE,
];

$form['solo_settings']['settings_global_misc']['site_global_width'] = [
  '#title' => t("Website's Width - Solo Theme"),
  '#type' => 'select',
  '#options' => _get_site_width_options(),
  '#default_value' => theme_get_setting('site_global_width') ?? 'sw-1600',
  '#description' => t("<p><strong>You can adjust the entire website's width by choosing your preferred size in pixels or rem units.</strong></p>"),
];

// Check if the solo_utilities module is enabled and if the Solo theme or its
// sub-themes are active.
if (\Drupal::moduleHandler()->moduleExists('solo_utilities') && solo_utilities__check_solo_theme_hierarchy_active()) {
  // Checkbox to enable custom widths for individual nodes.
  $form['solo_settings']['settings_global_misc']['enable_custom_node_width'] = [
    '#type' => 'checkbox',
    '#title' => t('Tick this box to enable custom widths for individual nodes'),
    '#default_value' => theme_get_setting('enable_custom_node_width'),
    '#description' => t('Allow setting custom widths for individual nodes. Once enabled, you will see a dropdown in the node edit form where you can select a custom width for each node. If "None" is selected, the default width will be used. Individual node widths take priority over content type-specific widths, which take priority over the global width.'),
  ];
}
else {
  // Display a message if the module is not enabled or the Solo theme or its
  // sub-themes are not active.
  $form['solo_settings']['settings_global_misc']['enable_custom_node_width_message'] = [
    '#type' => 'item',
    '#title' => t('Enable custom widths for individual nodes'),
    '#markup' => t('To enable custom widths for individual nodes, please ensure that the <a href="https://www.drupal.org/project/solo_utilities" target="_blank">Solo Utilities module</a> is installed and enabled. Additionally, this feature requires that the Solo theme or one of its sub-themes is actively being used on the site. The Solo theme must be set as the default theme or be a parent, grandparent, or part of the active theme hierarchy in any capacity.'),
  ];
}

// Checkbox to enable custom widths for each content type.
$form['solo_settings']['settings_global_misc']['enable_custom_widths'] = [
  '#type' => 'checkbox',
  '#title' => t('Enable custom widths for each content type'),
  '#default_value' => theme_get_setting('enable_custom_widths'),
];

// Collapsible details element for content type specific width settings.
$form['solo_settings']['settings_global_misc']['content_type_widths'] = [
  '#type' => 'details',
  '#title' => t('Content Type Widths'),
  '#open' => TRUE,
  '#states' => [
    'visible' => [
      ':input[name="enable_custom_widths"]' => ['checked' => TRUE],
    ],
  ],
];

// Content type specific width settings.
$global_width = theme_get_setting('site_global_width');
$content_types = _solo_get_content_types();
foreach ($content_types as $type => $label) {
  $form['solo_settings']['settings_global_misc']['content_type_widths']['site_width_' . $type] = [
    '#title' => t("Width for @content_type", ['@content_type' => $label]),
    '#type' => 'select',
    '#options' => _get_site_width_options(),
    '#default_value' => theme_get_setting('site_width_' . $type) ?? $global_width,
    '#description' => t("<p><strong>You can adjust the width for the @content_type content type by choosing your preferred size in pixels or rem units.</strong></p>", ['@content_type' => $label]),
    '#states' => [
      'visible' => [
        ':input[name="enable_custom_widths"]' => ['checked' => TRUE],
      ],
    ],
  ];
}

$form['solo_settings']['settings_global_misc']['site_global_breakpoints'] = [
  '#title' => t("Website's Breakpoints - Solo Theme"),
  '#type' => 'select',
  '#options' => _get_site_breakpoints_options(),
  '#default_value' => theme_get_setting('site_global_breakpoints') ?? 'sb-992',
  '#description' => t("<p><strong>Use them to adjust the global layout for a particular screen or device size.</strong></p>"),
];

$form['solo_settings']['settings_global_misc']['site_menu_breakpoints'] = [
  '#title' => t("Menu's Breakpoints - Solo Theme"),
  '#type' => 'select',
  '#options' => _get_menu_breakpoints_options(),
  '#default_value' => theme_get_setting('site_menu_breakpoints') ?? 'mn-992',
  '#description' => t("<p><strong>Adjust the global layout for any responsive menu on a specific screen or device size using them.</strong></p>"),
];

$form['solo_settings']['settings_global_misc']['site_global_regions_gap'] = [
  '#title' => t("Website Region's Spaces"),
  '#type' => 'select',
  '#options' => _get_site_global_regions_gap(),
  '#default_value' => theme_get_setting('site_global_regions_gap') ?? '16',
  '#description' => t("<p><strong>You can adjust the entire website's padding by choosing your preferred size in pixels or rem units.</strong></p>"),
];

$form['solo_settings']['settings_global_misc']['site_global_font_size'] = [
  '#title' => t("Website's Font Size"),
  '#type' => 'select',
  '#options' => _get_site_global_font_size(),
  '#default_value' => theme_get_setting('site_global_font_size') ?? '16',
  '#description' => t("<p><strong>You can adjust the entire website's font size by choosing your preferred size in pixels or rem units.</strong></p>"),
];

$form['solo_settings']['settings_global_misc']['site_node_date_format'] = [
  '#title' => t("Choose the node date format."),
  '#type' => 'select',
  '#options' => _get_site_node_date_format($d_s, $d_m, $d_l),
  '#default_value' => theme_get_setting('site_node_date_format') ?? 'solo_date_l',
];

$form['solo_settings']['settings_global_misc']['site_import_google_font'] = [
  '#type' => 'textfield',
  '#title' => t('Import Google Font'),
  '#description' => t("To import a Google font, follow these steps:<br>

Step 1: Obtain the Google Fonts URL. You can do this by visiting Google Fonts and copying the URL. For example, <strong class='info'>https://fonts.googleapis.com/css2?family=Teko:wght@300;400;500;600;700&display=swap</strong>.<br>

Step 2: Inject the CSS rules to specify the font families in the field below. For instance,
<strong class='info'>h1, h2, h3 {font-family: 'Teko', sans-serif <span class='error'>!important;</span>}</strong>.<br>

<span class='error'>Note</span>: If you are using Global Site Fonts or Heading Fonts, you must include <span class='error'>!important</span>; in your CSS.
 "),
  '#default_value' => theme_get_setting('site_import_google_font'),
];

$form['solo_settings']['settings_global_misc']['site_css_injector'] = [
  '#type' => 'textarea',
  '#title' => t('CSS Injector'),
  '#description' => t("Use this field to inject custom CSS directly into the page head on every page. This method is best for small snippets of CSS that modify the look and feel of your site globally. Changes take effect immediately and are stored within the theme settings, making it a convenient option for quick adjustments or tests."),
  '#default_value' => theme_get_setting('site_css_injector'),
];

$form['solo_settings']['settings_global_misc']['site_css_dynamic'] = [
  '#type' => 'textarea',
  '#title' => t('CSS Dynamic'),
  '#description' => t("Enter your CSS here to have it written into a dedicated CSS file that's loaded across your site. This approach is ideal for more substantial CSS customizations that benefit from being cached as a separate file. It's particularly useful for performance optimization on larger sites, as it ensures that your custom styles are cached by browsers and not inlined on every page load. It is saved in the public file system path sites/*/files/solo/css/solo-css-dynamic.css."),
  '#default_value' => theme_get_setting('site_css_dynamic'),
];

$form['solo_settings']['settings_global_misc']['site_js_dynamic'] = [
  '#type' => 'textarea',
  '#title' => t('JavaScript Dynamic'),
  '#description' => t("Enter your JavaScript code here to be saved into a separate, dedicated file that will be loaded across your site. Ideal for extensive JavaScript that benefits from browser caching, this approach helps in optimizing site performance by reducing page load times. By including this JavaScript, you'll automatically have access to a set of Drupal's core libraries within your scripts, including jQuery, AJAX functionalities, Drupal behaviors, drupalSettings, and once. This means you can leverage these powerful features directly in your custom JavaScript, enhancing your site's interactivity and functionality without needing to manually include these libraries. It is saved in the public file system path sites/*/files/solo/js/solo-js-dynamic.js."),
  '#default_value' => theme_get_setting('site_js_dynamic'),
];

$form['solo_settings']['settings_global_misc']['site_login_page_on'] = [
  '#type' => 'checkbox',
  '#title' => t('Tick this box to modify the layout for the login, register, and password reset pages.'),
  '#description'   => t('<p>The login form will be centralized, with the left and right sides concealed.</p>'),
  '#default_value' => theme_get_setting('site_login_page_on'),
];

$form['solo_settings']['settings_global_misc']['site_flip_header_menu'] = [
  '#type' => 'checkbox',
  '#title' => t('Tick this box to flip the main menu order. It will be displayed before the header.'),
  '#default_value' => theme_get_setting('site_flip_header_menu'),
];

$form['solo_settings']['settings_global_misc']['site_opacity_onscroll_on'] = [
  '#type' => 'checkbox',
  '#title' => t('Tick this box to animate opacity while scrolling down.'),
  '#description'   => t('<p>Upon visiting the page, the hidden blocks remain unseen. They only begin to animate and become visible as you scroll down. However, this feature is disabled by default.</p>'),
  '#default_value' => theme_get_setting('site_opacity_onscroll_on'),
];

$form['solo_settings']['settings_global_misc']['site_inline_items_on'] = [
  '#type' => 'checkbox',
  '#title' => t('Tick this box to display multiple taxonomy term field items on the same line.'),
  '#description'   => t("<p>In the current setup, when there's a field with multiple values, those values are shown one beneath the other in a block layout. This can use a significant amount of vertical space, especially if there are numerous values. However, this new feature modifies this layout. Instead of displaying the values on separate lines, they will be shown side by side on the same line, with spaces separating them. This change will result in a more condensed and easily readable display</p>"),
  '#default_value' => theme_get_setting('site_inline_items_on'),
];

$form['solo_settings']['settings_global_misc']['color_coded_system_tabs'] = [
  '#type' => 'checkbox',
  '#title' => t('Tick this box to enhanced navigation with color-coded system tabs'),
  '#description'   => t('<p>This tailored CSS script enriches the user interface of the website by assigning bespoke colors to core system tab links, such as View, Edit, Delete, and Revisions.</p>'),
  '#default_value' => theme_get_setting('color_coded_system_tabs'),
];

$form['solo_settings']['settings_global_misc']['site_reading_mode_content_type'] = [
  '#type' => 'select',
  '#title' => t('Reading Mode (45rem/720px)'),
  '#default_value' => theme_get_setting('site_reading_mode_content_type') ?? '',
  '#options' => _solo_get_content_types(),
  '#multiple' => TRUE,
  '#description' => t("A common guideline for the maximum width of a text block for comfortable reading is around 60 to 75 characters per line. This guideline helps to prevent the text from becoming too difficult to follow from one line to the next. Choose the content type for which 'Reading Mode' should be enabled, and set the maximum width for the content region to 45rem/720px for both Text Formatted Long and Text Formatted Long with Summary. It is for Default/Full view mode only."),
];
