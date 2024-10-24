<?php

namespace Drupal\rivet;

use Drupal\Component\Serialization\Yaml;
use Drupal\Core\Theme\StarterKitInterface;

/**
 * {@inheritdoc}
 */
final class StarterKit implements StarterKitInterface {

  /**
   * Clean out configurations that should inherit from parent theme.
   *
   * Usage:
   *
   * ```sh
   *    php core/scripts/drupal generate-theme \
   *        --name "Rivet Subtheme" \
   *        --path themes/custom \
   *        --starterkit rivet \
   *        rivet_subtheme
   * ```
   *
   * @todo Cleanup when https://www.drupal.org/i/3364885 lands in Drupal core.
   */
  public static function postProcess(string $working_dir, string $machine_name, string $theme_name): void {

    // Alter info file output for subtheme.
    $info_file = "$working_dir/$machine_name.info.yml";
    $info = Yaml::decode(file_get_contents($info_file));
    // Set the subtheme's base theme to Rivet.
    $info['base theme'] = 'rivet';
    // Set the subtheme's description.
    $info['description'] = 'A subtheme of the <a href="https://www.drupal.org/project/rivet">Rivet Drupal theme</a>.';
    // The subtheme is not a starterkit.
    unset($info['starterkit']);
    // The subtheme does not need to track config_devel.
    unset($info['config_devel']);
    // The subtheme does not need to extend or override libraries.
    unset($info['libraries-extend']);
    unset($info['libraries-override']);
    // Reestablish global libraries.
    $info['libraries'] = [
      'rivet/init',
      'core/normalize',
    ];
    file_put_contents($info_file, Yaml::encode($info));

    // Delete directories that are not needed in subtheme.
    $delete_dirs = [
      "$working_dir/.git",
      "$working_dir/css",
      "$working_dir/images",
      "$working_dir/js",
      "$working_dir/package.json",
      "$working_dir/package-lock.json",
      "$working_dir/README.md",
      "$working_dir/$machine_name.breakpoints.yml",
      "$working_dir/$machine_name.libraries.yml",
      "$working_dir/$machine_name.starterkit.yml",
      "$working_dir/screenshot.png",
      "$working_dir/src",
      "$working_dir/templates",
    ];
    foreach ($delete_dirs as $dir) {
      StarterKit::recursiveRemove($dir);
    }

    // Make SUBTHEME.md into the README.
    $readme_file = "$working_dir/README.md";
    $subtheme_file = "$working_dir/SUBTHEME.md";
    rename($subtheme_file, $readme_file);
    $readme = file_get_contents($readme_file);
    $readme = str_replace($machine_name, 'rivet', $readme);
    $readme = str_replace($theme_name, 'Rivet', $readme);
    $themeName = str_replace(' ', '', $theme_name);
    $readme = str_replace($themeName, 'Rivet', $readme);
    $readme = str_replace('Subtheme Name', $theme_name, $readme);
    $readme = str_replace('machine_name', $machine_name, $readme);
    file_put_contents($readme_file, $readme);
  }

  /**
   * Recursively remove a file or directory and all its contents.
   *
   * @param string $path
   *   The full path to the file or directory being removed.
   *
   * @return bool
   *   TRUE if the file or directory and its contents were removed successfully.
   *   FALSE otherwise.
   */
  private static function recursiveRemove($path): bool {
    if (FALSE === file_exists($path)) {
      return FALSE;
    }

    if (is_dir($path)) {
      /** @var \SplFileInfo[] $files */
      $files = new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
        \RecursiveIteratorIterator::CHILD_FIRST
      );

      foreach ($files as $fileinfo) {
        if ($fileinfo->isDir()) {
          if (FALSE === @rmdir($fileinfo->getRealPath())) {
            // Abort due to the failure.
            return FALSE;
          }
        }
        else {
          if (FALSE === @unlink($fileinfo->getRealPath())) {
            // Abort due to the failure.
            return FALSE;
          }
        }
      }

      return @rmdir($path);
    }
    else {
      return @unlink($path);
    }

  }

}
