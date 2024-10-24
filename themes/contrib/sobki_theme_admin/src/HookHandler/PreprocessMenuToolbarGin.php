<?php

declare(strict_types=1);

namespace Drupal\sobki_theme_admin\HookHandler;

use Drupal\Core\DependencyInjection\ClassResolverInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\gin\GinSettings;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Allow to use theme's default logo in Gin's toolbar.
 */
class PreprocessMenuToolbarGin implements ContainerInjectionInterface {

  public function __construct(
    protected ClassResolverInterface $classResolver,
    protected string $appRoot,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('class_resolver'),
      $container->getParameter('app.root'),
    );
  }

  /**
   * Allow to use theme's default logo in Gin's toolbar.
   *
   * @param array $variables
   *   The preprocessed variables.
   */
  public function preprocess(array &$variables): void {
    /** @var \Drupal\gin\GinSettings $settings */
    $settings = $this->classResolver->getInstanceFromDefinition(GinSettings::class);

    $logo_url = $settings->getDefault('logo.url');
    if (\is_file($this->appRoot . $logo_url)) {
      $variables['icon_path'] = $logo_url;
      // Force icon_default to FALSE due to logic in Gin Toolbar template.
      $variables['icon_default'] = FALSE;
    }
  }

}
