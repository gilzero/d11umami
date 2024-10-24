<?php

namespace Drupal\ui_patterns\Element;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\Context\Context;
use Drupal\Core\Plugin\Context\ContextDefinition;
use Drupal\Core\Render\RenderContext;

/**
 * Provides a Component form builder element.
 *
 * Usage example:
 *
 * @code
 * $form['component_form'] = [
 *   '#type' => 'component_form',
 *   '#default_value' => [
 *   ],
 * ];
 * @endcode
 *
 * Value example:
 *
 * @code
 *   ['#default_value' => [
 *     'component_id' => 'my_module:my_component',
 *     'variant_id' => 'variant',
 *     'slots' => [
 *       'slots_id' => [
 *         ['source_id' => 'id', 'value' => 'Source value']
 *       ],
 *     ],
 *     'props' => [
 *       ['props_id' =>
 *        ['source_id' => 'id', 'value' => 'Source value ']
 *       ]
 *     ],
 *   ]
 *  ]
 * @endcode
 *
 * Additional Configuration:
 *
 * '#component_id' => Optional Component Id.
 *   If unset a component selector is set.
 * '#source_contexts' => The context of the sources.
 * '#tag_filter' => Filter sources based on this tags.
 *
 * @FormElement("component_form")
 */
class ComponentForm extends ComponentFormBase {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);
    return [
      // The component id.
      '#component_id' => NULL,
      '#input' => TRUE,
      '#multiple' => FALSE,
      '#default_value' => NULL,
      '#source_contexts' => [],
      '#tag_filter' => [],
      '#process' => [
        [$class, 'buildForm'],
      ],
      '#element_validate' => [
        [$class, 'validateFormElement'],
      ],
      '#theme_wrappers' => ['form_element'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function valueCallback(&$element, $input, FormStateInterface $form_state) {
    if ($input) {
      $value = [
        'component_id' => $input['component_id'] ?? NULL,
        'variant_id' => $input['variant_id'] ?? NULL,
        'props' => $input['props'] ?? [],
        'slots' => $input['slots'] ?? [],
      ];
      $element['#default_value'] = $value;
      return $value;
    }
    else {
      return [
        'component_id' => NULL,
        'variant_id' => NULL,
        'props' => [],
        'slots' => [],
      ];
    }
  }

  /**
   * Processes the main form element including component selector.
   *
   * @SuppressWarnings(PHPMD.UnusedFormalParameter)
   */
  public static function buildForm(array &$element, FormStateInterface $form_state) : array {

    $initial_component_id = $element['#component_id'] ?? NULL;
    $component_id = $initial_component_id ?? $element['#default_value']['component_id'] ?? NULL;
    $wrapper_id = static::getElementId($element, 'ui-patterns-component');
    if ($component_id) {
      $contextComponentDefinition = ContextDefinition::create('string');
      $element['#source_contexts']['component_id'] = new Context($contextComponentDefinition, $component_id);
    }
    if ($initial_component_id === NULL) {
      $component_selector_form = array_merge(self::buildComponentSelectorForm(
        $wrapper_id,
        $component_id
      ), ["#ajax_url" => $element["#ajax_url"] ?? NULL]);
      $element["component_id"] = self::expandAjax($component_selector_form);
    }
    self::buildComponentForm(
      $element,
      $wrapper_id,
      $component_id
    );
    $element['#tree'] = TRUE;
    return $element;
  }

  /**
   * Processes the selected component from element.
   *
   * @param array $element
   *   The element form.
   * @param string $wrapper_id
   *   The wrapper id.
   * @param string|null $component_id
   *   The component id.
   */
  private static function buildComponentForm(
    array &$element,
    string $wrapper_id,
    ?string $component_id,
  ) : void {
    $element['#prefix'] = '<div id="' . $wrapper_id . '">';
    $element['#suffix'] = '</div>';
    if (!$component_id) {
      return;
    }
    $component = static::getComponent($element);
    if (isset($component->metadata->schema['properties']['variant'])) {
      $element['variant_id'] = self::buildComponentVariantSelectorForm(
        $component_id,
        $element['#default_value']['variant_id'] ?? NULL,
      );
    }
    $element['slots'] = self::buildSlotsForm($element, $component_id);
    $element['props'] = self::buildPropsForm($element, $component_id);
  }

  /**
   * Build components selector widget.
   *
   * @return array
   *   The component select.
   */
  private static function buildComponentSelectorForm(
    ?string $wrapper_id,
    ?string $selected_component_id,
  ): array {
    $definitions = \Drupal::service("plugin.manager.sdc")->getGroupedDefinitions();
    $options = [];
    foreach ($definitions as $group_id => $group) {
      foreach ($group as $component_id => $definition) {
        $options[$group_id][$component_id] = $definition['annotated_name'];
      }
    }
    return [
      "#type" => "select",
      "#title" => t("Component"),
      "#options" => $options,
      '#default_value' => $selected_component_id,
      '#ajax' => [
        'callback' => [static::class, 'changeSelectorFormChangeAjax'],
        'wrapper' => $wrapper_id,
        'effect' => 'fade',
      ],
      '#executes_submit_callback' => FALSE,
      '#empty_value' => '',
      '#empty_option' => t('- None -'),
      '#required' => TRUE,
    ];
  }

  /**
   * Build the variant select widget.
   *
   * @return array
   *   The variant select.
   */
  private static function buildComponentVariantSelectorForm(
    string $component_id,
    array|NULL $default_variant_id,
  ): array {

    return [
      "#type" => "component_prop_form",
      "#title" => t("Variant"),
      "#component_id" => $component_id,
      "#prop_id" => 'variant',
      '#default_value' => $default_variant_id,
    ];
  }

  /**
   * Build slots form.
   */
  private static function buildSlotsForm(array $element, string $component_id): array {
    return [
      '#title' => 'Slots',
      '#type' => 'component_slots_form',
      '#component_id' => $component_id,
      '#source_contexts' => $element['#source_contexts'],
      '#tag_filter' => $element['#tag_filter'],
      '#ajax_url' => $element['#ajax_url'] ?? NULL,
      '#access' => $element['#render_slots'] ?? TRUE,
      '#default_value' => [
        'slots' => $element['#default_value']['slots'],
      ],
    ];
  }

  /**
   * Build props form.
   */
  private static function buildPropsForm(array $element, string $component_id): array {
    return [
      '#title' => 'Props',
      '#type' => 'component_props_form',
      '#component_id' => $component_id,
      '#source_contexts' => $element['#source_contexts'],
      '#tag_filter' => $element['#tag_filter'],
      '#ajax_url' => $element['#ajax_url'] ?? NULL,
      '#access' => $element['#render_props'] ?? TRUE,
      '#default_value' => [
        'props' => $element['#default_value']['props'],
      ],
    ];
  }

  /**
   * Ajax callback for component selector change.
   */
  public static function changeSelectorFormChangeAjax(
    array $form,
    FormStateInterface $form_state,
  ) : array {
    $parents = $form_state->getTriggeringElement()['#array_parents'];
    $sub_form = NestedArray::getValue($form, array_slice($parents, 0, -1));
    $form_state->setRebuild();
    return $sub_form;
  }

  /**
   * Open wrapped elements with errors.
   *
   * @param array $element
   *   The element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @return bool
   *   return TRUE if errors were found.
   */
  protected static function openWrappedElementsWithErrors(array &$element, FormStateInterface $form_state) : bool {
    $errors = $form_state->getErrors();
    if (count($errors) === 0) {
      return FALSE;
    }
    $element_name = implode("][", $element["#parents"]);
    $error_elements_found = FALSE;
    foreach (array_keys($errors) as $error_name) {
      if (!str_starts_with($error_name, $element_name)) {
        continue;
      }
      $error_elements_found = TRUE;
      $parents = array_slice(explode("][", $error_name), count($element["#parents"]));
      if (count($parents) < 2) {
        continue;
      }
      $parents_of_prop_or_slot = array_slice($parents, 0, 2);
      $prop_or_slot = NestedArray::getValue($element, $parents_of_prop_or_slot);
      if (!empty($prop_or_slot) && isset($prop_or_slot["#wrap"]) && $prop_or_slot["#wrap"]) {
        $parents_of_prop_or_slot[] = $parents_of_prop_or_slot[1];
        $parents_of_prop_or_slot[] = "#open";
        NestedArray::setValue($element, $parents_of_prop_or_slot, TRUE);
      }
    }
    return $error_elements_found;
  }

  /**
   * Form element validation handler.
   */
  public static function validateFormElement(array &$element, FormStateInterface $form_state) : void {
    if (static::openWrappedElementsWithErrors($element, $form_state)) {
      return;
    }
    if (isset($element["#component_validation"]) && !$element["#component_validation"]) {
      $form_state->setValueForElement($element, $element['#value']);
      return;
    }
    try {
      $trigger_element = $form_state->getTriggeringElement();
      if (isset($trigger_element['#ui_patterns']) === FALSE) {
        $build = [
          '#type' => 'component',
          '#component' => $element['#value']['component_id'] ?? $element['#component_id'],
          '#ui_patterns' => $element['#value'],
          '#source_contexts' => $element['#source_contexts'] ?? [],
        ];
        $context = new RenderContext();
        $renderer = \Drupal::service("renderer");
        $renderer->executeInRenderContext($context, function () use (&$build, $renderer) {
          return $renderer->render($build);
        });
        $form_state->setValueForElement($element, $element['#value']);
      }
    }
    catch (\Throwable $e) {
      // If a component_id is we just show the error message instead
      // of highlighting the whole form.
      if (!empty($element['#component_id'])) {
        $form_state->setErrorByName('', $e->getMessage());
      }
      else {
        $form_state->setError($element['component_id'], $e->getMessage());
      }
    }
  }

}
