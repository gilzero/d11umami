<?php

declare(strict_types=1);

namespace Drupal\ui_patterns_legacy\Drush\Commands;

use Drupal\Component\Plugin\Discovery\CachedDiscoveryInterface;
use Drupal\ui_patterns_legacy\ComponentConverter;
use Drupal\ui_patterns_legacy\ComponentDiscovery;
use Drupal\ui_patterns_legacy\ComponentWriter;
use Drush\Attributes as CLI;
use Drush\Commands\AutowireTrait;
use Drush\Commands\DrushCommands;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Finder\Finder;

/**
 * Drush commands of UI Patterns Legacy module.
 */
final class UiPatternsLegacyCommands extends DrushCommands {
  use AutowireTrait;

  /**
   * {@inheritdoc}
   */
  public function __construct(
    #[Autowire(service: 'ui_patterns_legacy.component_converter')]
    private readonly ComponentConverter $converter,
    #[Autowire(service: 'ui_patterns_legacy.discovery')]
    private readonly ComponentDiscovery $discovery,
    #[Autowire(service: 'ui_patterns_legacy.component_writer')]
    private readonly ComponentWriter $writer,
    #[Autowire(service: 'plugin.manager.sdc')]
    private readonly CachedDiscoveryInterface $componentsManager,
  ) {
    parent::__construct();
  }

  /**
   * Migrate components from UI Patterns 1.x to UI Patterns 2.x.
   */
  #[CLI\Command(name: 'ui-patterns:migrate', aliases: ['upm'])]
  #[CLI\Argument(name: 'extension', description: 'Module or theme machine name.')]
  #[CLI\Usage(name: 'ui-patterns:migrate my_theme', description: 'Convert components, replace dependencies and API calls.')]
  public function migrate(string $extension): void {
    if (!$this->discovery->extensionExists($extension)) {
      print("Not found or not activated: " . $extension . "\n");
      return;
    }
    $extension_path = $this->discovery->getExtensionPath($extension);
    $this->convertComponents($extension, $extension_path);
    $this->replaceDeprecatedCalls($extension_path);
    $this->changeInfoFile($extension, $extension_path);
    $this->changeComposerFile($extension_path);
  }

  /**
   * Convert components.
   */
  protected function convertComponents(string $extension, string $extension_path): void {
    $legacy_definitions = $this->discovery->discover($extension);
    $this->converter->setExtension($extension);
    // First time to create all definitions.
    foreach ($legacy_definitions as $legacy_definition) {
      $component = $this->converter->convert($legacy_definition);
      $component_id = $legacy_definition['id'];
      $this->writer->writeDefinition($component_id, $component, $extension_path);
      $this->writer->copyTemplates($extension_path, $legacy_definition);
      $this->writer->checkOtherTemplates($legacy_definition);
      $this->writer->copyAssets($extension_path, $legacy_definition);
    }
    // Second time for tasks which need all definitions to be already created.
    $this->componentsManager->clearCachedDefinitions();
    foreach ($legacy_definitions as $legacy_definition) {
      $component = $this->converter->convert($legacy_definition);
      $component_id = $legacy_definition['id'];
      $this->writer->writeDefinition($component_id, $component, $extension_path);
    }
    $components = $this->componentsManager->getDefinitions();
    foreach ($components as $component) {
      if ($extension !== $component['provider']) {
        continue;
      }
      $errors = $this->converter->validate($component);
      foreach ($errors as $error) {
        $this->logger()->error($error);
      }
    }
  }

  /**
   * Replace deprecated calls.
   *
   * LinksSettingType::normalize() is now automatically managed with
   * PropTypeInterface::normalize(). No need for manual calls anymore.
   * So let's replace them by a temporary dummy.
   */
  protected function replaceDeprecatedCalls(string $extension_path): void {
    $finder = new Finder();

    $patterns = ['*.php', '*.inc', '*.module', '*.theme'];
    $finder->files()->name($patterns)->in($extension_path);
    foreach ($finder as $file) {
      $path = $file->getPathname();
      $content = file_get_contents($path) ?: "";
      $content = str_replace('Drupal\ui_patterns_settings\Plugin\UiPatterns\SettingType', 'Drupal\ui_patterns_legacy\Dummy', $content);
      file_put_contents($path, $content);
    }
  }

  /**
   * Change info file.
   */
  protected function changeInfoFile(string $extension, string $extension_path): void {
    $info_path = $extension_path . "/" . $extension . ".info.yml";
    // Just a safeguard because a info file is always expected.
    if (!file_exists($info_path)) {
      return;
    }
    $content = file_get_contents($info_path) ?: "";
    $content = preg_replace('/ui_patterns_settings.*/', 'ui_patterns:ui_patterns_legacy', $content);
    file_put_contents($info_path, $content);
  }

  /**
   * Change composer file.
   */
  protected function changeComposerFile(string $extension_path): void {
    $composer_path = $extension_path . "/composer.json";
    // Some themes or modules don't have a composer file. It is generated by
    // the Drupal pipeline if missing.
    if (!file_exists($composer_path)) {
      return;
    }
    $content = file_get_contents($composer_path) ?: "";
    $data = json_decode($content, TRUE);
    unset($data["require"]["drupal/ui_patterns_settings"]);
    $data["require"]["drupal/ui_patterns"] = "^2.0";
    file_put_contents($composer_path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
  }

}
