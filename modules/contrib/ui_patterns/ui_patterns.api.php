<?php

/**
 * @file
 * API file.
 */

/**
 * Alter Hook for SDC Component definition.
 *
 * @param array $definitions
 *   SDC Component definitions.
 *
 * @see \Drupal\ui_patterns\ComponentPluginManager
 */
function hook_component_info_alter(array &$definitions) {
  $definitions['COMPONENT_ID']['slots']['slot_name']["title"] = 'demo';
}
