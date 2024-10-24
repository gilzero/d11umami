<?php

declare(strict_types=1);

namespace Drupal\ui_patterns_devel;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\ui_patterns_devel\Attribute\TwigValidatorRule;

/**
 * TwigValidatorRule plugin manager.
 */
final class TwigValidatorRulePluginManager extends DefaultPluginManager {

  /**
   * Constructs the object.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/TwigValidatorRule', $namespaces, $module_handler, TwigValidatorRuleInterface::class, TwigValidatorRule::class);
    $this->alterInfo('twig_validator_rule_info');
    $this->setCacheBackend($cache_backend, 'twig_validator_rule_plugins');
  }

}
