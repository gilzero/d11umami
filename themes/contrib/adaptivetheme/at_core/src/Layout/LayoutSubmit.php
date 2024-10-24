<?php

namespace Drupal\at_core\Layout;

use Drupal\at_core\File\FileOperations;
use Drupal\at_core\File\DirectoryOperations;
use Drupal\Component\Utility\Unicode;
use Symfony\Component\Yaml\Parser;
use Drupal\Component\Utility\Html;
use Drupal\at_core\Theme\ThemeInfo;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Extension\ThemeExtensionList;

/**
 *
 */
class LayoutSubmit {

  /**
   * The layout configuration.
   *
   * @var array
   */
  public $layoutConfig;
  /**
   * The CSS configuration.
   *
   * @var array
   */
  public $cssConfig;
  /**
   * The layout name.
   *
   * @var string
   */
  public $layoutName;
  /**
   * The layout path.
   *
   * @var string
   */
  public $layoutPath;
  /**
   * The form values.
   *
   * @var array
   */
  public $formValues;

  /**
   * The active theme name.
   */
  protected $theme_name;

  /**
   * Form state values.
   */
  protected $values;

  /**
   * Constructor.
   */
  public function __construct($theme_name, $values) {
    $this->theme_name = $theme_name;
    $layout_data = new LayoutCompatible($this->theme_name);
    $layout_compatible_data = $layout_data->getCompatibleLayout();
    $this->layout_config = $layout_compatible_data['layout_config'];
    $this->css_config = $layout_compatible_data['css_config'];
    $this->layout_name = $layout_compatible_data['layout_name'];
    $this->layout_path = \Drupal::service('extension.list.theme')->getPath($this->layout_config['layout_provider']) . '/layout/' . $this->layout_name;
    $this->form_values = $values;
  }

  /**
   * Convert the table row array to settings the rest of the system can use.
   */
  public function convertLayoutSettings() {
    $settings = [];
    foreach ($this->form_values['table_layout_settings'] as $row_key => $row_values) {
      foreach ($row_values['layout'] as $setting_layout => $layout) {
        $settings[$setting_layout] = $layout;
      }
      foreach ($row_values['weight'] as $setting_weight => $weight) {
        $settings[$setting_weight] = $weight;
      }
      foreach ($row_values['hide'] as $setting_hide => $hide) {
        $settings[$setting_hide] = $hide;
      }
    }

    return $settings;
  }

