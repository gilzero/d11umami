<?php

/**
 * @file
 * Danland theme settings.
 */

use Drupal\file\Entity\File;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hook_form_system_theme_settings_alter().
 */
function danland_form_system_theme_settings_alter(&$form, FormStateInterface $form_state) {
  $form['danland_settings']['slideshow'] = [
    '#type' => 'fieldset',
    '#title' => t('Front Page Slideshow'),
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
  ];
  $form['danland_settings']['slideshow']['slideshow_display'] = [
    '#type' => 'checkbox',
    '#title' => t('Show slideshow'),
    '#default_value' => theme_get_setting('slideshow_display', 'danland'),
    '#description'   => t("Check this option to show Slideshow in front page. Uncheck to hide."),
  ];
  $form['danland_settings']['slideshow']['slide1'] = [
    '#type' => 'fieldset',
    '#title' => t('Slide 1'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  ];
  $form['danland_settings']['slideshow']['slide1']['slide1_image'] = [
    '#type' => 'managed_file',
    '#title' => t('Image 1'),
    '#default_value' => theme_get_setting('slide1_image', 'danland'),
    '#upload_location' => 'public://',
  ];

  $form['danland_settings']['slideshow']['slide2'] = [
    '#type' => 'fieldset',
    '#title' => t('Slide 2'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  ];
  $form['danland_settings']['slideshow']['slide2']['slide2_image'] = [
    '#type' => 'managed_file',
    '#title' => t('Image 2'),
    '#default_value' => theme_get_setting('slide2_image', 'danland'),
    '#upload_location' => 'public://',
  ];
  $form['danland_settings']['slideshow']['slide3'] = [
    '#type' => 'fieldset',
    '#title' => t('Slide 3'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  ];
  $form['danland_settings']['slideshow']['slide3']['slide3_image'] = [
    '#type' => 'managed_file',
    '#title' => t('Image 3'),
    '#default_value' => theme_get_setting('slide3_image', 'danland'),
    '#upload_location' => 'public://',
  ];
  $form['danland_settings']['slideshow']['slideimage'] = [
    '#markup' => t('To change the default Slide Images, Replace the slide-image-1.jpg, slide-image-2.jpg and slide-image-3.jpg in the images folder of the theme folder.'),
  ];
  $form['#submit'][] = 'danland_settings_form_submit';
  $theme = \Drupal::theme()->getActiveTheme()->getName();
  $theme_file = \Drupal::service('extension.list.theme')->getPath($theme) . '/theme-settings.php';
  $build_info = $form_state->getBuildInfo();
  if (!in_array($theme_file, $build_info['files'])) {
    $build_info['files'][] = $theme_file;
  }
  $form_state->setBuildInfo($build_info);
}

/**
 * Theme settings submit handler.
 */
function danland_settings_form_submit(&$form, FormStateInterface $form_state) {
  $account = \Drupal::currentUser();
  $values = $form_state->getValues();
  for ($i = 1; $i <= 3; $i++) {
    if (isset($values["slide{$i}_image"]) && !empty($values["slide{$i}_image"])) {
      // Load the file via file.fid.
      if ($file = File::load($values["slide{$i}_image"][0])) {
        // Change status to permanent.
        $file->setPermanent();
        $file->save();
        $file_usage = \Drupal::service('file.usage');
        $file_usage->add($file, 'user', 'user', $account->id());
      }
    }
  }
}
