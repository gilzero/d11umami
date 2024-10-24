<?php

namespace Drupal\ui_patterns\Element;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Form\FormStateInterface;

/**
 * Component to render slots for a component.
 *
 * Usage example:
 *
 * @code
 * $form['slots'] = [
 *   '#type' => 'component_slots_form',
 *   '#component_id' => 'id'
 *   '#default_value' => [
 *     'slots' => [],
 *   ],
 * ];
 * @endcode
 *
 * Value example:
 *
 * @code
 *   ['#default_value' =>
 *     'slots' => [
 *       'slots_id' => [
 *         ['sources' =>
 *           ['source_id' => 'id', 'value' => []]
 *         ]
 *       ],
 *     ],
 *   ]
 * @endcode
 *
 * @FormElement("component_slots_form")
 */
class ComponentSlotsForm extends ComponentFormBase {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);
    return [
      '#input' => TRUE,
      '#multiple' => FALSE,
      '#default_value' => NULL,
      '#component_id' => NULL,
      '#source_contexts' => [],
      '#tag_filter' => [],
      '#process' => [
        [$class, 'buildForm'],
      ],
    ];
  }

  /**
   * Processes slots form element.
   *
   * @SuppressWarnings(PHPMD.UnusedFormalParameter)
   */
  public static function buildForm(array &$element, FormStateInterface $form_state): array {

    $component = static::getComponent($element);
    if (!isset($component->metadata->slots) || count(
        $component->metadata->slots
      ) === 0) {
      hide($element);
      return $element;
    }
    $contexts = $element['#source_contexts'] ?? [];
    $configuration = $element['#default_value']['slots'] ?? [];
    $slot_heading = new FormattableMarkup("<p><strong>@title</strong></p>", ["@title" => t("Slots")]);
    $element[] = [
      '#markup' => $slot_heading,
    ];
    foreach ($component->metadata->slots as $slot_id => $slot) {
      $element[$slot_id] = [
        '#title' => $slot['title'] ?? '',
        '#type' => 'component_slot_form',
        '#default_value' => $configuration[$slot_id] ?? [],
        '#component_id' => $component->getPluginId(),
        '#slot_id' => $slot_id,
        '#source_contexts' => $contexts,
        '#tag_filter' => $element['#tag_filter'],
        '#prefix' => "<div class='component-form-slot'>",
        '#suffix' => "</div>",
      ];
    }
    return $element;
  }

}
