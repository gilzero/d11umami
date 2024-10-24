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
function revolt_form_system_theme_settings_alter(&$form, FormStateInterface $form_state) {
  if ($form['#attributes']['class'][0] == 'system-theme-settings') {
    $form['#attached']['library'][] = 'revolt/theme.setting';
  }

  // Verticle tabs.
  $form['revolt_tab'] = [
    '#type' => 'vertical_tabs',
    '#prefix' => '<h2><small>' . t('Revolt settings') . '</small></h2>',
    '#weight' => -10,
  ];
  $form['layout'] = [
    '#type' => 'details',
    '#title' => t('Header'),
    '#group' => 'revolt_tab',
  ];
  $form['layout']['stickey_header'] = [
    '#type' => 'checkbox',
    '#title' => t('Stickey Header'),
    '#default_value' => theme_get_setting('stickey_header', 'revolt'),
    '#description'   => t("Check this option to make header stickey."),
  ];
  $form['layout']['header_width'] = [
    '#type' => 'checkbox',
    '#title' => t('Full Width Header'),
    '#default_value' => theme_get_setting('header_width', 'revolt'),
    '#description'   => t("Check this option to make header full width."),
  ];
  $form['layout']['header_background'] = [
    '#title' => t('Header background'),
    '#type' => 'select',
    '#description' => t('Select the desired background for the header.'),
    '#options' => [
      'white' => t('White'),
      'dark-gray' => t('Dark-Gray'),
      'gray' => t('Gray'),
    ],
    '#default_value' => theme_get_setting("header_background", "revolt"),
  ];
  $form['layout']['header_class'] = [
    '#title' => t('Header custom class'),
    '#type' => 'textfield',
    '#default_value' => theme_get_setting("header_class", "revolt"),
  ];

  // Banner fields.
  $form['Banner'] = [
    '#type' => 'details',
    '#title' => t('Banner settings'),
    '#group' => 'revolt_tab',
  ];
  $form['Banner']['vt_banner']['slideshow'] = [
    '#type' => 'details',
    '#title' => t('Front Page Banner'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  ];
  $form['Banner']['vt_banner']['slideshow']['banner_display'] = [
    '#type' => 'checkbox',
    '#title' => t('Show Banner'),
    '#default_value' => theme_get_setting('banner_display', 'revolt'),
    '#description'   => t("Check this option to show Banner in front page. Uncheck to hide."),
  ];

  $form['Banner']['vt_banner']['slideshow']['banner_align'] = [
    '#title' => t('Banner content align'),
    '#type' => 'select',
    '#description' => t('Select the desired content alignment.'),
    '#options' => [
      'start' => t('START'),
      'center' => t('CENTER'),
      'end' => t('END'),
    ],
    '#default_value' => theme_get_setting("banner_align", "revolt"),
  ];
  $form['Banner']['vt_banner']['slideshow']['banner_title'] = [
    '#type' => 'textfield',
    '#title' => t('Banner Title'),
    '#default_value' => theme_get_setting("banner_title", "revolt"),
  ];
  $form['Banner']['vt_banner']['slideshow']['banner_image'] = [
    '#type' => 'managed_file',
    '#title' => t('Banner Image'),
    '#description' => t('Use same size for all the Banner images(Recommented size : 1920 X 603).'),
    '#default_value' => theme_get_setting("banner_image", "revolt"),
    '#upload_location' => 'public://',
  ];
  $form['Banner']['vt_banner']['slideshow']['banner_desc'] = [
    '#type' => 'text_format',
    '#title' => t('Description'),
    '#default_value' => theme_get_setting("banner_desc", "revolt")['value'],
    '#format' => 'full_html',
  ];
  $form['Banner']['vt_banner']['slideshow']['banner_url'] = [
    '#type' => 'textfield',
    '#title' => t('Banner URL'),
    '#default_value' => theme_get_setting("banner_url", "revolt"),
  ];
  $form['Banner']['vt_banner']['slideshow']['banner_url_title'] = [
    '#type' => 'textfield',
    '#title' => t('Banner title'),
    '#default_value' => theme_get_setting("banner_url_title", "revolt"),
  ];

}