  /**
   * Build and save the suggestions layout css files.
   */
  public function saveLayoutSuggestionsCSS() {
    $breakpoints_group = \Drupal::service('breakpoint.manager')->getBreakpointsByGroup($this->form_values['settings_breakpoint_group_layout']);
    $generated_files_path = $this->form_values['settings_generated_files_path'];
    $css_data = [];

    foreach ($this->form_values['settings_suggestions'] as $suggestion_key => $suggestions_name) {
      foreach ($breakpoints_group as $breakpoint_id => $breakpoint_value) {
        foreach ($this->layout_config['rows'] as $row_key => $row_values) {
          // Match the key set in the form, hacking on get label.
          $breakpoint_layout_key = strtolower(preg_replace("/\W|_/", "", $breakpoint_value->getLabel()));
          $css_data[$suggestion_key][$breakpoint_layout_key]['query'] = $breakpoint_value->getMediaQuery();

          // Layout with impossible BC.
          if (!empty($this->form_values['settings_' . $suggestion_key . '_' . $breakpoint_layout_key . '_' . $row_key])) {
            $css_data[$suggestion_key][$breakpoint_layout_key]['rows'][$row_key] = $this->form_values['settings_' . $suggestion_key . '_' . $breakpoint_layout_key . '_' . $row_key];
          }
          elseif (!empty($this->form_values['table_layout_settings'][$row_key]['layout']['settings_' . $suggestion_key . '_' . $breakpoint_layout_key . '_' . $row_key])) {
            $css_data[$suggestion_key][$breakpoint_layout_key]['rows'][$row_key]['layout'] = $this->form_values['table_layout_settings'][$row_key]['layout']['settings_' . $suggestion_key . '_' . $breakpoint_layout_key . '_' . $row_key];
          }
          else {
            $css_data[$suggestion_key][$breakpoint_layout_key]['rows'][$row_key]['layout'] = 'not_set';
          }

          // Row order (weight).
          if (!empty($this->form_values['table_layout_settings'][$row_key]['weight']['settings_' . $suggestion_key . '_' . $breakpoint_layout_key . '_' . $row_key . '_weight'])) {
            $css_data[$suggestion_key][$breakpoint_layout_key]['rows'][$row_key]['weight'] = $this->form_values['table_layout_settings'][$row_key]['weight']['settings_' . $suggestion_key . '_' . $breakpoint_layout_key . '_' . $row_key . '_weight'];
          }
          else {
            $css_data[$suggestion_key][$breakpoint_layout_key]['rows'][$row_key]['weight'] = 1;
          }

          // Row hidden (hide).
          if (!empty($this->form_values['table_layout_settings'][$row_key]['hide']['settings_' . $suggestion_key . '_' . $breakpoint_layout_key . '_' . $row_key . '_hide'])) {
            $css_data[$suggestion_key][$breakpoint_layout_key]['rows'][$row_key]['hide'] = $this->form_values['table_layout_settings'][$row_key]['hide']['settings_' . $suggestion_key . '_' . $breakpoint_layout_key . '_' . $row_key . '_hide'];
          }
          else {
            $css_data[$suggestion_key][$breakpoint_layout_key]['rows'][$row_key]['hide'] = 0;
          }
        }
      }
    }

    // Initialize or set vars.
    $output = [];
    $css_rows = [];
    $css_file = [];
    $row_hide_css = [];
    $row_weight_css = [];
    $path_to_css_files = $this->layout_path . '/' . $this->css_config['css_files_path'];

    foreach ($css_data as $suggestion => $breakpoints) {
      foreach ($breakpoints as $breakpoint_keys => $breakpoint_values) {
        foreach ($breakpoint_values['rows'] as $row_keys => $row_values) {
          $row_key = str_replace('_', '-', $row_keys);

          if (isset($row_values['hide']) && $row_values['hide'] == 1) {
            $row_hide_css[$suggestion][$breakpoint_keys][$row_keys] = '.l-' . $row_key . ' {display: none;}';
          }
          else {
            if (isset($row_values['weight'])) {
              $row_weight_css[$suggestion][$breakpoint_keys][$row_keys] = '.l-' . $row_key . ' { -webkit-order: ' . $row_values['weight'] . '; -ms-flex-order: ' . $row_values['weight'] . '; order: ' . $row_values['weight'] . "; }";
            }

            foreach ($this->css_config['css'] as $css_key => $css_values) {
              if (file_exists($path_to_css_files . '/' . $css_key . '/' . $row_values['layout'] . '.css')) {
                $css_file[$suggestion][$breakpoint_keys][$row_keys] = file_get_contents($path_to_css_files . '/' . $css_key . '/' . $row_values['layout'] . '.css');
                // TODO review fix for underscores in row names.
                $replace_class = 'pr-' . $row_key;
                if (!empty($css_file[$suggestion][$breakpoint_keys][$row_keys])) {
                  $file = str_replace($row_values['layout'], $replace_class, $css_file[$suggestion][$breakpoint_keys][$row_keys]);
                  $css_rows[$suggestion][$breakpoint_keys][$breakpoint_keys . '_' . $row_keys] = $file;
                }
              }
            }
          }
        }

        $output[$suggestion][] = "/* Begin breakpoint: $breakpoint_keys */\n" . '@media ' . $breakpoint_values['query'] . " {\n";
        if (!empty($row_hide_css[$suggestion][$breakpoint_keys])) {
          $output[$suggestion][] = implode("\n", $row_hide_css[$suggestion][$breakpoint_keys]) . "\n";
        }
        if (!empty($row_weight_css[$suggestion][$breakpoint_keys])) {
          $output[$suggestion][] = implode("\n", $row_weight_css[$suggestion][$breakpoint_keys]) . "\n";
        }
        if (!empty($css_rows[$suggestion][$breakpoint_keys])) {
          $output[$suggestion][] = implode("\n", $css_rows[$suggestion][$breakpoint_keys]);
        }
        $output[$suggestion][] = "}\n/* End breakpoint */\n";
      }
    }

    // Get the layouts global CSS if any.
    $global_css = '';
    if ($this->css_config['css_global_layout']) {
      $global_css = file_get_contents($path_to_css_files . '/' . $this->css_config['css_global_layout']);
    }

    // Max widths.
    $max_width = [];
    if (isset($this->form_values['settings_max_width_enable']) && $this->form_values['settings_max_width_enable'] === 1) {
      $max_width_value = Html::escape($this->form_values['settings_max_width_value']);
      $max_width['global'] = '.l-rw { max-width: ' . trim($max_width_value) . $this->form_values['settings_max_width_unit'] . '; }';

      // Per row.
      if (isset($this->form_values['settings_max_width_enable_rows']) && $this->form_values['settings_max_width_enable_rows'] === 1) {
        foreach ($this->layout_config['rows'] as $row_key => $row_values) {
          if (isset($this->form_values['settings_max_width_value_' . $row_key]) && !empty($this->form_values['settings_max_width_value_' . $row_key])) {
            $max_width_rows[$row_key]['value'] = trim($this->form_values['settings_max_width_value_' . $row_key]);
            $max_width_rows[$row_key]['unit'] = trim($this->form_values['settings_max_width_unit_' . $row_key]);
            $max_width[$row_key] = '.pr-' . str_replace('_', '-', $row_key) . '__rw { max-width: ' . $max_width_rows[$row_key]['value'] . $max_width_rows[$row_key]['unit'] . '; }';
          }
        }
      }
    }

    // Attribution row order, we need this for BC.
    $attribution_order = '.l-attribution { -webkit-order: 100; -ms-flex-order: 100; order: 100 ;}';

    // Don't regenerate CSS files to be removed.
    foreach ($this->form_values as $values_key => $values_value) {
      if (substr($values_key, 0, 18) === 'delete_suggestion_') {
        if ($values_value === 1) {
          $delete_suggestion_keys[] = mb_substr($values_key, 18);
          /*$delete_suggestion_keys[] = Unicode::substr($values_key, 18);*/
        }
      }
    }
    if (!empty($delete_suggestion_keys)) {
      foreach ($delete_suggestion_keys as $template_to_remove) {
        unset($output[$template_to_remove]);
      }
    }

    $saved_css = [];
    foreach ($output as $suggestion => $css) {
      if (!empty($css)) {
        $message = '/* Layout CSS for: ' . str_replace('_', '-', $suggestion) . '.html.twig, generated: ' . date(DATE_RFC822) . ' */';
        $file_content = $message . "\n\n" . $global_css . "\n" . implode("\n", $css) . "\n" . implode("\n", $max_width) . "\n" . $attribution_order . "\n";
        $file_name = $this->theme_name . '.layout.' . str_replace('_', '-', $suggestion) . '.css';
        $filepath = "$generated_files_path/$file_name";
        /*file_unmanaged_save_data($file_content, $filepath, FILE_EXISTS_REPLACE);*/
        \Drupal::service('file_system')->saveData($file_content, $filepath, FileSystemInterface::EXISTS_REPLACE);
        if (file_exists($filepath)) {
          $saved_css[] = $file_name;
        }
      }
    }

    if (!empty($saved_css)) {
      $saved_css_message_list = [
        '#theme' => 'item_list',
        '#items' => $saved_css,
      ];
      \Drupal::messenger()->addMessage(t('The following <b>CSS</b> files were generated in: <code>@generated_files_path</code> @saved_css', [
          '@saved_css' => \Drupal::service('renderer')->renderPlain($saved_css_message_list),
          '@generated_files_path' => $generated_files_path . '/'
        ]
      ), 'status');
    }
  }

