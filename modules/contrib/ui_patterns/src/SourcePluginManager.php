<?php

declare(strict_types=1);

namespace Drupal\ui_patterns;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\Context\ContextAwarePluginManagerInterface;
use Drupal\Core\Plugin\Context\ContextAwarePluginManagerTrait;
use Drupal\Core\Plugin\Context\ContextHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\ui_patterns\Attribute\Source;
use Drupal\ui_patterns\Plugin\Context\RequirementsContextDefinition;

/**
 * Source plugin manager.
 */
class SourcePluginManager extends DefaultPluginManager implements ContextAwarePluginManagerInterface {

  use ContextAwarePluginManagerTrait;

  /**
   * The static cache.
   *
   * @var array<string, mixed>
   */
  protected array $staticCache = [];

  /**
   * Constructs the object.
   */
  public function __construct(
    \Traversable $namespaces,
    CacheBackendInterface $cache_backend,
    ModuleHandlerInterface $module_handler,
    protected PropTypePluginManager $propTypeManager,
    protected ContextHandlerInterface $context_handler,
    protected EntityTypeManagerInterface $entityTypeManager,
  ) {
    parent::__construct(
      'Plugin/UiPatterns/Source',
      $namespaces,
      $module_handler,
      SourceInterface::class,
      Source::class
    );
    $this->alterInfo('ui_patterns_source_info');
    $this->setCacheBackend($cache_backend, 'ui_patterns_source_plugins', $this->getInvalidationCacheTags());
  }

  /**
   * {@inheritdoc}
   */
  protected function contextHandler() : ContextHandlerInterface {
    return $this->context_handler;
  }

  /**
   * Get the cache tags to invalidate.
   */
  protected function getInvalidationCacheTags() : array {
    $tags = [];
    $entity_type_definitions = $this->entityTypeManager->getDefinitions();
    foreach ($entity_type_definitions as $entity_type_definition) {
      if (!$entity_type_definition->entityClassImplements(FieldableEntityInterface::class)) {
        // Skip entity not using fields.
        continue;
      }
      $bundle_entity_type = $entity_type_definition->getBundleEntityType();
      if ($bundle_entity_type) {
        $tags[] = sprintf("config:%s_list", $bundle_entity_type);
      }
    }
    return $tags;
  }

  /**
   * Refines source plugin definition.
   *
   *  It allows for example, to add new context definitions
   *  to the plugin definition, using a static method inside sources plugins.
   *  Very useful for views for example, where each views plugin would
   *  declare a required context with a view.
   *
   * @param array<string, mixed> $definition
   *   Plugin definition to process.
   * @param string $plugin_id
   *   Plugin Id.
   */
  public function processDefinition(&$definition, $plugin_id): void {
    parent::processDefinition($definition, $plugin_id);
    if (array_key_exists("context_requirements", $definition) && count($definition["context_requirements"]) > 0) {
      $definition["context_definitions"]["context_requirements"] = RequirementsContextDefinition::fromRequirements($definition["context_requirements"]);
    }
  }

  /**
   * Returns source definitions for a prop type.
   *
   * There is also the method getNativeDefinitionsForPropType()
   * that returns only natively compatible source definitions.
   * There is also the method getConvertibleDefinitionsForPropType()
   * that returns only convertible source definitions.
   *
   * @param string $prop_type_id
   *   The prop type id.
   * @param array|null $contexts
   *   The contexts or null if not using contexts.
   * @param array<string, bool> $tag_filter
   *   Filter results by tags.
   *   The array keys are the tags, and the values are boolean.
   *   If the value is TRUE, the tag is required.
   *   If the value is FALSE, the tag is forbidden.
   *
   * @return array<string, mixed>
   *   Source definitions, keyed by source id.
   */
  public function getDefinitionsForPropType(string $prop_type_id, ?array $contexts = [], array $tag_filter = []): array {
    // No useful source plugins can be guessed
    // if the prop type is unknown. Let's return
    // no sources to hide the prop form.
    if ($prop_type_id === 'unknown') {
      return [];
    }
    $definitions = $this->getNativeDefinitionsForPropType($prop_type_id, $contexts, $tag_filter);
    foreach ($definitions as &$definition) {
      $definition["tags"][] = "prop_type_compatibility:native";
    }
    $convertibleDefinitions = $this->getConvertibleDefinitionsForPropType($prop_type_id, $contexts, $tag_filter);
    foreach ($convertibleDefinitions as &$definition) {
      $definition["tags"][] = "prop_type_compatibility:converted";
    }
    return array_merge($definitions, $convertibleDefinitions);
  }

