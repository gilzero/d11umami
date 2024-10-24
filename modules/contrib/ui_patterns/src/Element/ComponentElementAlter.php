<?php

declare(strict_types=1);

namespace Drupal\ui_patterns\Element;

use Drupal\Core\Plugin\Component;
use Drupal\Core\Security\TrustedCallbackInterface;
use Drupal\Core\Theme\ComponentPluginManager;
use Drupal\ui_patterns\Plugin\UiPatterns\PropType\SlotPropType;
use Drupal\ui_patterns\PropTypeAdapterPluginManager;

/**
 * Our additions to the SDC render element.
 */
class ComponentElementAlter implements TrustedCallbackInterface {

  /**
   * Constructs a ComponentElementAlter.
   */
  public function __construct(protected ComponentPluginManager $componentPluginManager, protected PropTypeAdapterPluginManager $adaptersManager) {
  }

  /**
   * {@inheritdoc}
   */
  public static function trustedCallbacks() {
    return ['alter'];
  }

  /**
   * Alter SDC component element.
   */
  public function alter(array $element): array {
    $element = $this->normalizeSlots($element);
    $component = $this->componentPluginManager->find($element['#component']);
    $element = $this->processAttributesProp($element, $component);
    $element = $this->processAttributesRenderProperty($element);
    $element = $this->normalizeProps($element, $component);
    return $element;
  }

  /**
   * Normalize slots.
   */
  public function normalizeSlots(array $element): array {
    foreach ($element["#slots"] as $slot_id => $slot) {
      // Because SDC validator is sometimes confused by a null slot.
      if (is_null($slot)) {
        unset($element['#slots'][$slot_id]);
        continue;
      }
      // Because SDC validator is sometimes confused by an empty slot.
      if (is_array($slot) && empty($slot)) {
        unset($element['#slots'][$slot_id]);
        continue;
      }
      $element["#slots"][$slot_id] = SlotPropType::normalize($slot);
    }
    return $element;
  }

  /**
   * Normalize props.
   */
  public function normalizeProps(array $element, Component $component): array {
    $props = $component->metadata->schema['properties'] ?? [];
    foreach ($element["#props"] as $prop_id => $prop) {
      if (!isset($props[$prop_id])) {
        continue;
      }
      $definition = $props[$prop_id];
      $prop_type = $definition['ui_patterns']['type_definition'];
      // Normalizing attributes to an array is not working
      // if the prop type is defined by type=Drupal\Core\Template\Attribute
      // This should actually be done by the normalize function.
      $data = $prop_type->normalize($prop);
      if (isset($definition['ui_patterns']['prop_type_adapter'])) {
        $prop_type_adapter_id = $definition['ui_patterns']['prop_type_adapter'];
        /** @var \Drupal\ui_patterns\PropTypeAdapterInterface $prop_type_adapter */
        $prop_type_adapter = $this->adaptersManager->createInstance($prop_type_adapter_id);
        $data = $prop_type_adapter->transform($data);
      }
      $element["#props"][$prop_id] = $data;
    }
    return $element;
  }

  /**
   * Process attributes prop.
   */
  public function processAttributesProp(array $element, Component $component): array {
    $element["#props"]["attributes"] = $element["#props"]["attributes"] ?? [];
    // Attribute PHP objects are rendered as strings by SDC ComponentValidator,
    // this is raising an error: "InvalidComponentException: String value
    // found, but an object is required".
    if (is_a($element["#props"]["attributes"], '\Drupal\Core\Template\Attribute')) {
      $element["#props"]["attributes"] = $element["#props"]["attributes"]->toArray();
    }
    // Attributes prop must never be empty, to avoid the processing of SDC's
    // ComponentsTwigExtension::mergeAdditionalRenderContext() which is adding
    // an Attribute PHP object.
    $element["#props"]["attributes"]['data-component-id'] = $component->getPluginId();
    return $element;
  }

  /**
   * Process #attributes render property.
   *
   * #attributes property is an universal property of the Render API, used by
   * many Drupal mechanisms from Core and Contrib, but not processed by SDC
   * render element.
   *
   * @todo Move this to Drupal Core.
   */
  public function processAttributesRenderProperty(array $element): array {
    if (!isset($element["#attributes"])) {
      return $element;
    }
    if (is_a($element["#attributes"], '\Drupal\Core\Template\Attribute')) {
      $element["#attributes"] = $element["#attributes"]->toArray();
    }
    $element["#props"]["attributes"] = array_merge(
      $element["#attributes"],
      $element["#props"]["attributes"]
    );
    return $element;
  }

  /**
   * Process stories slots.
   *
   * Stories slots have no "#" prefix in render arrays. Let's add them.
   * A bit like UI Patterns 1.x's PatternPreview::getPreviewMarkup()
   * This method belongs here because used by both ui_patterns_library and
   * ui_patterns_legacy.
   */
  public function processStoriesSlots(array $slots): array {
    foreach ($slots as $slot_id => $slot) {
      if (!is_array($slot)) {
        continue;
      }
      if (array_is_list($slot)) {
        $slots[$slot_id] = $this->processStoriesSlots($slot);
      }
      $slot_keys = array_keys($slot);
      $render_keys = ["theme", "type", "markup", "plain_text"];
      if (count(array_intersect($slot_keys, $render_keys)) > 0) {
        foreach ($slot as $key => $value) {
          if (is_array($value)) {
            $value = $this->processStoriesSlots($value);
          }
          if (str_starts_with($key, "#")) {
            continue;
          }
          $slots[$slot_id]["#" . $key] = $value;
          unset($slots[$slot_id][$key]);
        }
      }
    }
    return $slots;
  }

}
