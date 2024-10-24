<?php

declare(strict_types=1);

namespace Drupal\ui_examples\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ui_examples\ExamplePluginManagerInterface;
use Drupal\ui_examples\ExampleSyntaxConverterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller to display the examples library.
 */
class ExamplesLibraryController extends ControllerBase {

  /**
   * Examples manager service.
   *
   * @var \Drupal\ui_examples\ExamplePluginManagerInterface
   */
  protected ExamplePluginManagerInterface $examplesManager;

  /**
   * The syntax converter service.
   *
   * @var \Drupal\ui_examples\ExampleSyntaxConverterInterface
   */
  protected ExampleSyntaxConverterInterface $syntaxConverter;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    $instance = parent::create($container);
    $instance->examplesManager = $container->get('plugin.manager.ui_examples');
    $instance->syntaxConverter = $container->get('ui_examples.example_syntax_converter');
    return $instance;
  }

  /**
   * Render examples library page.
   *
   * @return array
   *   Examples overview page render array.
   */
  public function overview(): array {
    $examples = [];
    foreach ($this->examplesManager->getDefinitions() as $definition) {
      $examples[$definition->id()] = $definition->toArray() + [
        'definition' => $definition->toArray(),
      ];
    }

    return [
      '#theme' => 'ui_examples_overview_page',
      '#examples' => $examples,
    ];
  }

  /**
   * Render one example page.
   *
   * @param string $name
   *   The ID of the example plugin.
   *
   * @return array
   *   Style page render array.
   */
  public function single(string $name): array {
    /** @var \Drupal\ui_examples\Definition\ExampleDefinition $example */
    $example = $this->examplesManager->getDefinition($name);
    return $this->syntaxConverter->convertArray($example->getRender());
  }

  /**
   * Render one example title.
   *
   * @param string $name
   *   The ID of the example plugin.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup|string
   *   Example title.
   */
  public function title(string $name): string|TranslatableMarkup {
    /** @var \Drupal\ui_examples\Definition\ExampleDefinition $example */
    $example = $this->examplesManager->getDefinition($name);
    return $example->getLabel();
  }

}