  /**
   * Update the themes info file with new regions.
   */
  public function saveLayoutRegions() {
    $regions = [];

    foreach ($this->layout_config['rows'] as $row => $row_values) {
      foreach ($row_values['regions'] as $region_key => $region_values) {
        if (isset($region_values['label'])) {
          $regions[$region_key] = $region_values['label'];
        }
        // BC.
        else {
          $regions[$region_key] = $region_values;
        }
      }
    }

    $regions['page_top'] = 'Page top';
    $regions['page_bottom'] = 'Page bottom';

    // Get the paths to this theme and all dependant skin themes.
    $theme_info_data = new ThemeInfo($this->theme_name);
    $theme_paths[$this->theme_name] = $theme_info_data->getThemeInfo()->getPath();
    $sub_themes_info = $theme_info_data->getSubThemesInfo();

    if (!empty($sub_themes_info)) {
      $sub_theme_paths = $theme_info_data->getSubThemesPaths();
      foreach ($sub_themes_info as $machine_name => $sub_theme) {
        if (isset($sub_theme['subtheme type']) && $sub_theme['subtheme type'] == 'adaptive_skin') {
          $theme_paths = array_merge($theme_paths, $sub_theme_paths);
        }
      }
    }

    // Todo - move to method?
    // Create a backup.
    //    if ($this->form_values['settings_enable_backups'] == 1) {
    //
    //      $fileOperations = new FileOperations();
    //      $directoryOperations = new DirectoryOperations();
    //
    //      $backup_path = $directoryOperations->directoryPrepare($backup_file_path = [$path, 'backup', 'info']);
    //
    //      // Add a date time string to make unique and for easy identification,
    //      // save as .txt to avoid conflicts.
    //      $backup_file =  $info_file . '.'. date(DATE_ISO8601) . '.txt';
    //
    //      $file_paths = [
    //       'copy_source' => $file_path,
    //       'copy_dest' => $backup_path . '/' . $info_file,
    //       'rename_oldname' => $backup_path . '/' . $info_file,
    //       'rename_newname' => $backup_path . '/' . $backup_file,
    //      ];
    //      $fileOperations->fileCopyRename($file_paths);
    //    }

    // Parse, format and save info with new regions.
    $parser = new Parser();
    $buildInfo = new FileOperations();

    foreach ($theme_paths as $theme_name => $path) {
      $file_path = $path . '/' . $theme_name . '.info.yml';
      $theme_info_data = $parser->parse(file_get_contents($file_path));
      $theme_info_data['regions'] = $regions;
      $rebuilt_info = $buildInfo->fileBuildInfoYml($theme_info_data);
      /*file_unmanaged_save_data($rebuilt_info, $file_path, FILE_EXISTS_REPLACE);*/
      \Drupal::service('file_system')->saveData($rebuilt_info, $file_path, FileSystemInterface::EXISTS_REPLACE);
    }
  }

