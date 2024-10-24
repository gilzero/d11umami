<?php

/**
 * @file
 */

use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\Tags;

/**
 * Clean away twig debugging output, it breaks some stuff.
 *
 * @param $variable
 *
 * @return mixed
 */
function _at_core_cleanup_twig_debug_output($variable) {
  $output = preg_replace('/[ \t]+/', ' ', preg_replace('/<!--(.|\s)*?-->/', '', $variable));
  return preg_replace(['/\r/', '/\n/'], '', $output);
}

/**
 * Helper function for layout plugin attributes.
 *
 * @param $variables
 *
 * @return mixed
 */
function _at_core_layout_plugin_attributes($variables) {
  $html_element = 'div';
  $classes = [];
  $role = NULL;
  $bundle = NULL;
  $entity_type = NULL;
  $view_mode = NULL;
  $id = NULL;

  // Entity and bundle.
  if (isset($variables['content']['#entity_type'])) {
    $entity_type = $variables['content']['#entity_type'];
    $bundle = $variables['content']['#bundle'] ?? NULL;
    $view_mode = $variables['content']['#view_mode'] ?? NULL;
    $id = $variables['content']['#' . $entity_type]->id();

    $entity_classes = !empty($variables['content']['#ds_configuration']['layout']['entity_classes']) ? $variables['content']['#ds_configuration']['layout']['entity_classes'] : '';
    if ($entity_classes !== 'no_classes') {
      $classes[] = $entity_type;
      $classes[] = $view_mode ? Html::cleanCssIdentifier($entity_type . '--view-mode-' . $view_mode) : '';
      $classes[] = $bundle ? Html::cleanCssIdentifier($entity_type . '--type-' . $bundle) : '';
      $classes[] = $id ? Html::cleanCssIdentifier($entity_type . '--id-' . $id) : '';
    }

    if (in_array($entity_type, ['node', 'comment'])) {
      $html_element = 'article';
      $role = 'article';
    }

    if ($entity_type == 'comment') {
      $comment = $variables['content']['#comment'];
      $classes[] = 'js-comment';
      $classes[] = $comment->getStatus() != 1 ? 'unpublished' : '';
      if ($comment->getCommentedEntityTypeId() !== 'block_content') {
        $comment_owner = $comment->getOwnerId();
        $commented_entity_owner = $comment->getCommentedEntity()->getOwnerId();
        if ($comment_owner === $commented_entity_owner) {
          $classes[] = 'by-' . $comment->getCommentedEntity()->getEntityTypeId() . '-author';
        }
      }
    }

    if ($entity_type == 'node') {
      $node = $variables['content']['#node'];
      // Add classes reflecting the current node's attributes.
      if (!empty($node->isPromoted())) {
        $classes[] = 'node--promoted';
      }
      if (!empty($node->isSticky())) {
        $classes[] = 'node--sticky';
      }
      if (empty($node->isPublished())) {
        $classes[] = 'node--unpublished';
      }
    }

    // Extension settings.
    if ($variables['theme']['extensions']['is_enabled'] === TRUE) {
      if ($variables['theme']['shortcodes']['is_enabled'] === TRUE) {
        $shortcodes_config = $variables['theme'][$variables['theme']['shortcodes']['config']];
        if (isset($variables['content'])) {
          $entity_type_bundle_setting = $entity_type . 'type_classes_' . $bundle;
          if (isset($shortcodes_config[$entity_type_bundle_setting]) && !empty($shortcodes_config[$entity_type_bundle_setting])) {
            $shortcodes = Tags::explode($shortcodes_config[$entity_type_bundle_setting]);
            foreach ($shortcodes as $class) {
              $classes[] = Html::cleanCssIdentifier($class);
            }
          }
        }
      }
    }
  }

  // Panels.
  if (isset($variables['content']['#page_variant'])) {
    $classes[] = 'panels-page';
    $classes[] = 'panels-page--' . Html::cleanCssIdentifier($variables['content']['#page_variant']->get('page'));
  }

  // Global classes.
  $layout = 'none';
  // Layout plugin contrib module, Drupal core 8.2.x and below.
  if (isset($variables['layout']) && is_array($variables['layout']) && isset($variables['layout']['id'])) {
    $layout = $variables['layout']['id'];
  }
  // Layout discovery, Drupal core 8.3.x and up.
  elseif (isset($variables['content']['#layout'])) {
    $layout = $variables['content']['#layout']->id();
  }

  $classes[] = 'atl';
  $classes[] = 'atl--' . Html::cleanCssIdentifier($layout);

  $attributes['classes'] = $classes;
  $attributes['html_element'] = $html_element;
  if (!empty($role)) {
    $attributes['role'] = $role;
  }

  return $attributes;
}
