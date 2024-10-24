<?php

/**
 * @file
 */

/**
 * Implements hook_theme().
 */

use Drupal\Core\Extension\ThemeExtensionList;

function at_core_theme() {
  $path = \Drupal::service('extension.list.theme')->getPath('at_core');
  return [
    // Define the row template/hook.
    'row' => [
      'variables' => [
        'regions' => NULL
      ],
      'template' => 'row',
      'path' => $path . '/templates/layout',
    ],
    // Ensure suggestions are picked up in the base theme first.
    'row__header' => [
      'base hook' => 'row',
      'path' => $path . '/templates/layout',
    ],
    'row__footer' => [
      'base hook' => 'row',
      'path' => $path . '/templates/layout',
    ],
    // Template suggestion for the breadcrumb page title placeholder.
    'page_title__breadcrumb' => [
      'base hook' => 'page_title',
      'path' => $path . '/templates/content',
    ],
  ];
}
