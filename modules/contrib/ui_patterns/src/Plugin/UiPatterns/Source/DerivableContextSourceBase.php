<?php

declare(strict_types=1);

namespace Drupal\ui_patterns\Plugin\UiPatterns\Source;

use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ui_patterns\DerivableContextPluginBase;
use Drupal\ui_patterns\Plugin\UiPatterns\PropType\SlotPropType;
use Drupal\ui_patterns\SourceInterface;
use Drupal\ui_patterns\SourcePluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Ready to use base class for source plugins using DerivableContexts.
 */
abstract class DerivableContextSourceBase extends SourcePluginBase {
  /**
   * The source plugin manager.
   *
   * @var \Drupal\ui_patterns\SourcePluginManager
   */
  protected $sourcePluginManager;


  /**
   * The derivable context manager.
   *
   * @var \Drupal\ui_patterns\DerivableContextPluginManager
   */
  protected $derivableContextManager;


  /**
   * The context handler.
   *
   * @var \Drupal\Core\Plugin\Context\ContextHandlerInterface
   */
  protected $contextHandler;

  /**
   * Sources.
   *
   * @var array|null
   */
  protected ?array $derivableContexts = NULL;

  /**
   * The source plugin rendered.
   *
   * @var \Drupal\ui_patterns\SourceInterface|null
   */
  protected $sourcePlugin = NULL;

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition,
  ) {
    $plugin = parent::create(
      $container,
      $configuration,
      $plugin_id,
      $plugin_definition
    );
    $plugin->sourcePluginManager = $container->get('plugin.manager.ui_patterns_source');
    $plugin->contextHandler = $container->get('context.handler');
    $plugin->derivableContextManager = $container->get('plugin.manager.ui_patterns_derivable_context');
    return $plugin;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultSettings(): array {
    return [
      "derivable_context" => NULL,
      "source" => [],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getPropValue(): mixed {
    $source_plugin = $this->getSourcePlugin();
    return ($source_plugin) ? $source_plugin->getPropValue() : [];
  }

  /**
   * Set the source plugin according to configuration.
   */
  private function getSourcePlugin(): ?SourceInterface {
    $this->sourcePlugin = NULL;
    $derivable_context = $this->getSetting('derivable_context') ?? NULL;
    if (!$derivable_context) {
      return $this->sourcePlugin;
    }
    /** @var \Drupal\ui_patterns\DerivableContextInterface $derivable_context_plugin */
    $derivable_context_plugin = $this->derivableContextManager->createInstance($derivable_context, DerivableContextPluginBase::buildConfiguration($this->context));
    if (!$derivable_context_plugin) {
      return $this->sourcePlugin;
    }
    $derived_context = $derivable_context_plugin->getDerivedContext();
    $sources = $this->getSetting($derivable_context) ?? [];
    if (!is_array($sources) || !array_key_exists("value", $sources)) {
      return $this->sourcePlugin;
    }
    $sources = $sources["value"];

    $source_configuration = $this->isSlot() ? array_values($sources["sources"])[0] : $sources;
    $target_plugin_configuration = array_merge($source_configuration["source"] ?? [], [
      "context" => $derived_context,
    ]);
    $this->sourcePlugin = $this->createSourcePlugin($source_configuration["source_id"], $target_plugin_configuration, $derived_context);
    return $this->sourcePlugin;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $form = parent::settingsForm($form, $form_state);
    $this->buildDerivableContextSelectorForm($form, $form_state);
    return $form;
  }

  /**
   * Returns true for slots.
   */
  private function isSlot(): bool {
    $type_definition = $this->getPropDefinition()['ui_patterns']['type_definition'];
    return $type_definition instanceof SlotPropType ? TRUE : FALSE;
  }

  /**
   * Build the form to select and create a source.
   *
   * @param array $form
   *   Input form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Form state.
   *
   * @return array
   *   Returned form
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   *
   * @SuppressWarnings(PHPMD.UnusedFormalParameter)
   */
  private function buildDerivableContextSelectorForm(array &$form, FormStateInterface $form_state) : array {
    $derivableContexts = $this->listDerivableContexts();
    $wrapper_id = Html::getId(implode("_", $this->formArrayParents ?? []) . "_derivable_context_selector");
    $derivable_context = (string) ($this->getSetting('derivable_context') ?? '');
    $options_derivable_contexts = [];
    foreach ($derivableContexts as $key => $derivableContext) {
      $options_derivable_contexts[$key] = $derivableContext["label"];
    }
    asort($options_derivable_contexts);
    $form = [
      '#type' => 'container',
      "#tree" => TRUE,
    ];
    $form["derivable_context"] = [
      "#type" => "select",
      "#title" => $this->t("Context"),
      "#options" => $options_derivable_contexts,
      '#default_value' => $derivable_context,

      '#ajax' => [
        'callback' => [__CLASS__, 'onDerivableContextChange'],
        'wrapper' => $wrapper_id,
        'method' => 'replaceWith',
      ],
      '#executes_submit_callback' => FALSE,
      '#empty_value' => NULL,
      '#empty_option' => $this->t('- Select -'),
      '#required' => TRUE,
    ];
    $source_container = [
      '#type' => 'container',
      '#attributes' => ["id" => $wrapper_id],
      '#tree' => TRUE,
    ];
    if (!$derivable_context || !array_key_exists($derivable_context, $derivableContexts)) {
      $form["source"] = $source_container;
      return $form;
    }
    $source = $this->getSetting($derivable_context) ?? [];
    $source = $source["value"] ?? [];
    /** @var \Drupal\ui_patterns\DerivableContextInterface $derivable_context_plugin */
    $derivable_context_plugin = $this->derivableContextManager->createInstance($derivable_context, DerivableContextPluginBase::buildConfiguration($this->context));
    $derived_context = $derivable_context_plugin->getDerivedContext();
    $component_id = $derived_context["component_id"]->getContextValue();
    $is_slot = $this->isSlot();
    $form[$derivable_context] = $source_container;
    $form[$derivable_context]["value"] = [
      '#type' => $is_slot ? 'component_slot_form' : 'component_prop_form',
      '#default_value' => $source,
      '#component_id' => $component_id,
      '#title' => '',
      '#source_contexts' => $derived_context,
      '#cardinality_multiple' => FALSE,
      '#display_remove' => FALSE,
      "#wrap" => FALSE,
      '#' . ($is_slot ? 'slot_id' : 'prop_id') => $this->getPropId(),
      '#tree' => TRUE,
      '#tag_filter' => $this->getSourcesTagFilter(),
    ];
    return $form;
  }

  /**
   * Specifies some tags filter array for source selection.
   */
  protected function getSourcesTagFilter(): array {
    return [];
  }

  /**
   * Gets the plugin for this component.
   *
   * @param string $plugin_id
   *   The plugin ID.
   * @param array<mixed> $configuration
   *   The block configuration.
   * @param \Drupal\Core\Plugin\Context\ContextInterface[] $contexts
   *   An array of contexts to set on the plugin.
   * @param array<string> $form_array_parents
   *   Form array parents.
   *
   * @return \Drupal\ui_patterns\SourceInterface|null
   *   The plugin.
   */
  private function createSourcePlugin($plugin_id, array $configuration, array $contexts = [], array $form_array_parents = []) {
    if (!$plugin_id) {
      return NULL;
    }
    try {
      // Field formatter trick.
      $configuration["settings"] = $configuration["settings"] ?? [];
      /** @var \Drupal\ui_patterns\SourceInterface $plugin */
      $plugin = $this->sourcePluginManager->createInstance(
        $plugin_id,
        SourcePluginBase::buildConfiguration($plugin_id, $this->propDefinition, ["source" => $configuration], $contexts, $form_array_parents)
      );
      // If ($contexts && $plugin instanceof ContextAwarePluginInterface) {
      // $this->contextHandler->applyContextMapping($plugin, $contexts);
      // }.
      return $plugin;
    }
    catch (\Exception $e) {
      return NULL;
    }
  }

  /**
   * Ajax callback for fields with AJAX callback to update form substructure.
   *
   * @param array $form
   *   The form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @return array
   *   The replaced form substructure.
   */
  public static function onDerivableContextChange(array $form, FormStateInterface $form_state): array {
    $triggeringElement = $form_state->getTriggeringElement();
    // Dynamically return the dependent ajax for elements based on the
    // triggering element. This shouldn't be done statically because
    // settings forms may be different, e.g. for layout builder, core, ...
    if (!empty($triggeringElement['#array_parents'])) {
      $subformKeys = $triggeringElement['#array_parents'];
      array_pop($subformKeys);
      $subformKeys[] = $triggeringElement["#value"];
      $subform = NestedArray::getValue($form, $subformKeys);
      $form_state->setRebuild();
      return $subform;
    }
    return [];
  }

  /**
   * List source definitions.
   *
   * @return array
   *   Definitions of blocks
   */
  private function listDerivableContexts() : array {
    if ($this->derivableContexts) {
      return $this->derivableContexts;
    }
    $this->derivableContexts = $this->derivableContextManager->getDefinitionsForContexts($this->context);
    return $this->derivableContexts;
  }

  /**
   * {@inheritdoc}
   */
  public function alterComponent(array $element): array {
    $source_plugin = $this->getSourcePlugin();
    if (!$source_plugin) {
      return $element;
    }
    // @todo ?
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() : array {
    $dependencies = parent::calculateDependencies();
    $derivable_context = $this->getSetting('derivable_context') ?? NULL;
    if (!$derivable_context) {
      return $dependencies;
    }
    /** @var \Drupal\ui_patterns\DerivableContextInterface $derivable_context_plugin */
    $derivable_context_plugin = $this->derivableContextManager->createInstance($derivable_context, DerivableContextPluginBase::buildConfiguration($this->context));
    if (!$derivable_context_plugin) {
      return $dependencies;
    }
    SourcePluginBase::mergeConfigDependencies($dependencies, $this->getPluginDependencies($derivable_context_plugin));
    $source_plugin = $this->getSourcePlugin();
    if (!$source_plugin) {
      return $dependencies;
    }
    SourcePluginBase::mergeConfigDependencies($dependencies, $this->getPluginDependencies($source_plugin));
    return $dependencies;
  }

}
