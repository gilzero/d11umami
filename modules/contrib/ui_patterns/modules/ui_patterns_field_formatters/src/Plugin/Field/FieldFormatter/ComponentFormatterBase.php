<?php

declare(strict_types=1);

namespace Drupal\ui_patterns_field_formatters\Plugin\Field\FieldFormatter;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\Context\Context;
use Drupal\Core\Plugin\Context\ContextDefinition;
use Drupal\Core\Plugin\Context\EntityContext;
use Drupal\ui_patterns\Form\ComponentSettingsFormBuilderTrait;
use Drupal\ui_patterns\Plugin\Context\RequirementsContext;
use Drupal\ui_patterns\SourcePluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base class for ui_patterns field formatter plugin.
 */
abstract class ComponentFormatterBase extends FormatterBase {

  use ComponentSettingsFormBuilderTrait;

  /**
   * The component plugin manager.
   *
   * @var \Drupal\ui_patterns\ComponentPluginManager
   */
  protected $componentPluginManager;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity field manager.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * The sample entity generator.
   *
   * @var \Drupal\ui_patterns\Entity\SampleEntityGenerator
   */
  protected $sampleEntityGenerator;

  /**
   * The chain context entity resolver.
   *
   * @var \Drupal\ui_patterns\Resolver\ContextEntityResolverInterface
   */
  protected $chainContextEntityResolver;

  /**
   * The provided plugin contexts.
   *
   * @var array|null
   */
  protected $context = NULL;

  /**
   * Use for settings form, to know where the form is used exactly (Optional)
   *
   * @var array<string>|null
   */
  protected $formArrayParents = NULL;

  /**
   * Set the internal property formArrayParents.
   */
  public function setFormArrayParents(array $form_array_parents) : void {
    $this->formArrayParents = $form_array_parents;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->componentPluginManager = $container->get('plugin.manager.sdc');
    $instance->entityTypeManager = $container->get('entity_type.manager');
    $instance->entityFieldManager = $container->get('entity_field.manager');

    $instance->sampleEntityGenerator = $container->get('ui_patterns.sample_entity_generator');
    $instance->chainContextEntityResolver = $container->get('ui_patterns.chain_context_entity_resolver');

    return $instance;
  }

  /**
   * Set the context.
   *
   * @param array $context
   *   Context.
   *
   * @return void
   *   Nothing.
   */
  public function setContext(array $context): void {
    $this->context = $context;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    // Get list of component by manager to insert label.
    $components = $this->componentPluginManager->getDefinitions();
    $options = [];
    $options['_empty'] = $this->t('Empty');
    foreach ($components as $component_id => $component) {
      $options[$component_id] = $component['name'];
    }
    $settings = $this->getSetting('ui_patterns');
    $summary["selected"] = $this->t('No component selected.')->render();
    if (!empty($settings['component_id'])) {

      $summary["selected"] = $this->t('Component ":component" selected.', [':component' => $options[$settings['component_id']] ?? ""])->render();
    }
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return parent::defaultSettings() + self::getComponentFormDefault();
  }

