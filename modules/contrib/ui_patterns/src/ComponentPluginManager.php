<?php

declare(strict_types=1);

namespace Drupal\ui_patterns;

use Drupal\Component\Plugin\CategorizingPluginManagerInterface;
use Drupal\Component\Plugin\Definition\PluginDefinitionInterface;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Extension\ThemeHandlerInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Plugin\Component;
use Drupal\Core\Theme\Component\ComponentValidator;
use Drupal\Core\Theme\Component\SchemaCompatibilityChecker;
use Drupal\Core\Theme\ComponentNegotiator;
use Drupal\Core\Theme\ComponentPluginManager as SdcPluginManager;
use Drupal\Core\Theme\ThemeManagerInterface;
use Drupal\ui_patterns\SchemaManager\ReferencesResolver;

/**
 * UI Patterns extension of SDC component plugin manager.
 */
class ComponentPluginManager extends SdcPluginManager implements CategorizingPluginManagerInterface {

  /**
   * Cache key prefix to use in the cache backend.
   */
  const CACHE_KEY = 'ui_patterns';

  /**
   * {@inheritdoc}
   */
  public function __construct(
    ModuleHandlerInterface $module_handler,
    ThemeHandlerInterface $themeHandler,
    CacheBackendInterface $cacheBackend,
    ConfigFactoryInterface $configFactory,
    ThemeManagerInterface $themeManager,
    ComponentNegotiator $componentNegotiator,
    FileSystemInterface $fileSystem,
    SchemaCompatibilityChecker $compatibilityChecker,
    ComponentValidator $componentValidator,
    string $appRoot,
    protected PropTypePluginManager $propTypePluginManager,
    protected PropTypeAdapterPluginManager $prop_type_adapterPluginManager,
    protected ReferencesResolver $referencesSolver,
    ModuleExtensionList $moduleExtensionList,
  ) {
    parent::__construct(
      $module_handler,
      $themeHandler,
      $cacheBackend,
      $configFactory,
      $themeManager,
      $componentNegotiator,
      $fileSystem,
      $compatibilityChecker,
      $componentValidator,
      $appRoot
    );
    $this->moduleExtensionList = $moduleExtensionList;
    $this->alterInfo('component_info');
    $this->setCacheBackend($cacheBackend, self::CACHE_KEY);
  }

  /**
   * {@inheritdoc}
   */
  protected function alterDefinition(array $definition): array {
    // Overriding SDC alterDefinition method.
    $definition = parent::alterDefinition($definition);
    // Adding custom logic.
    $fallback_prop_type_id = $this->propTypePluginManager->getFallbackPluginId("");
    $definition = $this->alterLinks($definition);
    $definition = $this->alterSlots($definition);
    $definition = $this->annotateSlots($definition);
    $definition = $this->annotateProps($definition, $fallback_prop_type_id);
    return $definition;
  }

  /**
   * Alter links.
   */
  protected function alterLinks(array $definition): array {
    if (!isset($definition['links'])) {
      return $definition;
    }
    // Resolve the short notation.
    foreach ($definition['links'] as $delta => $link) {
      if (is_array($link)) {
        continue;
      }
      $definition['links'][$delta] = [
        "url" => (string) $link,
      ];
    }
    return $definition;
  }

  /**
   * Alter slots.
   */
  protected function alterSlots(array $definition): array {
    if (!isset($definition['slots'])) {
      return $definition;
    }
    // Prevent slots without title from breaking.
    foreach ($definition['slots'] as $slot_id => $slot) {
      $definition['slots'][$slot_id]["title"] = $slot["title"] ?? $slot_id;
    }
    return $definition;
  }

  /**
   * Annotate each slot in a component definition.
   */
  protected function annotateSlots(array $definition): array {
    if (empty($definition['slots'])) {
      return $definition;
    }
    $slot_prop_type = $this->propTypePluginManager->createInstance('slot', []);
    foreach ($definition['slots'] as $slot_id => $slot) {
      $slot['ui_patterns']['type_definition'] = $slot_prop_type;
      $definition['slots'][$slot_id] = $slot;
    }
    return $definition;
  }

