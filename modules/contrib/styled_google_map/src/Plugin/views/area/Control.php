<?php

namespace Drupal\styled_google_map\Plugin\views\area;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\area\Text;

/**
 * Views area text handler.
 *
 * @ingroup views_area_handlers
 *
 * @ViewsArea("google_map_control")
 */
class Control extends Text {

  /**
   * The list of allowed control's position on the map.
   *
   * @return array
   *   List of available positions for placing the control.
   */
  protected function controlPositions() {
    return [
      'TOP_CENTER' => $this->t('Top center'),
      'TOP_LEFT' => $this->t('Top left'),
      'TOP_RIGHT' => $this->t('Top right'),
      'LEFT_TOP' => $this->t('Left top (below top left)'),
      'RIGHT_TOP' => $this->t('Right top (below top right)'),
      'LEFT_CENTER' => $this->t('Left center (centered between the top left and bottom left)'),
      'RIGHT_CENTER' => $this->t('Right center (centered between the top right and bottom right)'),
      'LEFT_BOTTOM' => $this->t('Left bottom (above bottom left)'),
      'RIGHT_BOTTOM' => $this->t('Right bottom (above bottom right)'),
      'BOTTOM_CENTER' => $this->t('Bottom center'),
      'BOTTOM_LEFT' => $this->t('Bottom left'),
      'BOTTOM_RIGHT' => $this->t('Bottom right'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['position'] = ['default' => 'TOP_LEFT'];
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    $form['position'] = [
      '#title' => $this->t('Position of the control'),
      '#type' => 'select',
      '#default_value' => $this->options['position'],
      '#options' => $this->controlPositions(),
      '#description' => $this->t('For a detailed description on positions visit <a href="@url" target="_blank">Google Maps documentation page</a>', ['@url' => 'https://developers.google.com/maps/documentation/javascript/controls#ControlPositioning']),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function render($empty = FALSE) {
    $output = parent::render($empty);
    if (!empty($output)) {
      return [
        '#type' => 'container',
        '#attributes' => [
          'class' => 'google-map-control',
          'data-position' => Xss::filter($this->options['position']),
          'id' => uniqid('control-'),
        ],
        'control' => $output,
      ];
    }
  }

}
