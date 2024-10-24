<?php

/**
 * @file
 */

use Drupal\Core\Asset\CssOptimizer;
use Drupal\Component\Utility\Bytes;
use Drupal\Component\Utility\Environment;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\File\FileSystemInterface;

/**
 * This is basically a copy from Color module, with added CSS processing to
 * remove all comments including the sourceMapURL which causes 404 errors.
 *
 * @param $form
 * @param \Drupal\Core\Form\FormStateInterface $form_state
 */
function at_color_scheme_form_submit($form, FormStateInterface $form_state) {

  // Avoid color settings spilling over to theme settings.
  $color_settings = ['theme', 'palette', 'scheme'];
  if ($form_state->hasValue('info')) {
    $color_settings[] = 'info';
  }
  foreach ($color_settings as $setting_name) {
    ${$setting_name} = $form_state->getValue($setting_name);
    $form_state->unsetValue($setting_name);
  }
  if (!isset($info)) {
    return;
  }

  $config = \Drupal::configFactory()->getEditable('color.theme.' . $theme);

  // Resolve palette.
  if ($scheme != '') {
    foreach ($palette as $key => $color) {
      if (isset($info['schemes'][$scheme]['colors'][$key])) {
        $palette[$key] = $info['schemes'][$scheme]['colors'][$key];
      }
    }
    $palette += $info['schemes']['default']['colors'];
  }

  // Make sure enough memory is available.
  if (isset($info['base_image'])) {
    // Fetch source image dimensions.
    $source = \Drupal::service('extension.list.theme')->getPath($theme) . '/' . $info['base_image'];
    list($width, $height) = getimagesize($source);

    // We need at least a copy of the source and a target buffer of the same
    // size (both at 32bpp).
    $required = $width * $height * 8;
    // We intend to prevent color scheme changes if there isn't enough memory
    // available.  memory_get_usage(TRUE) returns a more accurate number than
    // memory_get_usage(), therefore we won't inadvertently reject a color
    // scheme change based on a faulty memory calculation.
    $usage = memory_get_usage(TRUE);
    $memory_limit = ini_get('memory_limit');
    $size = Bytes::toNumber($memory_limit);
    if (!Environment::checkMemoryLimit($usage + $required, $memory_limit)) {
		\Drupal::messenger()->addMessage(t('There is not enough memory available to PHP to change this theme\'s color scheme. You need at least %size more. Check the <a href=":php_url">PHP documentation</a> for more information.', [
		  ':php_url' => 'http://php.net/manual/ini.core.php#ini.sect.resource-limits',
		  '%size' => \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(
		    \Drupal::VERSION,
		    '10.2.0',
		    function() use ($usage, $required, $size) {
		      return \Drupal\Core\StringTranslation\ByteSizeMarkup::create($usage + $required - $size);
		    },
		    function() use ($usage, $required, $size) {
		      return format_size($usage + $required - $size);
		    }
		  )
		]), 'error');
      return;
    }
  }

  // Delete old files.
  $files = $config->get('files');
  if (isset($files)) {
    foreach ($files as $file) {
      \Drupal::service('file_system')->unlink($file);
    }
  }
  if (isset($file) && $file = dirname($file)) {
    \Drupal::service('file_system')->rmdir($file);
  }

  // No change in color config, use the standard theme from color.inc.
  if (implode(',', color_get_palette($theme, TRUE)) == implode(',', $palette)) {
    $config->delete();
    return;
  }

  // Prepare target locations for generated files.
  $id = $theme . '-' . substr(hash('sha256', serialize($palette) . microtime()), 0, 8);
  $paths['color'] = 'public://color';
  $paths['target'] = $paths['color'] . '/' . $id;
  foreach ($paths as $path) {
    /*file_prepare_directory($path, FILE_CREATE_DIRECTORY);*/
    // \Drupal::service('file_system')->prepareDirectory($path, FILE_CREATE_DIRECTORY);
    \Drupal::service('file_system')->prepareDirectory($path, FileSystemInterface::CREATE_DIRECTORY);
  }
  $paths['target'] = $paths['target'] . '/';
  $paths['id'] = $id;
  $paths['source'] = \Drupal::service('extension.list.theme')->getPath($theme).'/';
  $paths['files'] = $paths['map'] = [];

  // Save palette and logo location.
  $config
    ->set('palette', $palette)
    ->set('logo', $paths['target'] . 'logo.svg')
    ->save();

  // Copy over neutral images.
  foreach ($info['copy'] as $file) {
    $base = \Drupal::service('file_system')->basename($file);
    $source = $paths['source'] . $file;
    /*$filepath = file_unmanaged_copy($source, $paths['target'] . $base);*/
    $filepath = \Drupal::service('file_system')->copy($source, $paths['target'] . $base);
    $paths['map'][$file] = $base;
    $paths['files'][] = $filepath;
  }

  // Render new images, if image has been provided.
  if (isset($info['base_image'])) {
    _color_render_images($theme, $info, $paths, $palette);
  }

  // Rewrite theme stylesheets.
  $css = [];
  foreach ($info['css'] as $stylesheet) {
    // Build a temporary array with CSS files.
    $files = [];
    if (file_exists($paths['source'] . $stylesheet)) {
      $files[] = $stylesheet;
    }

    foreach ($files as $file) {
      $css_optimizer = new CssOptimizer();
      // Aggregate @imports recursively for each configured top level CSS file
      // without optimization. Aggregation and optimization will be
      // handled by drupal_build_css_cache() only.
      $style = $css_optimizer->loadFile($paths['source'] . $file, FALSE);

      // Return the path to where this CSS file originated from, stripping
      // off the name of the file at the end of the path.
      $css_optimizer->rewriteFileURIBasePath = base_path() . dirname($paths['source'] . $file) . '/';

      // processCss is a protected method so we can' call it here, instead I've
      // nicked some bits out of it to remove comments/sourceMapURL etc.
      // Strip all comment including source maps which cause 404 errors.
      $comment = '/\*[^*]*\*+(?:[^/*][^*]*\*+)*/';
      // Regexp to match double quoted strings.
      $double_quot = '"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"';
      // Regexp to match single quoted strings.
      $single_quot = "'[^'\\\\]*(?:\\\\.[^'\\\\]*)*'";
      // Strip all comment blocks, but keep double/single quoted strings.
      $style = preg_replace(
        "<($double_quot|$single_quot)|$comment>Ss",
        "$1",
        $style
      );

      // Prefix all paths within this CSS file, ignoring absolute paths.
      $style = preg_replace_callback('/url\([\'"]?(?![a-z]+:|\/+)([^\'")]+)[\'"]?\)/i', [$css_optimizer, 'rewriteFileURI'], $style);

      // Rewrite stylesheet with new colors.
      $style = _color_rewrite_stylesheet($theme, $info, $paths, $palette, $style);
      $base_file = \Drupal::service('file_system')->basename($file);
      $css[] = $paths['target'] . $base_file;
      _color_save_stylesheet($paths['target'] . $base_file, $style, $paths);
    }
  }

  $scheme_name = '';
  if (array_key_exists($scheme, $info['schemes'])) {
    $scheme_name = $info['schemes'][$scheme]['title'];
  }

  // Maintain list of files.
  $config
    ->set('stylesheets', $css)
    ->set('files', $paths['files'])
    ->save();

  \Drupal::messenger()->addMessage(t('Color scheme <i>@scheme</i> saved.', ['@scheme' => $scheme_name]), 'status');
}

