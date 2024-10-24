<?php

namespace Drupal\ui_patterns\Element;

use Drupal\Component\Utility\Html;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\Component;
use Drupal\Core\Render\Element;
use Drupal\Core\Render\Element\FormElementBase;
use Drupal\Core\Security\TrustedCallbackInterface;
use Drupal\ui_patterns\SourcePluginBase;

/**
 * Base class for components forms.
 */
abstract class ComponentFormBase extends FormElementBase implements TrustedCallbackInterface {

  /**
   * {@inheritdoc}
   */
  public static function trustedCallbacks() {
    return ['preRenderPropOrSlot', 'processPropOrSlot'];
  }

  /**
   * Check if the form element needs a details.
   *
   * @param array $element
   *   The form element.
   *
   * @return string|null
   *   Prop or slot id if the form element needs a details.
   */
  protected static function checkDetailsElement(array &$element) : ?string {
    if (!isset($element["#wrap"]) || !$element["#wrap"]) {
      return NULL;
    }
    $prop_or_slot_id = $element["#prop_id"] ?? $element["#slot_id"];
    $title_in_component = $element["#title_in_component"] ?? $prop_or_slot_id;
    $title = !empty($element['#title']) ? $element['#title'] : $title_in_component;
    if (!array_key_exists($prop_or_slot_id, $element)) {
      $element[$prop_or_slot_id] = [
        "#type" => "details",
        "#title" => $title,
        "#open" => FALSE,
      ];
    }
    return $prop_or_slot_id;
  }

  /**
   * Customize slot or prop form elements (pre-render).
   *
   * @param array $element
   *   Element to process.
   *
   * @return array
   *   Processed element
   */
  public static function preRenderPropOrSlot(array $element) : array {
    if ($prop_or_slot_id = static::checkDetailsElement($element)) {
      $children_keys = Element::children($element);
      foreach ($children_keys as $child_key) {
        if ($child_key === $prop_or_slot_id) {
          continue;
        }
        $element[$prop_or_slot_id][] = $element[$child_key];
        $element[$child_key]["#printed"] = TRUE;
      }
    }
    return $element;
  }

  /**
   * Customize slot or prop form elements (process).
   *
   * @param array $element
   *   Element to process.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @return array
   *   The processed element.
   */
  public static function processPropOrSlot(array &$element, FormStateInterface $form_state) {
    $triggering_element = $form_state->getTriggeringElement();
    if ($prop_or_slot_id = static::checkDetailsElement($element)) {
      if (is_array($triggering_element) && isset($triggering_element["#array_parents"]) && is_array($triggering_element["#array_parents"])) {
        $element_array_parents = $element["#array_parents"];
        $trigger_array_parents = $triggering_element["#array_parents"];
        $start_of_trigger_parents = array_slice($trigger_array_parents, 0, count($element_array_parents));
        if ($start_of_trigger_parents === $element_array_parents) {
          $element[$prop_or_slot_id]["#open"] = TRUE;
        }
      }
    }
    return $element;
  }

  /**
   * Get a unique element id based on the parents and a parameter.
   */
  protected static function getElementId(array $element, string $base_id): string {
    $parents = (array_key_exists("#array_parents", $element) && is_array($element["#array_parents"])) ?
      $element["#array_parents"] : [];
    $returned = (count($parents) > 0) ?
      Html::getId(implode("_", $parents) . "_" . $base_id)
      : Html::getId($base_id);
    return $returned;
  }

  /**
   * Expand each ajax element with ajax urls.
   *
   * @param array $element
   *   The ajax element.
   *
   * @return array
   *   The extended ajax form.
   */
  protected static function expandAjax(array $element): array {
    $url = $element['#ajax_url'] ?? NULL;
    if (isset($element['#ajax']) && $url) {
      $element['#ajax']['url'] = $url;
    }
    return $element;
  }

  /**
   * Helper function to return the component.
   */
  protected static function getComponent(array $element): Component | NULL {
    $component_id = $element['#default_value']['component_id'] ?? $element['#component_id'] ?? NULL;
    /** @var \Drupal\Core\Theme\ComponentPluginManager $component_plugin_manager */
    $component_plugin_manager = \Drupal::service("plugin.manager.sdc");
    return $component_id ? $component_plugin_manager->find($component_id) : NULL;
  }

