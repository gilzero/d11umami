<?php

namespace Drupal\at_core\Layout;

use Symfony\Component\Yaml\Parser;
use Drupal\Core\Extension\ThemeExtensionList;

/**
 *
 */
class Layout {

  protected $theme_name;
  protected $layout_markup;
  protected $layout_css;
  protected $layout_name;
  protected $layout_path;
  protected $layout_cid;

  /**
   * Layout constructor.
   *
   * @param $theme_name
   * @param $layout_name
   */
  public function __construct($theme_name, $layout_name) {
    $this->theme_name = $theme_name;
    $this->layout_name = $layout_name;
    $this->layout_path = \Drupal::service('extension.list.theme')->getPath($this->theme_name) . '/layout/' . $this->layout_name;
    $this->layout_cid = $this->theme_name . ':' . $this->layout_name;
  }

  /**
   * Returns layout configuration of a type (normally markup or css yml config).
   * looks for cached config first, if none we parse the respective yml file.
   *
   * @param $type
   *
   * @return array|mixed
   */
  public function LayoutConfig($type) {
    $config_data = [];

    if ($cache = \Drupal::cache()->get($this->layout_cid . ':' . $type)) {
      $config_data = $cache->data;
    }
    else {
      $config_file = $this->layout_path . '/' . $this->layout_name . '.' . $type . '.yml';

      if (file_exists($config_file)) {
        $parser = new Parser();
        $config_data = $parser->parse(file_get_contents($config_file));
      }

      if (!empty($config_data)) {
        \Drupal::cache()->set($this->layout_cid . ':' . $type, $config_data);
      }
    }

    return $config_data;
  }

  /**
   * Returns layout markup.
   *
   * @return array|mixed
   */
  public function getLayoutMarkup() {
    return $this->LayoutConfig('markup');
  }

  /**
   * Returns layout CSS.
   *
   * @return array|mixed
   */
  public function getLayoutCSS() {
    return $this->LayoutConfig('css');
  }

  /**
   * Return a layouts name.
   *
   * @return mixed
   */
  public function getLayoutName() {
    return $this->layout_name;
  }

}
