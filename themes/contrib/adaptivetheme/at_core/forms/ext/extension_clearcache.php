<?php

/**
 * @file
 */

use Drupal\Core\Messenger\MessengerInterface;

/**
 * Form submit handler for the theme settings form.
 */
function at_core_submit_extension_clearcache() {
  drupal_flush_all_caches();
  \Drupal::messenger()->addMessage(t('Cache cleared.'), 'status');
}
