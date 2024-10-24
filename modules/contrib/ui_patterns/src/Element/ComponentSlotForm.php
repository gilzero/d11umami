<?php

namespace Drupal\ui_patterns\Element;

use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Render\Element\RenderElementBase;
use Drupal\ui_patterns\SourcePluginBase;

/**
 * Component to render a single slot.
 *
 * Usage example:
 *
 * @code
 * $form['slot'] = [
 *   '#type' => 'component_slot_form',
 *   '#component_id' => 'card',
 *   '#slot_id' => 'body',
 *   '#default_value' => [
 *     'sources' => [],
 *   ],
 * ];
 * @endcode
 *
 * Value example:
 *
 * @code
 *    ['#default_value' =>
 *      ['sources' =>
 *        ['source_id' => 'id', 'value' => []]
 *      ]
 *    ]
 * @endcode
 *
 * Configuration:
 *
 * '#component_id' =>Optional Component ID.
 *    A slot can rendered without knowing any context.
 * '#slot_id' =>Optional Slot ID.
 * '#source_contexts' =>The context of the sources.
 * '#tag_filter' =>Filter sources based on these tags.
 * '#display_remove' =>Display or hide the remove button. Default = true
 * '#cardinality_multiple' =>Allow or disallow multiple slot items
 *
 * @FormElement("component_slot_form")
 */
class ComponentSlotForm extends ComponentFormBase {

