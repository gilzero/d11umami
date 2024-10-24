<?php

declare(strict_types=1);

namespace Drupal\ui_patterns_devel\Template;

use Drupal\Core\Template\TwigEnvironment;
use Twig\Error\SyntaxError;
use Twig\Source;

/**
 * A class that override a Twig environment for Drupal.
 *
 * Catch the Twig compile to print the error instead of throw.
 */
class TwigEnvironmentOverride extends TwigEnvironment {

  /**
   * {@inheritdoc}
   */
  public function compileSource(Source $source): string {
    try {
      return parent::compileSource($source);
    }
    catch (SyntaxError $error) {
      $this->enableDebug();
      $this->setCache(FALSE);

      $context = [
        'type' => \get_class($error),
        'message' => $error->getMessage(),
        'line' => $error->getLine(),
        'file' => $error->getFile(),
        'trace' => $error->getTraceAsString(),
      ];
      $message = $this->render('ui_patterns_devel:devel', $context);
      $source = new Source($message, $source->getName(), $source->getPath());
    }

    return parent::compileSource($source);
  }

}