  /**
   * Returns natively compatible source definitions for a prop type.
   *
   * There is also the method getConvertibleDefinitionsForPropType()
   * that returns convertible source definitions.
   *
   * @param string $prop_type_id
   *   The prop type id.
   * @param array|null $contexts
   *   The contexts or null if not using contexts.
   * @param array<string, bool> $tag_filter
   *   Filter results by tags.
   *   The array keys are the tags, and the values are boolean.
   *   If the value is TRUE, the tag is required.
   *   If the value is FALSE, the tag is forbidden.
   *
   * @return array<string, mixed>
   *   Source definitions, keyed by source id.
   */
  public function getNativeDefinitionsForPropType(string $prop_type_id, ?array $contexts = [], array $tag_filter = []): array {
    // Filter by context.
    $definitions = (NULL === $contexts) ? $this->getDefinitions() : $this->getDefinitionsForContextsRefined($contexts);
    // Filter by prop type.
    $definitions = $this->filterDefinitionsByPropType($definitions, $prop_type_id);
    // Filter by tags.
    if (is_array($tag_filter) && count($tag_filter) > 0) {
      $definitions = static::filterDefinitionsByTags($definitions, $tag_filter);
    }
    foreach ($definitions as &$definition) {
      $definition["tags"] = array_merge(array_key_exists("tags", $definition) ? $definition["tags"] : [], ["prop_type_matched:" . $prop_type_id]);
    }
    unset($definition);
    return $definitions;
  }

  /**
   * Filters definitions by prop type.
   *
   * @param array<string, array<string, mixed> > $definitions
   *   The definitions.
   * @param string $prop_type_id
   *   The prop type id.
   *
   * @return array
   *   The filtered definitions.
   */
  protected function filterDefinitionsByPropType(array $definitions, string $prop_type_id): array {
    return array_filter($definitions, static function ($definition) use ($prop_type_id) {
      $supported_prop_types = array_key_exists("prop_types", $definition) ? $definition['prop_types'] : [];
      return !(is_array($supported_prop_types) && (count($supported_prop_types) > 0) && !in_array($prop_type_id, $supported_prop_types));
    });
  }

  /**
   * Filters definitions by tags.
   *
   * @param array $definitions
   *   The definitions.
   * @param array<string, bool> $tag_filter
   *   Filter results by tags.
   *    The array keys are the tags, and the values are boolean.
   *    If the value is TRUE, the tag is required.
   *    If the value is FALSE, the tag is forbidden.
   *
   * @return array
   *   The filtered definitions.
   */
  protected static function filterDefinitionsByTags(array $definitions, array $tag_filter): array {
    return array_filter($definitions, static function ($definition) use ($tag_filter) {
      $tags = array_key_exists("tags", $definition) ? $definition['tags'] : [];
      if (count($tag_filter) > 0) {
        foreach ($tag_filter as $tag => $tag_required) {
          $found = in_array($tag, $tags);
          if (($tag_required && !$found) || (!$tag_required && $found)) {
            return FALSE;
          }
        }
      }
      return TRUE;
    });
  }

