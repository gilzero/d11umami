<?php

declare(strict_types=1);

namespace Drupal\Tests\ui_patterns\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\ui_patterns\ComponentPluginManager;

/**
 * Test the ComponentPluginManager service.
 *
 * @coversDefaultClass \Drupal\ui_patterns\ComponentPluginManager
 *
 * @group ui_patterns
 */
final class ComponentPluginManagerTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['ui_patterns', 'ui_patterns_test'];

  /**
   * Themes to install.
   *
   * @var string[]
   */
  protected static $themes = [];

  /**
   * The component plugin manager from ui_patterns.
   *
   * @var \Drupal\ui_patterns\ComponentPluginManager
   */
  protected ComponentPluginManager $manager;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->manager = \Drupal::service('plugin.manager.sdc');
  }

  /**
   * @covers ::alterDefinition
   */
  public function testHookComponentInfoAlter() : void {
    $definition = $this->manager->getDefinition('ui_patterns_test:alert');
    $this->assertEquals('Hook altered', $definition['variants']['hook']['title']);
  }

  /**
   * @covers ::getCategories
   */
  public function testGetCategories() : void {
    $categories = $this->manager->getCategories();
    $this->assertNotEmpty($categories);
  }

  /**
   * @covers ::getSortedDefinitions
   */
  public function testGetSortedDefinitions(): void {
    $sortedDefinitions = $this->manager->getSortedDefinitions();
    $this->assertNotEmpty($sortedDefinitions);
  }

  /**
   * @covers ::getGroupedDefinitions
   */
  public function testGetGroupedDefinitions(): void {
    $groupedDefinitions = $this->manager->getGroupedDefinitions();
    $this->assertNotEmpty($groupedDefinitions);
  }

}
