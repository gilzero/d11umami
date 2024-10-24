<?php

declare(strict_types=1);

namespace Drupal\Tests\ui_patterns\Traits;

use Drupal\Component\Serialization\Yaml;

/**
 * Entity test data trait.
 */
trait ConfigImporterTrait {

  /**
   * Config is initialized.
   */
  private bool $configInitialize = FALSE;

  /**
   * Initialize config.
   */
  private function initializeConfig() {
    if ($this->configInitialize === FALSE) {
      $this->copyConfig(\Drupal::service('config.storage'), \Drupal::service('config.storage.sync'));
    }
    $this->configInitialize = TRUE;
  }

  /**
   * Load a configure fixture.
   *
   * @param string $path
   *   The path to the fixture.
   *
   * @return array
   *   The fixture.
   */
  public function loadConfigFixture(string $path):array {
    $yaml = file_get_contents($path);
    if ($yaml === FALSE) {
      throw new \InvalidArgumentException($path . ' not found.');
    }
    return Yaml::decode($yaml);
  }

  /**
   * Import config fixture.
   *
   * @param string $config_id
   *   The config id.
   * @param array $config
   *   The config fixture.
   */
  public function importConfigFixture(string $config_id, array $config) {
    $this->initializeConfig();
    \Drupal::service('config.storage.sync')->write($config_id, $config);
    $config_importer = $this->configImporter();
    $config_importer->import();
  }

}