  /**
   * Get sources for a prop or slot, ordered.
   *
   * @param string $prop_or_slot_id
   *   The prop or slot ID.
   * @param array $definition
   *   The prop or slot definition.
   * @param array $element
   *   The form element.
   *
   * @return array<string, \Drupal\ui_patterns\SourceInterface>
   *   The sources, ordered.
   */
  protected static function getSources(string $prop_or_slot_id, array $definition, array $element): array {
    $configuration = $element['#default_value'] ?? [];
    $source_contexts = $element['#source_contexts'];
    $form_array_parents = $element['#array_parents'];
    $tag_filter = $element['#tag_filter'];
    /** @var \Drupal\ui_patterns\PropTypeInterface $prop_type */
    $prop_type = empty($definition) ? NULL : $definition['ui_patterns']['type_definition'];
    /** @var \Drupal\ui_patterns\SourcePluginManager $source_plugin_manager */
    $source_plugin_manager = \Drupal::service("plugin.manager.ui_patterns_source");
    $prop_plugin_definition = $prop_type->getPluginDefinition();
    $default_source_id = (is_array($prop_plugin_definition) && isset($prop_plugin_definition["default_source"])) ? $prop_plugin_definition["default_source"] : NULL;
    $sources = $source_plugin_manager->getDefinitionsForPropType($prop_type->getPluginId(), $source_contexts, $tag_filter);
    $source_ids = array_keys($sources);
    $source_ids = array_combine($source_ids, $source_ids);
    if (empty($source_ids)) {
      return [];
    }
    $valid_sources = $source_plugin_manager->createInstances($source_ids, SourcePluginBase::buildConfiguration($prop_or_slot_id, $definition, $configuration, $source_contexts, $form_array_parents));
    foreach ($valid_sources as &$source) {
      /** @var \Drupal\ui_patterns\SourcePluginBase $source  */
      $source_id = $source->getPluginId();
      $source->setConfiguration(array_merge($source->getConfiguration(), [
        "selection" => [
          "default" => ($source_id === $default_source_id),
          "tags" => $sources[$source_id]["tags"] ?? [],
        ],
      ]));
    }
    unset($source);
    return static::orderSources($valid_sources, $default_source_id);
  }

  /**
   * Order sources according to a strategy.
   *
   * @param array<string, \Drupal\ui_patterns\SourceInterface> $sources
   *   The sources to order.
   * @param string $default_source_id
   *   The default source id.
   *
   * @return array<string, \Drupal\ui_patterns\SourceInterface>
   *   The ordered sources.
   */
  protected static function orderSources(array $sources, string $default_source_id) : array {
    $returned = [];
    if ($default_source_id !== NULL && isset($sources[$default_source_id])) {
      $returned[$default_source_id] = $sources[$default_source_id];
    }
    $native_sources = array_filter($sources, function ($source) use ($default_source_id) {
      /** @var \Drupal\ui_patterns\SourcePluginBase $source */
      return ($source->getPluginId() !== $default_source_id) && in_array("prop_type_compatibility:native", $source->getConfiguration()["selection"]["tags"] ?? []);
    });
    $converted_sources = array_filter($sources, function ($source) use ($default_source_id) {
      /** @var \Drupal\ui_patterns\SourcePluginBase $source */
      return ($source->getPluginId() !== $default_source_id) && !in_array("prop_type_compatibility:native", $source->getConfiguration()["selection"]["tags"] ?? []);
    });
    uasort($native_sources, function ($a, $b) {
      return strcasecmp($a->label(), $b->label());
    });
    uasort($converted_sources, function ($a, $b) {
      return strcasecmp($a->label(), $b->label());
    });
    foreach ($native_sources as $source) {
      $returned[$source->getPluginId()] = $source;
    }
    foreach ($converted_sources as $source) {
      $returned[$source->getPluginId()] = $source;
    }
    return $returned;
  }

  /**
   * Get selected source plugin.
   */
  protected static function sourcesToOptions(array $sources, bool $use_group = TRUE): array {
    $options = [];
    // @todo better organize sources in groups.
    foreach ($sources as $valid_source_plugin) {
      $plugin_configuration = $valid_source_plugin->getConfiguration();
      if ($use_group && isset($plugin_configuration['selection']) && isset($plugin_configuration['selection']["tags"]) && in_array("prop_type_compatibility:converted", $plugin_configuration['selection']["tags"])) {
        if (!isset($options["Converted"])) {
          $options["Converted"] = [];
        }
        $options["Converted"][$valid_source_plugin->getPluginId()] = $valid_source_plugin->label();
        continue;
      }
      $options[$valid_source_plugin->getPluginId()] = $valid_source_plugin->label();
    }
    return $options;
  }

}