  /**
   * Build and save twig templates.
   * Save each suggestion template, these are saved every time the layout
   * settings are saved because the rows and regions might change, so we re-save
   * every template, every time the form is submitted.
   */
  public function saveLayoutSuggestionsMarkup() {
    $template_suggestions = [];
    $fileOperations = new FileOperations();
    $directoryOperations = new DirectoryOperations();

    if (!empty($this->form_values['settings_suggestions'])) {
      $template_suggestions = $this->form_values['settings_suggestions'];
    }

    if (!empty($this->form_values['ts_name'])) {
      $template_suggestions['page__' . $this->form_values['ts_name']] = 'page__' . $this->form_values['ts_name'];
    }

    // Don't regenerate templates to be deleted.
    foreach ($this->form_values as $values_key => $values_value) {
      if (substr($values_key, 0, 18) === 'delete_suggestion_') {
        if ($values_value === 1) {
          /*$delete_suggestion_keys[] = Unicode::substr($values_key, 18);*/
          $delete_suggestion_keys[] = mb_substr($values_key, 18);
        }
      }
    }
    if (!empty($delete_suggestion_keys)) {
      foreach ($delete_suggestion_keys as $template_to_remove) {
        unset($template_suggestions[$template_to_remove]);
      }
    }

    // Template path.
    $template_file = $this->layout_path . '/' . $this->layout_name . '.html.twig';

    // Path to target theme where the template will be saved.
    $path = \Drupal::service('extension.list.theme')->getPath($this->theme_name);

    // Remove if this exists, its now deprecated, this is a BC layer so to speak.
    $directoryOperations->directoryRemove($path . '/templates/page');
    $template_directory = $path . '/templates/generated';

    // Check and create the templates directory if does not exist.
    if (!file_exists($path . '/templates')) {
      \Drupal::service('file_system')->mkdir($path . '/templates');
    }
    if (!file_exists($template_directory)) {
      \Drupal::service('file_system')->mkdir($template_directory);
    }

    // Initialize vars.
    $row_regions = [];
    $templates = [];
    $saved_templates = [];

    // We have to save every template every time, in case a row has been added to the layout, all template MUST update.
    // This could be changed later to only do this IF a row has been added, we're not that flash right now :)
    foreach ($template_suggestions as $suggestion_key => $suggestions_name) {

      $output = [];
      $suggestion_key = Html::escape($suggestion_key);

      // Doc block.
      $doc = [];
      $doc[$suggestion_key][] = '{#';
      $doc[$suggestion_key][] = '/**';
      $doc[$suggestion_key][] = ' * Layout provider: ' . $this->layout_name;
      $doc[$suggestion_key][] = ' * Template suggestion: ' . $suggestion_key;
      $doc[$suggestion_key][] = ' * Theme: ' . $this->theme_name;
      $doc[$suggestion_key][] = ' * Generated on: ' . date(DATE_RFC822);
      $doc[$suggestion_key][] = ' */';
      $doc[$suggestion_key][] = '#}' . "\n";
      $docblock[$suggestion_key] = implode("\n", $doc[$suggestion_key]);

      // Attach the layout library.
      $generated_files_path = $this->form_values['settings_generated_files_path'];
      $layout_file = $this->theme_name . '.layout.' . str_replace('_', '-', $suggestion_key) . '.css';
      if (file_exists($generated_files_path . '/' . $layout_file)) {
        $library = $this->theme_name . '/' . $this->theme_name . '.layout.' . $suggestion_key;
      }
      else {
        $library = $this->theme_name . '/' . $this->theme_name . '.layout.page';
      }
      $attach_layout = "{{ attach_library('$library') }}";

      // Get the template file, if not found attempt to generate the template.
      if (file_exists($template_file)) {
        $template = file_get_contents($template_file);
      }
      else {
        $generated[$suggestion_key][] = '<div{{ attributes }}>' . "\n";
        $generated[$suggestion_key][] = '  {{ rows }}' . "\n";
        $generated[$suggestion_key][] = "  {{ attribution }}" . "\n";
        $generated[$suggestion_key][] = '</div>' . "\n";
        $template[$suggestion_key] = implode($generated[$suggestion_key]);
      }

      // Prepend the doc block and attached layout to the template markup.
      $template_markup[$suggestion_key] = $docblock[$suggestion_key] . $attach_layout . "\n" . $template[$suggestion_key];

      // Set the template file name, either it's page or a page suggestion.
      if ($suggestion_key !== 'page') {
        $template_file = str_replace('_', '-', $suggestion_key) . '.html.twig';
      }
      else {
        $template_file = 'page.html.twig';
      }

      // Set the template path.
      $template_path = $template_directory . '/' . $template_file;

      // Build array of files to be saved.
      $templates[$suggestion_key]['markup'] = $template_markup[$suggestion_key];
      $templates[$suggestion_key]['template_path'] = $template_path;
      $templates[$suggestion_key]['template_name'] = $template_file;

      // Create a backup.
      if ($this->form_values['settings_enable_backups'] == 1) {
        $backup_path = $directoryOperations->directoryPrepare($backup_file_path = [$path, 'backup', 'templates']);

        // Add a date time string to make unique and for easy identification,
        // save as .txt to avoid conflicts.
		$date = new DateTime();
		$backup_file = $template_file . '.' . $date->format('Y-m-d\TH:i:sO') . '.txt';

        $file_paths = [
          'copy_source' => $template_path,
          'copy_dest' => $backup_path . '/' . $template_file,
          'rename_oldname' => $backup_path . '/' . $template_file,
          'rename_newname' => $backup_path . '/' . $backup_file,
        ];

        $fileOperations->fileCopyRename($file_paths);
      }
    }

    foreach ($templates as $suggestion => $template_values) {
      if (!file_exists($templates[$suggestion]['template_path'])) {
        $new_template = $templates[$suggestion]['template_name'];
        $new_template_message = t('It looks like you generated a new template: <b>@new_template</b>. Save the layout settings again so they will take effect.', ['@new_template' => $new_template]);
      }
      /*file_unmanaged_save_data($templates[$suggestion]['markup'], $templates[$suggestion]['template_path'], FILE_EXISTS_REPLACE);*/
      \Drupal::service('file_system')->saveData($templates[$suggestion]['markup'], $templates[$suggestion]['template_path'], FileSystemInterface::EXISTS_REPLACE);
      if (file_exists($templates[$suggestion]['template_path'])) {
        $saved_templates[] = $templates[$suggestion]['template_name'];
      }
    }

    if (!empty($saved_templates)) {
      $saved_templates_message_list = [
        '#theme' => 'item_list',
        '#items' => $saved_templates,
      ];
      \Drupal::messenger()->addMessage(t('The following <b>templates</b> were generated in: <code>@template_directory</code> @saved_templates', [
          '@saved_templates' => \Drupal::service('renderer')->renderPlain($saved_templates_message_list),
          '@template_directory' => $template_directory . '/'
        ]
      ), 'status');
    }
    if (isset($new_template_message)) {
      \Drupal::messenger()->addMessage($new_template_message, 'status');
    }
  }

}