  /**
   * Annotate each prop in a component definition.
   *
   * This is the main purpose of overriding SDC component plugin manager.
   * We add a 'ui_patterns' object in each prop schema of the definition.
   */
  protected function annotateProps(array $definition, string $fallback_prop_type_id): array {
    // In JSON schema, 'required' is out of the prop definition.
    if (isset($definition['props']['required'])) {
      foreach ($definition['props']['required'] as $prop_id) {
        $definition['props']['properties'][$prop_id]['ui_patterns']['required'] = TRUE;
      }
    }
    if (isset($definition["variants"])) {
      $definition['props']['properties']['variant'] = $this->buildVariantProp($definition);
    }
    $definition['props']['properties'] = $this->addAttributesProp($definition);
    foreach ($definition['props']['properties'] as $prop_id => $prop) {
      $definition['props']['properties'][$prop_id] = $this->annotateProp($prop_id, $prop, $fallback_prop_type_id);
    }
    return $definition;
  }

  /**
   * Annotate a single prop.
   */
  protected function annotateProp(string $prop_id, array $prop, string $fallback_prop_type_id): array {
    $prop["title"] = $prop["title"] ?? $prop_id;
    $prop_type = $this->propTypePluginManager->guessFromSchema($prop);
    if ($prop_type->getPluginId() === $fallback_prop_type_id) {
      // Sometimes, a prop JSON schema is different enough to not be caught by
      // the compatibility checker, but close enough to address the same
      // sources as an existing prop type with only some small unidirectional
      // transformation of the data. So, we need an adapter plugin.
      $prop_type_adapter = $this->prop_type_adapterPluginManager->guessFromSchema($prop);
      if ($prop_type_adapter) {
        $prop_type_id = $prop_type_adapter->getPropTypeId();
        $prop_type = $this->propTypePluginManager->createInstance($prop_type_id);
        $prop['ui_patterns']['prop_type_adapter'] = $prop_type_adapter->getPluginId();
      }
    }

    if (isset($prop['$ref']) && str_starts_with($prop['$ref'], "ui-patterns://")) {
      // Resolve prop schema here, because:
      // - Drupal\Core\Theme\Component\ComponentValidator::getClassProps() is
      //   executed before schema references are resolved, so SDC believe
      //   a reference is a PHP namespace.
      // - It is not possible to propose a patch to SDC because
      //   SchemaStorage::resolveRefSchema() is not recursively resolving
      //   the schemas anyway.
      $prop = $this->referencesSolver->resolve($prop);
    }
    $prop['ui_patterns']['type_definition'] = $prop_type;
    $prop['ui_patterns']["summary"] = ($prop_type instanceof PropTypeInterface) ? $prop_type->getSummary($prop) : "";
    return $prop;
  }

  /**
   * Add attributes prop.
   *
   * 'attribute' is one of the 2 'magic' props: its name and type are already
   * set. Always available because automatically added by
   * ComponentsTwigExtension::mergeAdditionalRenderContext().
   */
  private function addAttributesProp(array $definition): array {
    // Let's put it at the beginning (for forms).
    return array_merge(
     [
       'attributes' => [
         'title' => 'Attributes',
         '$ref' => "ui-patterns://attributes",
       ],
     ],
      $definition['props']['properties'] ?? [],
    );
  }

