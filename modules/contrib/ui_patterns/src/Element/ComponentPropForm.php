<?php

namespace Drupal\ui_patterns\Element;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\ui_patterns\SourceInterface;

/**
 * Component to render a single prop.
 *
 * Usage example:
 *
 * @code
 * $form['prop_name'] = [
 *   '#type' => 'component_prop_form',
 *   '#component_id' => 'component_id',
 *   '#prop_id' => 'prop'
 *   '#default_value' => [
 *     'source' => [],
 *     'source_id' => 'textfield'
 *   ],
 * ];
 * @endcode
 *
 * Value example:
 *
 * @code
 * '#default_value' => ['source_id' => 'id', 'source' => []]
 * @endcode
 *
 *  Configuration:
 *
 *  '#component_id' => Required Component ID.
 *  '#prop_id' => Required Prop ID.
 *  '#source_contexts' => The context of the sources.
 *  '#tag_filter' => Filter sources based on these tags.
 *
 * @FormElement("component_prop_form")
 */
class ComponentPropForm extends ComponentFormBase {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);
    return [
      '#input' => TRUE,
      '#multiple' => FALSE,
      '#default_value' => NULL,
      '#source_contexts' => [],
      '#tag_filter' => [],
      '#component_id' => NULL,
      '#slot_id' => NULL,
    // Wrapped (into details/summary) or not.
      '#wrap' => FALSE,
      '#process' => [
        [$class, 'buildForm'],
        [$class, 'processPropOrSlot'],
      ],
      '#pre_render' => [
        [$class, 'preRenderPropOrSlot'],
      ],
      '#theme_wrappers' => [],
    ];
  }

  /**
   * Build props forms.
   */
  public static function buildForm(array &$element, FormStateInterface $form_state): array {
    $element['#tree'] = TRUE;
    $prop_id = $element['#prop_id'];
    $component = static::getComponent($element);
    $definition = $component->metadata->schema['properties'][$prop_id];
    $configuration = $element['#default_value'] ?? [];
    $sources = static::getSources($prop_id, $definition, $element);
    $selected_source = static::getSelectedSource($configuration, $sources);
    if (!$selected_source) {
      // Default source is in first position.
      $selected_source = current($sources);
    }
    if (!$selected_source) {
      return [];
    }

    $wrapper_id = static::getElementId($element, 'ui-patterns-prop-item-' . $prop_id);
    $source_selector = static::buildSourceSelector($sources, $selected_source, $wrapper_id);
    $source_form = static::getSourcePluginForm($form_state, $selected_source, $wrapper_id);

    $element += [
      'source_id' => $source_selector,
      'source' => $source_form,
    ];
    $element = static::addRequired($element, $prop_id);
    if ($prop_id === "variant") {
      $element["source"]["value"]["#title"] = $element["#title"];
    }
    return $element;
  }

  /**
   * Add required visual clue to the fieldset.
   *
   * The proper required control is managed by SourcePluginBase::addRequired()
   * so the visual clue is present whether or not the control is done by the
   * source plugin. This is feature, not a bug.
   */
  protected static function addRequired(array $element, string $prop_id): array {
    $component = static::getComponent($element);
    if (!isset($component->metadata->schema["required"])) {
      return $element;
    }
    $required_props = $component->metadata->schema["required"];
    if (!in_array($prop_id, $required_props)) {
      return $element;
    }
    $element["#required"] = TRUE;
    return $element;
  }

  /**
   * Get source plugin form.
   */
  protected static function getSourcePluginForm(FormStateInterface $form_state, SourceInterface $source, string $wrapper_id): array {
    $form = $source->settingsForm([], $form_state);
    $form["#type"] = 'container';
    $form['#attributes'] = [
      'id' => $wrapper_id,
    ];
    // Weird, but :switchSourceForm() AJAX handler doesn't work without that.
    foreach (Element::children($form) as $child) {
      if (isset($form[$child]['#description']) && !isset($form[$child]['#description_display'])) {
        $form[$child]['#description_display'] = 'after';
      }
    }
    return $form;
  }

  /**
   * Build sources selector widget.
   */
  protected static function buildSourceSelector(array $sources, SourceInterface $selected_source, string $wrapper_id): array {
    if (empty($sources)) {
      return [];
    }
    if (count($sources) == 1) {
      return [
        '#type' => 'hidden',
        '#value' => array_keys($sources)[0],
      ];
    }
    $options = static::sourcesToOptions($sources);
    return [
      '#type' => 'select',
      "#options" => $options,
      '#title' => t('Source'),
      '#default_value' => $selected_source->getPluginId(),
      '#attributes' => [
        'class' => ["uip-source-selector"],
      ],
      '#prop_id' => $selected_source->getPropId(),
      '#prop_definition' => $selected_source->getPropDefinition(),
      '#ajax' => [
        'callback' => [static::class, 'switchSourceForm'],
        'wrapper' => $wrapper_id,
        'effect' => 'fade',
      ],
      '#suffix' => '<hr/>',
    ];
  }

  /**
   * Ajax handler: Switch source plugin form.
   */
  public static function switchSourceForm(array $form, FormStateInterface $form_state): array {
    $parents = $form_state->getTriggeringElement()["#array_parents"];
    $subform = NestedArray::getValue($form, array_slice($parents, 0, -1));
    return $subform["source"];
  }

  /**
   * Get selected source plugin.
   */
  protected static function getSelectedSource(array $configuration, array $sources): ?SourceInterface {
    $source_id = $configuration['source_id'] ?? NULL;
    foreach ($sources as $source) {
      if ($source->getPluginId() === $source_id) {
        return $source;
      }
    }
    return NULL;
  }

}
