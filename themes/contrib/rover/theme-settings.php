<?php

/**
 * @file
 * Implements().
 */

use Drupal\Core\Form\FormStateInterface;

/**
 * @file
 * Harmony_haven theme file.
 */

/**
 * Implements hook_form_system_theme_settings_alter().
 */
function rover_form_system_theme_settings_alter(&$form, FormStateInterface $form_state) {

  // dd($form);
  if ($form['#attributes']['class'][0] == 'system-theme-settings') {
    $form['#attached']['library'][] = 'rover/theme.setting';

    // Verticle tabs.
    $form['rover_tab'] = [
      '#type' => 'vertical_tabs',
      '#prefix' => '<h2><small>' . t('Rover settings') . '</small></h2>',
      '#weight' => -10,
    ];

    // Layout.
    $form['layout'] = [
      '#type' => 'details',
      '#title' => t('Layout'),
      '#group' => 'rover_tab',
    ];
    // Container.
    $form['layout']['container'] = [
      '#type' => 'details',
      '#title' => t('Container'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    ];
    $form['layout']['container']['container_size'] = [
      '#type' => 'select',
      '#title' => t('Container'),
      '#default_value' => theme_get_setting('stickey_header', 'rover'),
      '#options' => [
        'container' => t('Container'),
        'container-md' => t('Container Medium'),
        'container-lg' => t('Container Large'),
        'container-xl' => t('Container Extra Large'),
        'container-xxl' => t('Container Extra Extra Large'),
        'container-fluid' => t('Container Fluid'),
      ],
      '#description' => t('Use <code>.container-XX</code> class. See @bootstrap_fluid_containers_link.', []),
    ];
    // Container.
    $form['layout']['sidebars'] = [
      '#type' => 'details',
      '#title' => t('sidebars'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    ];
    $form['layout']['sidebars']['sidebar_first'] = [
      '#type' => 'select',
      '#title' => t('Sidebar left'),
      '#default_value' => theme_get_setting('sidebar_first', 'rover'),
      '#options' => [
        2 => t('cols-2'),
        3 => t('cols-3'),
        4 => t('cols-4'),
      ],
      '#description' => t('Use <code>.container-XX</code> class. See @bootstrap_fluid_containers_link.', []),
    ];
    $form['layout']['sidebars']['sidebar_second'] = [
      '#type' => 'select',
      '#title' => t('Sidebar right'),
      '#default_value' => theme_get_setting('sidebar_second', 'rover'),
      '#options' => [
        2 => t('cols-2'),
        3 => t('cols-3'),
        4 => t('cols-4'),
      ],
      '#description' => t('Use <code>.container-XX</code> class. See @bootstrap_fluid_containers_link.', []),
    ];

    // Header settings
    // Header.
    $form['header'] = [
      '#type' => 'details',
      '#title' => t('Header'),
      '#group' => 'rover_tab',
    ];
    $form['header']['header_bg'] = [
      '#type' => 'details',
      '#title' => t('Header background colours'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    ];
    $form['header']['header_bg']['header_bg_colors'] = [
      '#type' => 'select',
      '#title' => t('Backgrounds'),
      '#default_value' => theme_get_setting('header_bg_colors', 'rover'),
      '#options' => [
        'bg-primary' => t('Primary background'),
        'bg-success' => t('Success background'),
        'bg-info' => t('Info background'),
        'bg-danger' => t('Danger background'),
        'bg-transparent' => t('Transparent'),
      ],
    ];

    // Banner fields.
    $form['banner'] = [
      '#type' => 'details',
      '#title' => t('Banner settings'),
      '#group' => 'rover_tab',
    ];
    $form['banner']['vt_banner'] = [
      '#type' => 'details',
      '#title' => t('Front Page Banner'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    ];
    $form['banner']['vt_banner']['banner_display'] = [
      '#type' => 'checkbox',
      '#title' => t('Show Banner'),
      '#default_value' => theme_get_setting('banner_display', 'rover'),
      '#description'   => t("Check this option to show Banner in front page. Uncheck to hide."),
    ];

    $form['banner']['vt_banner']['banner_align'] = [
      '#title' => t('Banner content align'),
      '#type' => 'select',
      '#description' => t('Select the desired content alignment.'),
      '#options' => [
        'start' => t('START'),
        'center' => t('CENTER'),
        'end' => t('END'),
      ],
      '#default_value' => theme_get_setting("banner_align", "rover"),
    ];

    $form['banner']['vt_banner']['banner_title'] = [
      '#type' => 'textfield',
      '#title' => t('Banner Title'),
      '#default_value' => theme_get_setting("banner_title", "rover"),
    ];
    $form['banner']['vt_banner']['banner_image'] = [
      '#type' => 'managed_file',
      '#title' => t('Banner Image'),
      '#description' => t('Use same size for all the Banner images(Recommented size : 1920 X 603).'),
      '#default_value' => theme_get_setting("banner_image", "rover"),
      '#upload_location' => 'public://',
    ];
    $form['banner']['vt_banner']['banner_desc'] = [
      '#type' => 'text_format',
      '#title' => t('Description'),
      '#default_value' => theme_get_setting("banner_desc", "rover")['value'],
      '#format' => 'full_html',
    ];
    $form['banner']['vt_banner']['banner_url'] = [
      '#type' => 'textfield',
      '#title' => t('Banner URL'),
      '#default_value' => theme_get_setting("banner_url", "rover"),
    ];
    $form['banner']['vt_banner']['banner_url_title'] = [
      '#type' => 'textfield',
      '#title' => t('Banner title'),
      '#default_value' => theme_get_setting("banner_url_title", "rover"),
    ];

    // Buttons and inputs settings.
    $form['buttons'] = [
      '#type' => 'details',
      '#title' => t('Buttons & Inputs'),
      '#group' => 'rover_tab',
    ];
    $form['buttons']['btn_type'] = [
      '#type' => 'select',
      '#title' => t('Button type'),
      '#default_value' => theme_get_setting('btn_type', 'rover'),
      '#options' => [
        'primary' => t('Primary Button'),
        'secondary' => t('Secondary Button'),
      ],
    ];
    $form['buttons']['btn_size'] = [
      '#type' => 'select',
      '#title' => t('Button size'),
      '#default_value' => theme_get_setting('btn_size', 'rover'),
      '#options' => [
        'btn-md' => t('Regular Button'),
        'btn-sm' => t('Small Button'),
        'btn-lg' => t('Large Button'),
      ],
    ];
  }
}