  /**
   * Build variant prop.
   *
   * 'variant' is one of the 2 'magic' props: its name and type are already set.
   * Available if at least a variant is set in the component definition.
   */
  private function buildVariantProp(array $definition): array {
    $enums = [];
    $meta_enums = [];
    foreach ($definition["variants"] as $variant_id => $variant) {
      $enums[] = $variant_id;
      $meta_enums[$variant_id] = $variant['title'] ?? $variant_id;
    }
    return [
      'title' => 'Variant',
      '$ref' => "ui-patterns://variant",
      'enum' => $enums,
      'meta:enum' => $meta_enums,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCategories() {
    // Fetch all categories from definitions and remove duplicates.
    $categories = array_unique(array_values(array_map(function ($definition) {
      return $definition['group'] ?? t("Other");
    }, $this->getDefinitions())));
    natcasesort($categories);
    return array_values($categories);
  }

  /**
   * {@inheritdoc}
   */
  public function getSortedDefinitions(?array $definitions = NULL) {
    // Sort the plugins first by group, then by label.
    $definitions = $definitions ?? $this->getDefinitions();
    $label_key = 'name';
    uasort($definitions, function ($a, $b) use ($label_key) {
      $a_group = isset($a['group']) ? (string) $a['group'] : '';
      $b_group = isset($b['group']) ? (string) $b['group'] : '';
      if ($a_group !== $b_group) {
        return strnatcasecmp($a_group, $b_group);
      }
      $a_label = preg_replace("/[^A-Za-z0-9 ]/", '', $a[$label_key]);
      $b_label = preg_replace("/[^A-Za-z0-9 ]/", '', $b[$label_key]);
      return strnatcasecmp($a_label, $b_label);
    });
    return $definitions;
  }

  /**
   * {@inheritdoc}
   */
  public function getGroupedDefinitions(?array $definitions = NULL): array {
    $definitions = $definitions ?: $this->getSortedDefinitions();
    $groups = [];
    foreach ($definitions as $id => $definition) {
      $group = $definition["group"] ?? "Other";
      $groups[$group][$id] = $definition;
    }
    return $groups;
  }

  /**
   * {@inheritdoc}
   */
  protected function findDefinitions() {
    $definitions = parent::findDefinitions();
    // Add annotated_name property to distinct components with the same name.
    $labels = array_column($definitions, "name");
    $duplicate_labels = array_unique(array_intersect($labels, array_unique(array_diff_key($labels, array_unique($labels)))));
    foreach ($definitions as $id => $definition) {
      $definitions[$id]["annotated_name"] = $this->getAnnotatedLabel($definition, $duplicate_labels);
    }
    return $definitions;
  }

  /**
   * Add annotation to label when many components share the same name.
   */
  protected function getAnnotatedLabel(array $definition, array $duplicate_labels): string {
    $label = $definition['name'] ?? $definition['machineName'];
    if (!in_array($label, $duplicate_labels)) {
      return $label;
    }
    if (!isset($definition['provider'])) {
      return $label;
    }
    return $label . " (" . $this->getExtensionLabel($definition['provider']) . ")";
  }

  /**
   * Get the extension (module or theme) label.
   */
  protected function getExtensionLabel(string $extension): string {
    if ($this->moduleHandler->moduleExists($extension)) {
      return $this->moduleExtensionList->getName($extension);
    }
    if ($this->themeHandler->themeExists($extension)) {
      return $this->themeHandler->getTheme($extension)->info['name'];
    }
    return $extension;
  }

  /**
   * Calculate dependencies of a component.
   *
   * @param \Drupal\Core\Plugin\Component $component
   *   The component.
   *
   * @return array
   *   Config Dependencies.
   */
  public function calculateDependencies(Component $component) : array {
    $definition = $component->getPluginDefinition();
    $provider = ($definition instanceof PluginDefinitionInterface) ? $definition->getProvider() : (string) ($definition["provider"] ?? '');
    $extension_type = $this->getExtensionType($provider);
    return (empty($provider) || empty($extension_type)) ? [] : [$extension_type => [$provider]];
  }

  /**
   * Get extension type (theme or module).
   */
  protected function getExtensionType(string $extension): string {
    if ($this->moduleHandler->moduleExists($extension)) {
      return 'module';
    }
    if ($this->themeHandler->themeExists($extension)) {
      return 'theme';
    }
    return '';
  }

}