/**
 * Logs a notice of the custom color settings.
 *
 * If a custom color scheme has been created in the UI it is injected into the
 * schemes array and saved. You must rename the Custom scheme and give it a
 * unique array key before using the generated file in your theme.
 *
 * Note that color module validates the input of the color form and this is not
 * run if there is a problem, e.g. the user inputting non hexadecimal CSS color
 * strings, which color module validates to avoid XSS.
 *
 * @param $form
 * @param \Drupal\Core\Form\FormStateInterface $form_state
 */
function at_core_log_color_scheme($form, FormStateInterface $form_state) {
  $build_info = $form_state->getBuildInfo();
  $values     = $form_state->getValues();
  $theme      = $build_info['args'][0];
  $palette    = $values['palette'];
  $indent     = str_pad(' ', 6);
  $lines      = explode("\n", var_export($palette, TRUE));

  $message = "    'PaletteName' => array(\n";
  $message .= $indent . "'title' => t('PaletteName'),\n";
  $message .= $indent . "'colors' => array(\n";
  $last_line = $indent . array_pop($lines) . ',';
  $message_scss = '';

  array_shift($lines);
  foreach ($lines as $line) {
    if (strpos($line, ' => ') !== FALSE) {
      $parts = explode(' => ', $line);
      $message .= $indent . $parts[0] . str_pad(' ', (52 - strlen($line))) . '=> ' . $parts[1];
    }
    else {
      $message .= "$indent  $line";
    }
    $message .= "\n";
  }

  foreach ($lines as $line) {
    if (strpos($line, ' => ') !== FALSE) {
      $parts = explode(' => ', $line);
      $part_0 = trim(str_replace("'", "", $parts[0]));
      $part_1 = trim(str_replace(",", ";", $parts[1]));
      $message_scss .= "$" . $part_0 . str_pad(' ', (52 - strlen($line))) . " : " . str_replace("'", "", $part_1) . "\n";
    }
  }

  $message .= "$last_line\n";
  $message .= "    ),\n";
  $message = '<pre>' . $message . "\n\n" . $message_scss . '</pre>';

  \Drupal::logger($theme)->notice($message);

  // Hopefully this goes away if this ever lands, https://www.drupal.org/node/2415663
  // I'm not holding my breath.
  drupal_flush_all_caches();

  $obi_wan_quotes = [
    "You'll find that many of the truths we cling to depend greatly on our own point of view.",
    "Only imperial storm troopers are so precise.",
    "You will never find a more wretched hive of scum and villainy.",
    "These aren't the droids you're looking for.",
    "I felt a great disturbance in the Force.",
    "Only a Sith Lord deals in absolutes.",
    "It's over, Anakin. I have the high ground.",
    "He will learn patience.",
    "Aren't you a little short for a Storm Trooper?",
    "You can't win, Darth.",
    "Use the Force, Luke.",
    "May the force be with you.",
  ];
  $obi_k = array_rand($obi_wan_quotes);
  $obi_wan = $obi_wan_quotes[$obi_k];

  \Drupal::messenger()->addMessage(t('Color scheme logged. Cache cleared. <p>Obi Wan says... <em>"@obiwan"</em></p>', ['@obiwan' => $obi_wan]), 'status');
}
