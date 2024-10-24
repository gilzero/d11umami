<?php

declare(strict_types=1);

namespace Drupal\ui_patterns\SchemaManager;

use JsonSchema\Constraints\BaseConstraint;
use JsonSchema\SchemaStorage;

/**
 * JSON Schema References resolver.
 *
 * Because SchemaStorage::resolveRefSchema() is not recursively resolving the
 * referenced schemas.
 * See: https://github.com/justinrainbow/json-schema/issues/427
 */
class ReferencesResolver {

  const MAXIMUM_RECURSIVITY_LEVEL = 10;

  /**
   * Resolve schema references recursively.
   */
  public function resolve(array $schema, int $depth = 0): array {
    if ($depth > self::MAXIMUM_RECURSIVITY_LEVEL) {
      return $schema;
    }
    $depth = $depth + 1;
    $schema = BaseConstraint::arrayToObjectRecursive($schema);
    $storage = new SchemaStorage();
    $schema = (array) $storage->resolveRefSchema($schema);
    foreach ($schema as $key => $value) {
      if (!is_object($value)) {
        continue;
      }
      $schema[$key] = $this->resolve((array) $value, $depth);
    }
    return $schema;
  }

}
