<?php

declare(strict_types=1);

namespace Drupal\ui_patterns\Plugin\UiPatterns\Source;

use Drupal\Component\Plugin\Definition\PluginDefinitionInterface;
use Drupal\Component\Render\MarkupInterface;
use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Block\BlockManagerInterface;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Entity\EntityDisplayBase;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\SubformState;
use Drupal\Core\Plugin\Context\ContextHandlerInterface;
use Drupal\Core\Plugin\Context\ContextRepositoryInterface;
use Drupal\Core\Plugin\ContextAwarePluginInterface;
use Drupal\Core\Plugin\PluginFormFactoryInterface;
use Drupal\Core\Plugin\PluginWithFormsInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ui_patterns\Attribute\Source;
use Drupal\ui_patterns\Entity\SampleEntityGeneratorInterface;
use Drupal\ui_patterns\PropTypePluginManager;
use Drupal\ui_patterns\SourcePluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the source.
 */
#[Source(
  id: 'block',
  label: new TranslatableMarkup('Block'),
  description: new TranslatableMarkup('A block plugin from a whitelist.'),
  prop_types: ['slot']
)]
class BlockSource extends SourcePluginBase {
  /**
   * Block to be rendered.
   *
   * @var \Drupal\Core\Block\BlockPluginInterface|null
   */
  protected $block = NULL;

  /**
   * {@inheritdoc}
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    PropTypePluginManager $propTypeManager,
    ContextRepositoryInterface $contextRepository,
    RouteMatchInterface $routeMatch,
    SampleEntityGeneratorInterface $sampleEntityGenerator,
    ModuleHandlerInterface $moduleHandler,
    protected BlockManagerInterface $blockManager,
    protected PluginFormFactoryInterface $pluginFormFactory,
    protected ContextHandlerInterface $contextHandler,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $propTypeManager, $contextRepository, $routeMatch, $sampleEntityGenerator, $moduleHandler);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition,
  ) {
    $instance = new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plugin.manager.ui_patterns_prop_type'),
      $container->get('context.repository'),
      $container->get('current_route_match'),
      $container->get('ui_patterns.sample_entity_generator'),
      $container->get('module_handler'),
      $container->get('plugin.manager.block'),
      $container->get('plugin_form.factory'),
      $container->get('context.handler')
    );
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultSettings(): array {
    return [
      'plugin_id' => NULL,
      'settings' => [],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getPropValue(): mixed {
    // Create a block entity.
    $this->block = $this->getBlock($this->getSetting('plugin_id') ?? '');
    if (!$this->block) {
      return [];
    }
    return $this->block->build();
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    $block = $this->getBlock($form_state->getValue('plugin_id'));
    if ($block) {
      $subform_state = SubformState::createForSubform($form['settings'], $form, $form_state);
      $this->getPluginForm($block)->validateConfigurationForm($form['settings'], $subform_state);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $form = parent::settingsForm($form, $form_state);
    $this->buildBlockCreateForm($form, $form_state);
    return $form;
  }

  /**
   * Build the form to create a block.
   */
  protected function buildBlockCreateForm(array &$form, FormStateInterface $form_state) : void {
    $definitions = $this->listBlockDefinitions();
    $options = $this->getBlockOptions($definitions);
    $wrapper_id = Html::getId(implode("_", $this->formArrayParents ?? []) . "_block-create-form-ajax");
    $plugin_id = $this->getSetting('plugin_id') ?? '';
    $form["plugin_id"] = [
      "#type" => "select",
      "#title" => $this->t("Block"),
      "#options" => $options,
      '#default_value' => $plugin_id,

      '#ajax' => [
        'callback' => [__CLASS__, 'onBlockPluginIdChange'],
        'wrapper' => $wrapper_id,
        'method' => 'replaceWith',
      // 'callback' => [static::class, 'onBlockPluginIdChange'],
      ],
      '#executes_submit_callback' => FALSE,
      '#empty_value' => '',
      '#empty_option' => $this->t('- None -'),
      '#required' => FALSE,
    ];
    $form["settings"] = [
      '#type' => 'container',
      '#attributes' => ["id" => $wrapper_id],
      "#tree" => TRUE,
    ];
    $block = $this->getBlock($plugin_id);
    if ($block) {
      // Create a block entity.
      $form['#tree'] = TRUE;
      // $form['#process'] = [ '::validateForm'];
      $form['settings'] = [];
      $subform_state = SubformState::createForSubform($form['settings'], $form, $form_state);
      $form['settings'] = $this->getPluginForm($block)->buildConfigurationForm($form['settings'], $subform_state);
      $form["settings"]['#tree'] = TRUE;
      $form['settings']['#prefix'] = '<div id="' . $wrapper_id . '">' . ($form['settings']['#prefix'] ?? '');
      $form['settings']['#suffix'] = ($form['settings']['#suffix'] ?? '') . '</div>';
    }
  }

  /**
   * Retrieves the plugin form for a given block.
   *
   * @param \Drupal\Core\Block\BlockPluginInterface $block
   *   The block plugin.
   *
   * @return \Drupal\Core\Plugin\PluginFormInterface
   *   The plugin form for the block.
   */
  protected function getPluginForm(BlockPluginInterface $block) {
    if ($block instanceof PluginWithFormsInterface) {
      return $this->pluginFormFactory->createInstance($block, 'configure');
    }
    return $block;
  }

