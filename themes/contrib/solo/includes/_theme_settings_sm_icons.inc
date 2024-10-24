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

$form['solo_settings']['settings_social_icons'] = [
  '#type' => 'details',
  '#title' => t("<strong class='purple'>Social Media Links</strong> Settings - Solo Theme"),
  '#collapsible' => TRUE,
  '#collapsed' => FALSE,
];

$form['solo_settings']['settings_social_icons']['sm_icon_size'] = [
  '#type' => 'select',
  '#title' => t('The size of the social media icons can be altered from here.'),
  '#default_value' => theme_get_setting('sm_icon_size'),
  '#options' => [
    'sm-icons-small' => t('Small - Default'),
    'sm-icons-medium'  => t('Medium icons'),
    'sm-icons-large'   => t('Large icons'),
  ],
];

$form['solo_settings']['settings_social_icons']['sm_icon_colors'] = [
  '#type' => 'textfield',
  '#maxlength' => 7,
  '#size' => 10,
  '#title' => t('Change Social Media Default Colors'),
  '#description' => t("Select this option if you wish to alter the color of the social media icons."),
  '#default_value' => theme_get_setting('sm_icon_colors'),
  '#attributes' => [
    'pattern' => '^#[a-fA-F0-9]{6}',
  ],
  '#wrapper_attributes' => [
    'data-drupal-selector' => 'solo-color-picker',
  ],
];

$form['solo_settings']['settings_social_icons']['sm_show_icons'] = [
  '#type' => 'checkbox',
  '#title' => t('Show Social Icons'),
  '#default_value' => theme_get_setting('sm_show_icons'),
  '#description' => t("Select this option to display the Social Icon. Deselect it to conceal it."),
];
$social_icons = [
  'facebook' => t('Facebook'),
  'twitter' => t('Formerly (Twitter) X'),
  'pinterest' => t('Pinterest'),
  'linkedin' => t('Linkedin'),
  'instagram' => t('Instagram'),
  'youtube' => t('Youtube'),
  'mastodon' => t('Mastodon'),
  'reddit' => t('Reddit'),
  'whatsapp' => t('WhatsApp'),
  'telegram' => t('Telegram'),
  'snapchat' => t('Snapchat'),
  'threads' => t('Threads'),
  'tiktok' => t('TikTok'),
  'drupal' => t('Drupal'),
  'rss' => t('RSS'),
  'email' => t('Email'),
];

foreach ($social_icons as $key => $value) {
  $form['solo_settings']['settings_social_icons']['sm_icon_' . $key] = [
  // Check if the key is 'email'.
  // '#type' => $key === 'email' ? 'email' : 'textfield',.
    '#type' => 'textfield',
    '#title' => $value,
    '#default_value' => theme_get_setting('sm_icon_' . $key),
  ];
}
