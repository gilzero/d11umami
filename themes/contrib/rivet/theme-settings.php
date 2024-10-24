<?php

/**
 * @file
 * Contains the theme's settings form.
 */

use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hook_form_system_theme_settings_alter().
 */
function rivet_form_system_theme_settings_alter(&$form, FormStateInterface &$form_state, $form_id = NULL) {
  // Work-around for a core bug affecting admin themes. See issue #943212.
  if (isset($form_id)) {
    return;
  }

  // Our site name setting integrates with the site_settings module and requires
  // a site_name variable be created (useful for multisite), which will override
  // Drupal's system site name. You can also provide a specific overridden value
  // here, if necessary.
  $site_name = Drupal::configFactory()->get('system.site')->get('name');
  $site_name_source = 'system basic site settings';
  $site_name_link = '/admin/config/system/site-information';
  if (
    \Drupal::hasService('site_settings.loader') &&
    $site_settings = \Drupal::service('site_settings.loader')
  ) {
    $settings = $site_settings->loadAll();
    $site_name = $settings['site_name'] ?? $site_name;
    $site_name_source = 'site settings';
    $site_name_link = '/admin/content/site-settings';
  }

  $form['theme_settings']['#open'] = FALSE;
  $form['logo']['#open'] = FALSE;
  $form['logo']['#group'] = 'header_options';

  $form['favicon']['#open'] = FALSE;
  $form['favicon']['default_favicon']['#default_value'] = theme_get_setting('favicon.use_default');

  $form['header_options'] = [
    '#type' => 'details',
    '#title' => t('Header options'),
    '#open' => TRUE,
    '#weight' => -10,
  ];
  $form['header_options']['site_name'] = [
    '#type' => 'textfield',
    '#title' => t('Site name'),
    '#default_value' => theme_get_setting('site_name'),
    '#attributes' => ['placeholder' => $site_name],
    '#description' => t('Leave blank to inherit site name “%site_name” from <a href="@site_name_link">@site_name_source</a>.', [
      '%site_name' => $site_name,
      '@site_name_source' => $site_name_source,
      '@site_name_link' => $site_name_link,
    ]),
  ];
  $form['header_options']['subtitle'] = [
    '#type' => 'textfield',
    '#title' => t('Sub title'),
    '#description' => t('Appears below site name. Use to designate a Campus, School, College, or parent administrative unit.'),
    '#attributes' => [
      'placeholder' => t('E.g., Indiana University Bloomington'),
    ],
    '#default_value' => theme_get_setting('subtitle'),
  ];

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
    '#default_value' => theme_get_setting('hide_home_breadcrumb') ?? '2',
  ];

  $form['footer_options'] = [
    '#type' => 'details',
    '#title' => t('Footer options'),
    '#open' => TRUE,
    '#weight' => -9,
  ];

  $form['footer_options']['footer_variant'] = [
    '#type' => 'radios',
    '#default_value' => theme_get_setting('footer_variant') ?? 'default',
    '#options' => [
      'default' => t('Maroon (default)'),
      'light' => t('Light variant'),
    ],
    '#title' => t('Footer color variant'),
    '#description' => t('Branding guidelines require each site to have an IU-branded footer.'),
  ];

  $form['footer_options']['privacy_url'] = [
    '#type' => 'textfield',
    '#default_value' => theme_get_setting('privacy_url'),
    '#title' => t('Privacy notice url'),
    '#description' => t('Include "http://" in the link if the notice is on another site, or an absolute path beginning with "/" if using a page on this website.<br>Read more about <a href="https://policies.iu.edu/policies/ispp-24-web-privacy-notices">ISPP 24 - The University Policy on Website Privacy Notices</a> and the associated <a target="_blank" href="https://informationsecurity.iu.edu/policies/ispp24faq.html">FAQ</a>. You may <a target="_blank" href="https://privacygen.iu.edu/">generate web copy for a compliant Privacy notice at privacygen.iu.edu</a>.'),
  ];

  $form['theme_settings']['sidebar_border_right'] = [
    '#type' => 'checkbox',
    '#title' => t('Rivet sidebar vertical border separator'),
    '#return_value' => 1,
    '#weight' => -10,
    '#default_value' => theme_get_setting('sidebar_border_right') ?? 1,
  ];

  $form['layout_options'] = [
    '#type' => 'details',
    '#title' => t('Layout options'),
    '#open' => TRUE,
    '#weight' => -8,
  ];
  $form['layout_options']['page_width'] = [
    '#type' => 'radios',
    '#title' => t('Header/footer global page width'),
    '#default_value' => theme_get_setting('page_width') ?? 'rvt-container-lg',
    '#options' => [
      'rvt-container-xl' => 'X-Large Container',
      'rvt-container-lg' => 'Large Container [Default]',
      'rvt-container-md' => 'Medium Container',
      'rvt-container-sm' => 'Small Container',
      'full-width' => 'Full width (application)',
    ],
  ];
  $form['layout_options']['content_width'] = [
    '#type' => 'radios',
    '#title' => t('One-column content width'),
    '#default_value' => theme_get_setting('content_width') ?? 'rvt-container-md',
    '#options' => [
      'rvt-container-xl' => 'X-Large Container',
      'rvt-container-lg' => 'Large Container',
      'rvt-container-md' => 'Medium Container [Default]',
      'rvt-container-sm' => 'Small Container',
    ],
    '#description' => 'Note: The two-column content width must match the global page width specified above.',
  ];
  $form['#submit'][] = 'rivet_settings_submit';
}

/**
 * Custom form submit handler for rivet_form_system_theme_settings_alter().
 */
function rivet_settings_submit($form, FormStateInterface &$form_state) {
  $submitted = $form_state->getUserInput();
  if ($submitted['site_name'] == NULL) {
    $form_state->setValue('site_name', '');
  }
  if ($submitted['subtitle'] == NULL) {
    $form_state->setValue('subtitle', '');
  }
}
