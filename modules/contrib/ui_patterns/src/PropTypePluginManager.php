<?php

namespace Drupal\ui_patterns;

use Drupal\Component\Plugin\FallbackPluginManagerInterface;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\ui_patterns\SchemaManager\CompatibilityChecker;

/**
 * PropType plugin manager.
 */
class PropTypePluginManager extends DefaultPluginManager implements FallbackPluginManagerInterface, SchemaGuesserInterface {

  /**
   * Convertibility graph.
   *
   * @var array<string, array<string>>|null
   */
  protected ?array $convertibility = NULL;

  /**
   * Constructs PropTypePluginManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   * @param \Drupal\ui_patterns\SchemaManager\CompatibilityChecker $compatibilityChecker
   *   The compatibility checker.
   */
  public function __construct(
    \Traversable $namespaces,
    CacheBackendInterface $cache_backend,
    ModuleHandlerInterface $module_handler,
    protected CompatibilityChecker $compatibilityChecker,
  ) {
    parent::__construct(
      'Plugin/UiPatterns/PropType',
      $namespaces,
      $module_handler,
      'Drupal\ui_patterns\PropTypeInterface',
      'Drupal\ui_patterns\Attribute\PropType'
    );
    $this->alterInfo('prop_type_info');
    $this->setCacheBackend($cache_backend, 'ui_patterns_prop_type_plugins');
  }

  /**
   * {@inheritdoc}
   */
  public function guessFromSchema(array $prop_schema): ?PropTypeInterface {
    $definition = $this->getDefinitionFromSchema($prop_schema);
    if ($definition !== NULL) {
      /** @var \Drupal\ui_patterns\PropTypeInterface */
      return $this->createInstance($definition['id'], []);
    }
    /** @var \Drupal\ui_patterns\PropTypeInterface */
    return $this->createInstance('unknown', []);
  }

  /**
   * Returns plugin definitions sorted by priority.
   */
  protected function getSortedDefinitions(): array {
    $definitions = $this->getDefinitions();
    usort($definitions, function ($a, $b) {
      return ($b['priority'] ?? 1) - ($a['priority'] ?? 1);
    });
    return $definitions;
  }

  /**
   * Returns a prop type definition from a JSON schema.
   */
  protected function getDefinitionFromSchema(array $prop_schema): ?array {
    if (isset($prop_schema['$ref']) && str_starts_with($prop_schema['$ref'], "ui-patterns://")) {
      $prop_type_id = str_replace("ui-patterns://", "", $prop_schema['$ref']);
      return $this->getDefinition($prop_type_id);
    }
    $definitions = $this->getSortedDefinitions();
    foreach ($definitions as $definition) {
      if ($this->compatibilityChecker->isCompatible($prop_schema, $definition['schema'])) {
        return $definition;
      }
    }
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getFallbackPluginId($plugin_id, array $configuration = []) {
    return 'unknown';
  }

  /**
   * Get all prop types given a typed data.
   *
   * @param string $data_type
   *   Typed data plugin ID.
   *
   * @return array
   *   A list of prop type IDs.
   */
  public function getAllPropTypeByTypedData(string $data_type): array {
    $definitions = $this->getDefinitions();
    $prop_types = [];
    foreach ($definitions as $prop_type_id => $definition) {
      if (empty($definition['typed_data'])) {
        continue;
      }
      foreach ($definition['typed_data'] as $item) {
        if ($data_type === $item) {
          $prop_types[] = $prop_type_id;
        }
      }
    }
    return $prop_types;
  }

  /**
   * Get reachable identifiers with their convert paths.
   *
   * @param string $propId
   *   Identifier to start from.
   *
   * @return array<string, array<string>>
   *   Reachable identifiers with paths.
   */
  public function getConvertibleProps(string $propId) : array {
    $convertibility = $this->getPropConvertibilityGraph();
    $reachable = [];
    $visited = [];
    // Stack stores pairs of (current identifier, path to current identifier)
    $stack = [[$propId, [$propId]]];

    while (!empty($stack)) {
      [$current, $path] = array_pop($stack);

      if (isset($visited[$current])) {
        continue;
      }

      $visited[$current] = TRUE;
      $reachable[$current] = $path;

      if (isset($convertibility[$current])) {
        foreach ($convertibility[$current] as $neighbor) {
          if (!isset($visited[$neighbor])) {
            $stack[] = [$neighbor, array_merge($path, [$neighbor])];
          }
        }
      }
    }
    if (array_key_exists($propId, $reachable)) {
      unset($reachable[$propId]);
    }
    return $reachable;
  }

  /**
   * Get prop convertibility graph (with caching).
   *
   * @return array<string, array<string>>|null
   *   Prop convertibility graph.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getPropConvertibilityGraph() {
    if (isset($this->convertibility) && is_array($this->convertibility)) {
      return $this->convertibility;
    }
    if (($cache = ($this->cacheGet($this->getCachedKeyPropConvertibilityGraph()))) && isset($cache->data)) {
      $this->convertibility = $cache->data;
      return $this->convertibility;
    }
    // Build convertibility graph.
    $this->convertibility = [];
    $prop_type_definitions = $this->getDefinitions();
    foreach ($prop_type_definitions as $prop_id => $prop_type_definition) {
      $this->convertibility[$prop_id] = $this->getDirectlyConvertibleProps($prop_id, $prop_type_definition);
    }
    // ---
    $this->cacheSet($this->getCachedKeyPropConvertibilityGraph(), $this->convertibility, Cache::PERMANENT, $this->cacheTags);
    return $this->convertibility;
  }

  /**
   * {@inheritdoc}
   */
  public function clearCachedDefinitions(): void {
    parent::clearCachedDefinitions();
    if ($this->cacheBackend && !$this->cacheTags) {
      $this->cacheBackend->delete($this->getCachedKeyPropConvertibilityGraph());
    }
    $this->convertibility = NULL;
  }

  /**
   * Get cached prop convertibility graph.
   *
   * @return string
   *   Cache key name.
   */
  protected function getCachedKeyPropConvertibilityGraph() : string {
    return $this->cacheKey . ':prop_convertibility_graph';
  }

  /**
   * Get directly convertible prop identifiers.
   *
   * @param string $propId
   *   Prop identifier.
   * @param array<string, mixed>|null $prop_type_definition
   *   Prop type definition.
   *
   * @return array<string>
   *   Convertible prop identifiers.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function getDirectlyConvertibleProps(string $propId, ?array $prop_type_definition = NULL) : array {
    if (!$prop_type_definition) {
      $prop_type_definition = $this->getDefinition($propId);
    }
    if (!isset($prop_type_definition['convert_from']) || !is_array($prop_type_definition['convert_from'])) {
      return [];
    }
    return $prop_type_definition['convert_from'];
  }

}