  /**
   * Returns convertible source definitions for a prop type.
   *
   * There is also the method getNativeDefinitionsForPropType()
   * that returns natively compatible source definitions.
   *
   * @param string $prop_type_id
   *   The prop type id.
   * @param array|null $contexts
   *   The contexts or null if not using contexts.
   * @param array<string, bool> $tag_filter
   *   Filter results by tags.
   *   The array keys are the tags, and the values are boolean.
   *   If the value is TRUE, the tag is required.
   *   If the value is FALSE, the tag is forbidden.
   *
   * @return array<string, mixed>
   *   Source definitions, keyed by source id.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getConvertibleDefinitionsForPropType(string $prop_type_id, ?array $contexts = [], array $tag_filter = []): array {
    $definitions = [];
    $convertible_sources_by_prop_id = $this->getConvertibleDefinitionsPerPropertyId($prop_type_id, $contexts, $tag_filter);
    foreach ($convertible_sources_by_prop_id as $convertible_sources) {
      foreach ($convertible_sources as $source_id => $source) {
        $definitions[$source_id] = $source;
      }
    }
    return $definitions;
  }

  /**
   * Source definitions for prop type, by convertible prop id.
   *
   * Internal usage only.
   *
   * @param string $prop_type_id
   *   The prop type id.
   * @param array|null $contexts
   *   The contexts or null if not using contexts.
   * @param array<string, bool>|null $tag_filter
   *   Filter results by tags.
   *   The array keys are the tags, and the values are boolean.
   *   If the value is TRUE, the tag is required.
   *   If the value is FALSE, the tag is forbidden.
   *
   * @return array<string, array<string, mixed>>
   *   Source definitions, keyed by convertible prop id.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function getConvertibleDefinitionsPerPropertyId(string $prop_type_id, ?array $contexts = [], ?array $tag_filter = []): array {
    $definitions = [];
    if (!is_array($tag_filter)) {
      $tag_filter = [];
    }
    $tag_filter = array_merge($tag_filter, ["widget:dismissible" => FALSE]);
    $convertible_props = $this->propTypeManager->getConvertibleProps($prop_type_id);
    foreach (array_keys($convertible_props) as $convertible_prop_id) {
      $convertible_sources = $this->getNativeDefinitionsForPropType($convertible_prop_id, $contexts, $tag_filter);
      $definitions[$convertible_prop_id] = $convertible_sources;
    }
    return $definitions;
  }

  /**
   * Returns the default source identifier for a prop type.
   *
   * @param string $prop_type_id
   *   The prop type id.
   * @param array $contexts
   *   The contexts.
   * @param array<string, bool> $tag_filter
   *   Filter results by tags.
   *   The array keys are the tags, and the values are boolean.
   *   If the value is TRUE, the tag is required.
   *   If the value is FALSE, the tag is forbidden.
   *
   * @return string|null
   *   The source plugin identifier.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getPropTypeDefault(string $prop_type_id, array $contexts = [], array $tag_filter = []): ?string {
    // First try with prop type default source.
    $prop_type_definition = $this->propTypeManager->getDefinition($prop_type_id);
    $default_source_id = $prop_type_definition["default_source"] ?? NULL;
    $default_source_applicable = $default_source_id && $this->isApplicable($default_source_id, $contexts);
    if (!$tag_filter && $default_source_applicable) {
      return $default_source_id;
    }
    $definitions = $this->getDefinitionsForPropType($prop_type_id, $contexts, $tag_filter);
    if ($tag_filter && $default_source_applicable && array_key_exists($default_source_id, $definitions)) {
      return $default_source_id;
    }
    $source_ids = array_keys($definitions);
    foreach ($source_ids as $source_id) {
      if ($this->isApplicable($source_id, $contexts)) {
        return $source_id;
      }
    }
    return NULL;
  }

  /**
   * Creates a plugin instances with the same configuration.
   *
   * @param array $plugin_ids
   *   The source plugin identifiers.
   * @param array $configuration
   *   An array of configuration.
   *
   * @return array
   *   A list of fully configured plugin instances.
   */
  public function createInstances(array $plugin_ids, array $configuration): array {
    return array_map(
      function ($plugin_id) use ($configuration) {
        return $this->createInstance($plugin_id, $configuration);
      },
      $plugin_ids,
    );
  }

  /**
   * Check if the source is matching the specified context.
   *
   * @param string $source_id
   *   The source plugin identifier.
   * @param array $contexts
   *   An array of contexts.
   *
   * @return bool
   *   Is the source applicable.
   */
  public function isApplicable(string $source_id, array $contexts): bool {
    // @todo use a method of the plugin instead?
    $definitions = $this->getDefinitionsForContextsRefined($contexts);
    return isset($definitions[$source_id]);
  }

  /**
   * Get a hash key for caching.
   *
   * @param string $key
   *   A key.
   * @param array $contexts
   *   An array of contexts.
   *
   * @return string
   *   The hash key.
   */
  private function getHashKey(string $key, array $contexts = []) : string {
    return hash("sha256", serialize([$key, $contexts]));
  }

  /**
   * Advanced method to get source definitions for contexts.
   *
   *  In addition to getDefinitionsForContexts(), this method
   *  checks context_definitions of plugins according to their keys.
   *  When required in def, a context must be present with same key,
   *  and it must satisfy the context definition.
   *
   * @param \Drupal\Core\Plugin\Context\ContextInterface[] $contexts
   *   Contexts.
   *
   * @return array<string, array<string, mixed> >
   *   Plugin definitions
   */
  public function getDefinitionsForContextsRefined(array $contexts = []) : array {
    $cacheKey = $this->getHashKey(__FUNCTION__, $contexts);
    if (isset($this->staticCache[$cacheKey])) {
      return $this->staticCache[$cacheKey];
    }

    $definitions = $this->getDefinitionsForContexts($contexts);
    $checked_context_by_keys = [];
    foreach (array_keys($contexts) as $key) {
      $checked_context_by_keys[$key] = [];
    }
    $definitions = array_filter($definitions, function ($definition) use ($contexts, &$checked_context_by_keys) {
      $context_definitions = isset($definition['context_definitions']) ? $definition['context_definitions'] ?? [] : [];
      foreach ($context_definitions as $key => $context_definition) {
        if (!$context_definition->isRequired()) {
          continue;
        }
        if (!array_key_exists($key, $contexts)) {
          return FALSE;
        }
        $context_definition_key = hash('sha256', serialize($context_definition));
        if (!isset($checked_context_by_keys[$key][$context_definition_key])) {
          $checked_context_by_keys[$key][$context_definition_key] = $context_definition->isSatisfiedBy($contexts[$key]);
        }
        if (!$checked_context_by_keys[$key][$context_definition_key]) {
          return FALSE;
        }
      }
      return TRUE;
    });
    $this->staticCache[$cacheKey] = $definitions;
    return $definitions;
  }

}
