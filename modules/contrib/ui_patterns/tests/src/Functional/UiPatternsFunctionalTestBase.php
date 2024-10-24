<?php

declare(strict_types=1);

namespace Drupal\Tests\ui_patterns\Functional;

use Drupal\Component\Serialization\Yaml;
use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\ui_patterns\Traits\ConfigImporterTrait;
use Drupal\Tests\ui_patterns\Traits\TestContentCreationTrait;
use Drupal\Tests\ui_patterns\Traits\TestDataTrait;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Base function testing.
 *
 * @group ui_patterns
 */
abstract class UiPatternsFunctionalTestBase extends BrowserTestBase {

  use TestContentCreationTrait;
  use TestDataTrait;
  use ConfigImporterTrait;

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'ui_patterns_test_theme';

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'node',
    'ui_patterns',
    'ui_patterns_layouts',
    'field_ui',
  ];

  /**
   * The user.
   *
   * @var \Drupal\user\Entity\User|false
   */
  protected mixed $user = FALSE;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->user = $this->drupalCreateUser([
      'administer node display',
    ], NULL, TRUE);
    if ($this->user) {
      $this->drupalLogin($this->user);
    }
    else {
      throw new AccessDeniedHttpException($this->getTextContent());
    }
  }

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
    $this->configInitialize = FALSE;
    $this->initializeConfig();
    \Drupal::service('config.storage.sync')->write($config_id, $config);
    $config_importer = $this->configImporter();
    $config_importer->import();
  }

  /**
   * Build UI Patterns compatible configuration for given test_set.
   *
   * @param array $test_set
   *   The test_set to build the configuration from.
   *
   * @return array
   *   The builded configuration.
   */
  protected function buildUiPatternsConfig(array $test_set):array {
    if (!isset($test_set['component']['slots'])) {
      $test_set['component']['slots'] = [];
    }
    return $test_set['component'];
  }

  /**
   * Validates rendered component.
   *
   * @param array $test_set
   *   The test set to validate against.
   *
   * @throws \Behat\Mink\Exception\ElementNotFoundException
   */
  protected function validateRenderedComponent($test_set) {
    $output = $test_set['output'] ?? [];
    $page = $this->getSession()->getPage();

    foreach ($output as $prop_or_slot => $prop_or_slot_item) {
      foreach ($prop_or_slot_item as $prop_name => $output) {
        $expected_outputs_here = ($prop_or_slot === "props") ? [$output] : $output;
        foreach ($expected_outputs_here as $index => $expected_output) {
          $type = $prop_or_slot;
          $selector = '.ui-patterns-' . $type . '-' . $prop_name;
          $element = $page->find('css', $selector);
          $message = sprintf("Test '%s' failed for prop/slot '%s' of component %s. Selector %s. Output is %s", $test_set["name"] ?? "", $prop_or_slot, $test_set['component']['component_id'], $selector, $page->getContent());
          $this->assertNotNull(
            $element,
            $message
          );
          $prop_value = $element->getHtml();
          // Replace "same" by normalized_value.
          if (isset($expected_output["same"])) {
            if (!is_array($expected_output["same"]) && !isset($expected_output["normalized_value"])) {
              $expected_output["normalized_value"] = "" . $expected_output["same"];
            }
            unset($expected_output["same"]);
          }
          if (count($expected_output) > 0) {
            $this->assertExpectedOutput($expected_output, $prop_value, $message);
          }
        }
      }
    }
  }

}