  /**
   * {@inheritdoc}
   */
  public function getComponentSettings(): array {
    if (!empty($this->configuration)) {
      return $this->configuration;
    }
    return $this->getSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $injected_contexts = $this->getComponentSourceContexts();
    // Here we need to propagate the information
    // that in the parent field_formatter hierarchy
    // The current field value has been treated in a per item manner
    // Thus, when source plugins will be fetched and displayed,
    // we properly get them especially source plugins
    // with context_requirements having field_granularity:item.
    if (is_array($this->context) && array_key_exists("context_requirements", $this->context) && $this->context["context_requirements"]->hasValue("field_granularity:item")) {
      $injected_contexts = RequirementsContext::addToContext(["field_granularity:item"], $injected_contexts);
    }
    return [
      'ui_patterns' => $this->buildComponentsForm($form_state, $injected_contexts),
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function checkEntityHasField(EntityInterface $entity, string $entity_type_id, string $field_name) : bool {
    $field_definitions = $this->entityFieldManager->getFieldDefinitions($entity->getEntityTypeId(), $entity->bundle());
    return ($entity->getEntityTypeId() === $entity_type_id &&
      array_key_exists($field_name, $field_definitions));
  }

  /**
   * Find an entity bundle which has a field.
   *
   * @param string $entity_type_id
   *   The entity type id.
   * @param string $field_name
   *   The field name to be found in searched bundle.
   *
   * @return string
   *   The bundle.
   */
  protected function findEntityBundleWithField(string $entity_type_id, string $field_name) : string {
    // @todo better implementation with service 'entity_type.bundle.info'
    $bundle = $entity_type_id;
    $bundle_entity_type = $this->entityTypeManager->getDefinition($entity_type_id)->getBundleEntityType();
    if (NULL !== $bundle_entity_type) {
      $bundle_list = $this->entityTypeManager->getStorage($bundle_entity_type)->loadMultiple();
      if (count($bundle_list) > 0) {
        foreach ($bundle_list as $bundle_entity) {
          $bundle_to_test = (string) $bundle_entity->id();
          $definitions = $this->entityFieldManager->getFieldDefinitions($entity_type_id, $bundle_to_test);
          if (array_key_exists($field_name, $definitions)) {
            $bundle = $bundle_to_test;
            break;
          }
        }
      }
    }
    return $bundle;
  }

  /**
   * Set the context of field and entity (override the method trait).
   *
   * @param ?FieldItemListInterface $items
   *   Field items when available.
   *
   * @return array
   *   Source contexts.
   */
  protected function getComponentSourceContexts(?FieldItemListInterface $items = NULL): array {
    // @todo does this really makes sense to propagate the externally injected context
    $contexts = array_merge($this->context ?? [], $this->getThirdPartySetting('ui_patterns', 'context') ?? []);
    $field_definition = $this->fieldDefinition;
    $field_name = $field_definition->getName() ?? "";
    $contexts['field_name'] = new Context(ContextDefinition::create('string'), $field_name);
    $contexts = RequirementsContext::addToContext(["field_formatter"], $contexts);
    $bundle = $field_definition->getTargetBundle();
    $contexts['bundle'] = new Context(ContextDefinition::create('string'), $bundle ?? "");
    // Get the entity.
    $entity_type_id = $field_definition->getTargetEntityTypeId();
    // When field items are available, we can get the entity directly.
    $entity = ($items) ? $items->getEntity() : NULL;
    if (!$entity) {
      $entity = $this->chainContextEntityResolver->guessEntity($contexts);
    }
    if (!$entity || !$this->checkEntityHasField($entity, $entity_type_id, $field_name)) {
      // Generate a default bundle when it is missing,
      // this covers contexts like the display of a field in a view.
      // the bundle selected should have the field in definition...
      $entity = !empty($bundle) ? $this->sampleEntityGenerator->get($entity_type_id, $bundle) :
        $this->sampleEntityGenerator->get($entity_type_id, $this->findEntityBundleWithField($entity_type_id, $field_name));

    }
    $contexts['entity'] = EntityContext::fromEntity($entity);
    return $contexts;
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() {
    $dependencies = parent::calculateDependencies();
    $component_configuration = $this->getComponentConfiguration();
    $component_id = $component_configuration['component_id'] ?? NULL;
    if (!$component_id) {
      return $dependencies;
    }
    $component_dependencies = $this->calculateComponentDependencies($component_id, $this->getComponentSourceContexts());
    SourcePluginBase::mergeConfigDependencies($dependencies, $component_dependencies);
    SourcePluginBase::mergeConfigDependencies($dependencies, ["module" => ["ui_patterns_field_formatters"]]);
    return $dependencies;
  }

}
