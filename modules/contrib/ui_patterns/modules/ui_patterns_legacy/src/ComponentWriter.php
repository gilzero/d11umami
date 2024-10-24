<?php

declare(strict_types=1);

namespace Drupal\ui_patterns_legacy;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

/**
 * Component writer.
 */
class ComponentWriter {

  /**
   * Write component definition to YML file.
   */
  public function writeDefinition(string $component_id, array $definition, string $extension_path): void {
    $path = realpath(".") . '/' . $extension_path . '/components/' . $component_id . '/';
    if (!is_dir($path)) {
      mkdir($path, 0775, TRUE);
    }
    $filename = $component_id . '.component.yml';
    // Let's comply with https://prettier.io/
    // - indentation: 2 spaces
    // However, still not 100% compliant:
    // - strings: Symfony does simple quotes, Prettier does double quotes
    // - empty objects: Symfony does "{  }", Prettier does "{}"
    // - arrays of objects: Symfony adds a line break after dash, Prettier
    //   doesn't.
    $yaml = Yaml::dump($definition, 10, 2);
    file_put_contents($path . $filename, $yaml);
  }

  /**
   * Copy component assets to the new folder.
   */
  public function copyAssets(string $extension_path, array $legacy_definition): void {
    $component_id = $legacy_definition['id'];
    $source = realpath(".") . '/' . $legacy_definition['base path'];
    $target = realpath(".") . '/' . $extension_path . '/components/' . $component_id;
    $finder = new Finder();
    $finder->files()->notName('*.patterns.yml')->notName('*.pattern.yml')->notName('*.ui_patterns.yml')->notName('*.html.twig')->in($source);
    foreach ($finder as $file) {
      $path = str_replace($source, $target, $file->getPath());
      if (!is_dir($path)) {
        mkdir($path, 0775, TRUE);
      }
      $from = $file->getPathname();
      $to = str_replace($source, $target, $from);
      copy($from, $to);
    }
  }

  /**
   * Copy component templates to the new locations.
   */
  public function copyTemplates(string $extension_path, array $legacy_definition): void {
    $component_id = $legacy_definition['id'];
    // Copy pattern template to component folder.
    $source = realpath(".") . '/' . $legacy_definition['base path'] . '/pattern-' . $component_id . '.html.twig';
    $target = realpath(".") . '/' . $extension_path . '/components/' . $component_id . '/' . $component_id . '.twig';
    if (file_exists($source)) {
      copy($source, $target);
      return;
    }
    // Try with dash instead of underscores.
    $source = realpath(".") . '/' . $legacy_definition['base path'] . '/pattern-' . str_replace('_', '-', $component_id) . '.html.twig';
    if (file_exists($source)) {
      copy($source, $target);
    }
  }

  /**
   * Check if there is some preview or variant templates.
   */
  public function checkOtherTemplates(array $legacy_definition): void {
    $component_id = $legacy_definition['id'];
    $path = realpath(".") . '/' . $legacy_definition['base path'] . '/pattern-' . $component_id . '--preview.html.twig';
    if (file_exists($path)) {
      print("⚠️ " . $component_id . " has a preview template which will not be converted. Use the new stories system instead.\n");
    }
    // Example: pattern-button--variant-danger.html.twig.
    if (!isset($legacy_definition["variants"]) || !is_array($legacy_definition["variants"])) {
      return;
    }
    foreach (array_keys($legacy_definition["variants"]) as $variant_id) {
      $message = "⚠️ " . $component_id . " has a " . $variant_id . " variant template which will not be converted, because this mechanism doesn't exist in SDC.\n";
      $path = realpath(".") . '/' . $legacy_definition['base path'] . '/pattern-' . $component_id . '--variant-' . $variant_id . '.html.twig';
      if (file_exists($path)) {
        print($message);
        continue;
      }
      // Try with dash instead of underscores.
      $path = realpath(".") . '/' . $legacy_definition['base path'] . '/pattern-' . str_replace('_', '-', $component_id) . '--variant-' . $variant_id . '.html.twig';
      if (file_exists($path)) {
        print($message);
      }
    }
  }

}
