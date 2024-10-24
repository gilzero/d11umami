<?php

declare(strict_types=1);

namespace Drupal\ui_patterns_devel;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Override the ComponentValidator service.
 */
class UiPatternsDevelServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container): void {
    $components = [
      'Drupal\Core\Template\ComponentsTwigExtension',
      'plugin.manager.sdc',
    ];

    foreach ($components as $serviceId) {
      if (!$container->hasDefinition($serviceId)) {
        continue;
      }

      $definition = $container->getDefinition($serviceId);
      $arguments = $definition->getArguments();

      foreach ($arguments as $index => $argument) {
        if ('Drupal\Core\Theme\Component\ComponentValidator' !== (string) $argument) {
          continue;
        }

        $definition->replaceArgument($index, new Reference('Drupal\ui_patterns_devel\Component\ComponentValidatorSilencer'));
      }
    }

    // Override Twig environment for Drupal to catch errors.
    $definition = $container->getDefinition('twig');
    $definition->setClass('Drupal\ui_patterns_devel\Template\TwigEnvironmentOverride');
  }

}