  /**
   * Gets the plugin for this component.
   *
   * @param string|null $plugin_id
   *   The plugin ID.
   *
   * @return \Drupal\Core\Block\BlockPluginInterface|null
   *   The plugin.
   */
  public function getBlock(?string $plugin_id) {
    if (!$plugin_id) {
      return NULL;
    }
    $block_configuration = $this->getSetting('settings') ?? [];
    $contexts = $this->context;
    /** @var \Drupal\Core\Block\BlockPluginInterface $plugin */
    $plugin = $this->blockManager->createInstance($plugin_id, $block_configuration);
    if ($plugin instanceof ContextAwarePluginInterface) {
      // Propagate the contexts known by this source to the block instance.
      $plugin_contexts = $plugin->getContexts();
      $plugin_definition = $plugin->getPluginDefinition();
      $provider = ($plugin_definition instanceof PluginDefinitionInterface) ? $plugin_definition->getProvider() : ($plugin_definition['provider'] ?? '');
      if ($provider === "layout_builder") {
        if (array_key_exists("view_mode", $plugin_contexts)) {
          $plugin->setContextValue("view_mode", EntityDisplayBase::CUSTOM_MODE);
        }
      }
      foreach ($contexts as $context_name => $context) {
        if (!array_key_exists($context_name, $plugin_contexts)) {
          $plugin->setContext($context_name, $context);
        }
        else {
          $plugin->setContextValue($context_name, $context->getContextValue());
        }
      }
      $this->contextHandler->applyContextMapping($plugin, $contexts);
    }
    // Custom patch for LB FieldBlock blocks.
    return $plugin;
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
  public static function onBlockPluginIdChange(array $form, FormStateInterface $form_state): array {
    $triggeringElement = $form_state->getTriggeringElement();
    // Dynamically return the dependent ajax for elements based on the
    // triggering element. This shouldn't be done statically because
    // settings forms may be different, e.g. for layout builder, core, ...
    if (!empty($triggeringElement['#array_parents'])) {
      $subformKeys = $triggeringElement['#array_parents'];
      // Remove the triggering element itself and add the 'block' below key.
      array_pop($subformKeys);
      $subformKeys[] = 'settings';
      // Return the subform:
      $subform = NestedArray::getValue($form, $subformKeys);
      $form_state->setRebuild();
      return $subform;
    }
    return [];
  }

  /**
   * Return blocks list.
   *
   * @see BlockLibraryController::listBlocks()
   * @see FilteredPluginManagerTrait::getFilteredDefinitions()
   * @see layout_builder_plugin_filter_block__block_ui_alter()
   * @see layout_builder_plugin_filter_block__layout_builder_alter()
   * @see layout_builder_plugin_filter_block_alter()
   *
   * @return array
   *   Definitions of blocks
   *
   * @SuppressWarnings(PHPMD.UnusedFormalParameter)
   */
  protected function listBlockDefinitions() : array {
    $context_for_block_discovery = $this->context;
    $definitions = $this->blockManager->getFilteredDefinitions('ui_patterns', $context_for_block_discovery, []);
    // Filter plugins based on the flag 'ui_patterns_compatibility'.
    // @see function ui_patterns_plugin_filter_block__ui_patterns_alter
    // from ui_patterns.module file
    $definitions = array_filter($definitions, function ($definition, $plugin_id) {
      return is_array($definition) && (!isset($definition['_ui_patterns_compatible']) || $definition['_ui_patterns_compatible']);
    }, ARRAY_FILTER_USE_BOTH);
    // Filter based on contexts.
    $definitions = $this->contextHandler->filterPluginDefinitionsByContexts($context_for_block_discovery, $definitions);
    // Order by category, and then by admin label.
    $definitions = $this->blockManager->getSortedDefinitions($definitions);
    return $definitions;
  }

  /**
   * Get options for block select.
   */
  protected function getBlockOptions(array $definitions) : array {
    $definition_groups = [];
    foreach ($definitions as $plugin_id => $plugin_definition) {
      $category = $plugin_definition['category'] ?? 'Other';
      if ($category instanceof MarkupInterface) {
        /** @var \Drupal\Component\Render\MarkupInterface $category */
        $category = $category->__toString();
      }
      if (!array_key_exists($category, $definition_groups)) {
        $definition_groups[$category] = [];
      }
      $definition_groups[$category][$plugin_id] = $plugin_definition;
    }
    $options = [];
    foreach ($definition_groups as $definition_group_id => $definition_group) {
      $group_options = [];
      foreach ($definition_group as $definition_id => $definition) {
        $group_options[$definition_id] = $definition['admin_label'];
      }
      $options[$definition_group_id] = $group_options;
    }
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function alterComponent(array $element): array {
    if (!$this->block) {
      return $element;
    }

    // CacheableMetadata::createFromRenderArray($content)
    $cache = $element["#cache"] ?? [];
    $element["#cache"] = array_merge($cache, [
      "max-age" => 0,
     // "tags" => ['config:system.menu.' . $this->menuId],
    ]);
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() : array {
    $dependencies = parent::calculateDependencies();
    if (!$this->block) {
      $this->block = $this->getBlock($this->getSetting('plugin_id') ?? '');
    }
    if (!$this->block) {
      return $dependencies;
    }
    SourcePluginBase::mergeConfigDependencies($dependencies, $this->getPluginDependencies($this->block));
    return $dependencies;
  }

}
