<?php

/**
 * @file
 * Contains real_estate.page.inc.
 *
 * Page callback for Real estate entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Real estate templates.
 *
 * Default template: real_estate.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_real_estate(array &$variables) {
  // Fetch RealEstate Entity Object.
  $real_estate = $variables['elements']['#real_estate'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