  /**
   * {@inheritdoc}
   */
  public static function trustedCallbacks() {
    return array_merge(parent::trustedCallbacks(), ['postRenderSlotTable']);
  }

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
      '#display_remove' => TRUE,
      '#component_id' => NULL,
      '#slot_id' => NULL,
      '#cardinality_multiple' => TRUE,
      '#process' => [
        [$class, 'buildForm'],
        [$class, 'processPropOrSlot'],
      ],
      '#pre_render' => [
        [$class, 'preRenderPropOrSlot'],
      ],
      "#wrap" => TRUE,
      "#title_in_component" => NULL,
    ];
  }

  /**
   * Build single slot form.
   */
  public static function buildForm(array &$element, FormStateInterface $form_state): array {
    $slot_id = $element['#slot_id'];
    $trigger_element = $form_state->getTriggeringElement();
    if ($form_state->isRebuilding() && isset($trigger_element['#ui_patterns_slot'])) {
      if ($trigger_element['#ui_patterns_slot_parents'] == $element['#parents']) {
        $value = $form_state->getValue($trigger_element['#ui_patterns_slot_parents']);
        $element['#default_value'] = $value;
      }
    }

    $component = static::getComponent($element);
    if ($component !== NULL) {
      $slots = $component->metadata->slots;
      $definition = $slots[$slot_id];
    }
    else {
      /** @var \Drupal\ui_patterns\PropTypePluginManager $prop_type_manager */
      $prop_type_manager = \Drupal::service("plugin.manager.ui_patterns_prop_type");
      $definition = [
        'ui_patterns' => $prop_type_manager->createInstance('slot', []),
      ];
    }
    $wrapper_id = static::getElementId($element, 'ui-patterns-slot-' . $slot_id);
    $element['#tree'] = TRUE;
    $element['#table_title'] = $element['#title'];
    $element['#title_in_component'] = $element['#title'];
    $element['#title'] = '';
    $element['sources'] = static::buildSourcesForm($element, $form_state, $definition, $wrapper_id);
    if ($element['#cardinality_multiple'] === TRUE ||
      (!isset($element['#default_value']['sources']) || count($element['#default_value']['sources']) === 0)) {
      $element['add_more_button'] = static::buildSourceSelector($element, $definition, $wrapper_id);
    }
    $element['#prefix'] = '<div class="uip-slot" id="' . $wrapper_id . '">';
    $element['#suffix'] = '</div>';
    return $element;
  }

  /**
   * Returns the dropdown options array.
   */
  private static function getSourceOptions(array $element, array $definition):array {
    $sources = static::getSources($element['#slot_id'], $definition, $element);
    return static::sourcesToOptions($sources, FALSE);
  }

  /**
   * Removes the first occurrence of the <thead> element from an HTML string.
   *
   * @param string $html
   *   The HTML string.
   *
   * @return string
   *   The modified HTML string without the first <thead> element.
   */
  public static function removeFirstThead($html) {
    // Load the HTML into a DOMDocument object.
    $document = Html::load($html);

    // Find the first <thead> element.
    $thead = $document->getElementsByTagName('thead')->item(0);

    // If a <thead> element is found, remove it.
    if ($thead) {
      $thead->parentNode->removeChild($thead);
    }

    // Serialize the modified DOM back into a string.
    return Html::serialize($document);
  }

  /**
   * Alters the rendered form to simulate input forgery.
   *
   * It's necessary to alter the rendered form here because Mink does not
   * support manipulating the DOM tree.
   *
   * @param string $rendered_form
   *   The rendered form.
   *
   * @return string
   *   The modified rendered form.
   *
   * @see \Drupal\Tests\system\Functional\Form\FormTest::testInputForgery()
   */
  public static function postRenderSlotTable($rendered_form) {
    return static::removeFirstThead($rendered_form);
  }

  /**
   * Build single slot's sources form.
   */
  protected static function buildSourcesForm(array $element, FormStateInterface $form_state, array $definition, string $wrapper_id): array {
    $configuration = $element['#default_value'] ?? [];
    $form = [
      '#theme' => 'field_multiple_value_form',
      '#title' => $element['#table_title'] ?? '',
      '#cardinality_multiple' => $element['#cardinality_multiple'],
      '#post_render' => [
        [self::class, 'postRenderSlotTable'],
      ],
    ];
    // Add fake #field_name to avoid errors from
    // template_preprocess_field_multiple_value_form.
    $form['#field_name'] = "foo";
    if (!isset($configuration['sources'])) {
      return $form;
    }
    foreach ($configuration['sources'] as $delta => $source_configuration) {
      if (!isset($source_configuration['source_id'])) {
        continue;
      }
      $form[$delta] = static::buildSourceForm($element, $form_state, $definition, $source_configuration, $delta, $wrapper_id);
    }
    return $form;
  }

  /**
   * Build single source form.
   */
  protected static function buildSourceForm(array $element, FormStateInterface $form_state, array $definition, array $configuration, int $delta, string $wrapper_id): array {
    $slot_id = $element['#slot_id'] ?? NULL;
    $form_array_parents = $element["#array_parents"];
    $source_contexts = $element['#source_contexts'] ?? [];
    $form_array_parents[] = $slot_id ?? 'default';
    $form_array_parents[] = $delta;
    $form = [];
    $sources_manager = \Drupal::service("plugin.manager.ui_patterns_source");
    /** @var \Drupal\ui_patterns\SourceInterface $source */
    $source = $sources_manager->createInstance(
      $configuration['source_id'],
      SourcePluginBase::buildConfiguration($slot_id, $definition, $configuration, $source_contexts, $form_array_parents)
    );
    $form['source'] = $source->settingsForm([], $form_state);
    $form['source_id'] = [
      '#type' => 'hidden',
      '#value' => $source->getPluginId(),
    ];
    $form['_weight'] = [
      '#type' => 'weight',
      '#title' => t(
        'Weight for row @number',
        ['@number' => $delta + 1]
      ),
      '#title_display' => 'invisible',
      '#delta' => count($form),
      '#default_value' => $configuration['_weight'] ?? $delta,
      '#weight' => 100,
    ];
    if ($element['#display_remove'] === TRUE) {
      $form['_remove'] = static::buildRemoveSourceButton($element, $slot_id, $wrapper_id, $delta);
    }

    return $form;
  }

  /**
   * Build widget to remove source.
   */
  protected static function buildRemoveSourceButton(array $element, string $slot_id, string $wrapper_id, int $delta): array {
    $id = implode('-', $element['#array_parents']);
    $remove_action = [
      '#type' => 'submit',
      '#name' => strtr($slot_id, '-', '_') . $id . '_' . $delta . '_remove',
      '#value' => t('Remove'),
      '#submit' => [
        static::class . '::removeSource',
      ],
      '#access' => TRUE,
      '#delta' => $delta,
      '#ui_patterns_slot' => TRUE,
      '#ui_patterns_slot_parents' => $element['#parents'],
      '#ui_patterns_slot_array_parents' => $element['#array_parents'],
      '#limit_validation_errors' => [],
      '#ajax' => [
        'callback' => [static::class, 'refreshForm'],
        'wrapper' => $wrapper_id,
        'effect' => 'fade',
      ],
    ];
    return [
      '#type' => 'container',
      'dropdown_actions' => [
        static::expandComponentButton($element, $remove_action),
      ],
    ];
  }

  /**
   * Build source selector.
   */
  protected static function buildSourceSelector(array $element, array $definition, string $wrapper_id): array {
    $options = self::getSourceOptions($element, $definition);
    $slot_id = $element['#slot_id'];
    $action_buttons = [];
    foreach ($options as $option_key => $group_or_source_label) {
      $group_of_sources = is_array($group_or_source_label) ? $group_or_source_label : [$option_key => $group_or_source_label];
      foreach ($group_of_sources as $source_id => $source_label) {
        $label = ($source_id === array_key_first($options)) ? t('Add %source', ['%source' => $source_label]) : $source_label;
        $action_buttons[$source_id] = static::expandComponentButton($element, [
          '#type' => 'submit',
          '#name' => strtr($slot_id, '-', '_') . implode('-', $element['#array_parents']) . '_' . $source_id . '_add_more',
          '#value' => $label,
          '#submit' => [
            static::class . '::addSource',
          ],
          '#access' => TRUE,
          '#source_id' => $source_id,
          '#ui_patterns_slot' => TRUE,
          '#ui_patterns_slot_parents' => $element['#parents'],
          '#ui_patterns_slot_array_parents' => $element['#array_parents'],
          '#ajax' => [
            'callback' => [
              static::class,
              'refreshForm',
            ],
            'wrapper' => $wrapper_id,
            'effect' => 'fade',
          ],
        ]);
      }
    }
    return static::buildComponentDropbutton($action_buttons);
  }

  /**
   * Build drop button.
   *
   * @param array $elements
   *   Elements for drop button.
   *
   * @return array
   *   Drop button array.
   */
  protected static function buildComponentDropbutton(array $elements = []): array {
    $build = [
      '#type' => 'dropbutton',
      '#dropbutton_type' => 'small',
      '#links' => [],
    ];
    // Because we are cloning the elements into title sub element we need to
    // sort children first.
    foreach (Element::children($elements, TRUE) as $child) {
      // Clone the element as an operation.
      $build["#links"][$child] = ['title' => $elements[$child]];
      $build["#links"][$child]['title'] = RenderElementBase::preRenderAjaxForm($build["#links"][$child]['title']);
      // Flag the original element as printed so it doesn't render twice.
      $elements[$child]['#printed'] = TRUE;
    }

    return $build + $elements;
  }

  /**
   * Expand button base array into a paragraph widget action button.
   *
   * @param array $element
   *   Element.
   * @param array $button_base
   *   Button base render array.
   *
   * @return array
   *   Button render array.
   */
  protected static function expandComponentButton(array $element, array $button_base): array {
    // Do not expand elements that do not have submit handler.
    if (empty($button_base['#submit'])) {
      return $button_base;
    }

    $button = $button_base + [
      '#type' => 'submit',
      '#theme_wrappers' => ['input__submit__ui_patterns_action'],
    ];

    // Html::getId will give us '-' char in name but we want '_' for now so
    // we use strtr to search&replace '-' to '_'.
    $button['#name'] = strtr(Html::getId($button_base['#name']), '-', '_');
    $button['#id'] = static::getElementId($element, $button['#name']);

    if (isset($button['#ajax'])) {
      $button['#ajax'] += [
        'effect' => 'fade',
        // Since a normal throbber is added inline, this has the potential to
        // break a layout if the button is located in dropbuttons. Instead,
        // it's safer to just show the fullscreen progress element instead.
        'progress' => ['type' => 'fullscreen'],
      ];
    }

    return static::expandAjax($button);
  }

  /**
   * Ajax submit handler: Add source.
   *
   * @SuppressWarnings(PHPMD.UnusedFormalParameter)
   */
  public static function addSource(array $form, FormStateInterface $form_state) : void {
    $trigger_element = $form_state->getTriggeringElement();
    $source_id = $trigger_element['#source_id'];
    $component_form_parents = $trigger_element['#ui_patterns_slot_parents'];
    $configuration = $form_state->getValue($component_form_parents);
    $configuration['sources'][] = [
      'source_id' => $source_id,
      'source' => [],
    ];

    $form_state->setValue($component_form_parents, $configuration);
    $form_state->setRebuild();
  }

  /**
   * Ajax handler: Refresh sources form.
   */
  public static function refreshForm(array $form, FormStateInterface $form_state) : ?array {
    $parents = $form_state->getTriggeringElement()['#ui_patterns_slot_array_parents'];
    $form_state->setRebuild(TRUE);
    return NestedArray::getValue($form, $parents);
  }

  /**
   * Ajax submit handler: Remove source.
   *
   * @SuppressWarnings(PHPMD.UnusedFormalParameter)
   */
  public static function removeSource(array $form, FormStateInterface $form_state): void {
    $trigger_element = $form_state->getTriggeringElement();
    $delta = $trigger_element['#delta'];
    $component_form_parents = $trigger_element['#ui_patterns_slot_parents'];
    $configuration = $form_state->getValue($component_form_parents);
    unset($configuration['sources'][$delta]);
    $form_state->setValue($component_form_parents, $configuration);
    $form_state->setRebuild();
  }

}
