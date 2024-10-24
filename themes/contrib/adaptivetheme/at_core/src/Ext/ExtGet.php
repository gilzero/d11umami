<?php

namespace Drupal\at_core\Ext;

use Drupal\node\Entity\NodeType;
use Drupal\comment\Entity\CommentType;
use Drupal\block_content\Entity\BlockContentType;
use Drupal\paragraphs\Entity\ParagraphsType;
use Drupal\image\Entity\ImageStyle;

/**
 *
 */
class ExtGet {

  /**
   * @param $theme
   * @return \Drupal\Core\Entity\EntityInterface[]|null
   */
  public function getActiveThemeBlocks($theme) {
    $theme_blocks = NULL;

    if (\Drupal::moduleHandler()->moduleExists('block') === TRUE) {
      $theme_blocks = \Drupal::entityTypeManager()->getStorage('block')->loadByProperties(['theme' => $theme]);
    }

    return $theme_blocks;
  }

  /**
   * @return array
   */
  public function getBreakPoints() {
    $breakpoints = [];

    if (\Drupal::moduleHandler()->moduleExists('breakpoint') === TRUE) {
      $breakpoints['breakpoint_groups'] = \Drupal::service('breakpoint.manager')->getGroups();

      foreach ($breakpoints['breakpoint_groups'] as $group_key => $group_values) {
        $breakpoints['breakpoints'][$group_key] = \Drupal::service('breakpoint.manager')->getBreakpointsByGroup($group_key);
      }

      foreach ($breakpoints['breakpoints'] as $group => $breakpoint_values) {
        if ($breakpoint_values !== []) {
          $breakpoints['breakpoint_options'][$group] = $group;
        }
      }
    }
    else {
      \Drupal::messenger()->addMessage(t('This theme requires the <b>Breakpoint module</b> to be installed. Go to the <a href="@extendpage" target="_blank">Modules</a> page and install Breakpoint. You cannot set the layout or use this themes custom settings until Breakpoint is installed.', ['@extendpage' => base_path() . 'admin/modules']), 'error');
    }

    return $breakpoints;
  }

  /**
   * @return array
   */
  public function getEntityTypes() {
    $entity_types = [];

    // Get node types.
    if (\Drupal::moduleHandler()->moduleExists('node') === TRUE) {
      $entity_types['node'] = NodeType::loadMultiple();
    }

    // Get comment types.
    if (\Drupal::moduleHandler()->moduleExists('comment') === TRUE) {
      $entity_types['comment'] = CommentType::loadMultiple();
    }

    // Get block types.
    if (\Drupal::moduleHandler()->moduleExists('block_content') === TRUE) {
      $entity_types['block_content'] = BlockContentType::loadMultiple();
    }

    // Get paragraph types.
    if (\Drupal::moduleHandler()->moduleExists('paragraphs') === TRUE) {
      $entity_types['paragraphs'] = ParagraphsType::loadMultiple();
    }

    return $entity_types;
  }

  /**
   * @return array
   */
  public function getViewModes() {
    $view_modes = [];

    if (\Drupal::moduleHandler()->moduleExists('node') === TRUE) {
      $view_modes['node'] = \Drupal::service('entity_display.repository')->getViewModes('node');
      // Unset unwanted view modes.
      unset($view_modes['node']['rss']);
      unset($view_modes['node']['search_index']);
      unset($view_modes['node']['search_result']);
    }

    // Get comment view modes.
    if (\Drupal::moduleHandler()->moduleExists('comment') === TRUE) {
      $view_modes['comment'] = \Drupal::service('entity_display.repository')->getViewModes('comment');
    }

    // Get block view modes.
    if (\Drupal::moduleHandler()->moduleExists('block_content') === TRUE) {
      $view_modes['block_content'] = \Drupal::service('entity_display.repository')->getViewModes('block_content');
    }

    // Get paragraph view modes.
    if (\Drupal::moduleHandler()->moduleExists('paragraphs') === TRUE) {
      $view_modes['paragraphs'] = \Drupal::service('entity_display.repository')->getViewModes('paragraph');
    }

    return $view_modes;
  }

  /**
   * @return array|\Drupal\Core\Entity\EntityInterface[]|static[]
   */
  public function getImageStyles() {
    $image_styles = [];

    if (\Drupal::moduleHandler()->moduleExists('image') === TRUE) {
      $image_styles = ImageStyle::loadMultiple();
    }

    return $image_styles;
  }

  /**
   * @param $file_path
   * @return array
   */
  public function getSassVariables($file_path) {
    $sass_variables = [];

    if (file_exists($file_path)) {
      $lines = file($file_path);

      foreach ($lines as $i => $line) {
        // Check the line is a variable.
        if (substr($line, 0, 1) === '$') {
          $line_arr = explode(":", $line);
          $key = trim(str_replace('$', '', $line_arr[0]));
          $value = trim(str_replace(';', '', $line_arr[1]));
          $sass_variables[$key] = $value;
        }
      }
    }

    return $sass_variables;
  }

}
