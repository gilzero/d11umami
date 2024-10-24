<?php

declare(strict_types=1);

namespace Drupal\ui_patterns_library\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\ui_patterns\ComponentPluginManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Component library's overview and single pages.
 *
 * @package Drupal\ui_patterns_library\Controller
 */
class LibraryController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public function __construct(protected ComponentPluginManager $componentPluginManager) {
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.sdc')
    );
  }

  /**
   * Title callback.
   *
   * @param string $provider
   *   Module or theme providing the component.
   * @param string $machineName
   *   Component machine name.
   *
   * @return string
   *   Pattern label.
   */
  public function title(string $provider, string $machineName) {
    $id = $provider . ":" . $machineName;
    $definition = $this->componentPluginManager->getDefinition($id);
    return $definition["name"];
  }

  /**
   * Render a single component page.
   *
   * @param string $provider
   *   Module or theme providing the component.
   * @param string $machineName
   *   Component machine name.
   *
   * @return array
   *   Return render array.
   */
  public function single(string $provider, string $machineName) {
    $id = $provider . ":" . $machineName;
    $definition = $this->componentPluginManager->getDefinition($id);
    return [
      '#theme' => 'ui_patterns_single_page',
      '#component' => $definition,
    ];
  }

  /**
   * Provider title callback.
   *
   * @param string $provider
   *   Module or theme providing the component.
   *
   * @return string
   *   Provider label.
   */
  public function providerTitle(string $provider) {
    // @todo the label
    return $provider;
  }

  /**
   * Render the components overview page for a specific provider.
   *
   * @param string $provider
   *   Module or theme providing the component.
   *
   * @return array
   *   Patterns overview page render array.
   */
  public function provider(string $provider) {
    $groups = [];
    $grouped_definitions = $this->componentPluginManager->getGroupedDefinitions();
    // @todo move to componentPluginManager?
    foreach ($grouped_definitions as $group_id => $definitions) {
      foreach ($definitions as $definition_id => $definition) {
        if ($definition['provider'] == $provider) {
          $groups[$group_id][$definition_id] = $definition;
        }
      }
    }
    return [
      '#theme' => 'ui_patterns_overview_page',
      '#groups' => $groups,
    ];
  }

  /**
   * Render the components overview page.
   *
   * @return array
   *   Patterns overview page render array.
   */
  public function overview() {
    $groups = $this->componentPluginManager->getGroupedDefinitions();
    return [
      '#theme' => 'ui_patterns_overview_page',
      '#groups' => $groups,
    ];
  }

}
