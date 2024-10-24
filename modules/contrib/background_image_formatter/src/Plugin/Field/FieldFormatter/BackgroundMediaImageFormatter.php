<?php

namespace Drupal\background_image_formatter\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Annotation\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\image\Entity\ImageStyle;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceEntityFormatter;
use Drupal\Core\Field\FieldDefinitionInterface;

/**
 * Plugin implementation of the background_media_image_formatter.
 *
 * @FieldFormatter(
 *  id = "background_media_image_formatter",
 *  label = @Translation("Background Image"),
 *  field_types = {"entity_reference"}
 * )
 */
class BackgroundMediaImageFormatter extends EntityReferenceEntityFormatter {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'image_style' => '',
      'background_image_output_type' => 'inline',
      'background_image_selector' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {

    $element = [];

    $image_styles = image_style_options(FALSE);

    $element['image_style'] = [
      '#title' => $this->t('Image style'),
      '#type' => 'select',
      '#options' => $image_styles,
      '#default_value' => $this->getSetting('image_style'),
      '#empty_option' => $this->t('None (original image)'),
      '#description' => $this->t('Select the image style to use.'),
    ];

    $element['background_image_output_type'] = [
      '#title' => $this->t('Output To'),
      '#type' => 'select',
      '#options' => [
        'inline' => $this->t('Write background-image to inline style attribute'),
        'css' => $this->t('Write background-image to CSS selector'),
      ],
      '#default_value' => $this->getSetting('background_image_output_type'),
      '#required' => TRUE,
      '#description' => $this->t('Define how background-image will be printed to the dom.'),
    ];

    $element['background_image_selector'] = [
      '#title' => $this->t('CSS Selector'),
      '#type' => 'textfield',
      '#default_value' => $this->getSetting('background_image_selector'),
      '#required' => FALSE,
      '#description' => $this->t('CSS selector that image(s) are attached to.'),
    ];

    return $element;

  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {

    $summary = [];

    $image_styles = image_style_options(FALSE);

    unset($image_styles['']);

    $select_style = $this->getSetting('image_style');

    if (isset($image_styles[$select_style])) {
      $summary[] = $this->t('URL for image style: @style', ['@style' => $image_styles[$select_style]]);
    }
    else {
      $summary[] = $this->t('Original image');
    }

    $summary[] = $this->t('Output type: @output_type', ['@output_type' => $this->getSetting('background_image_output_type')]);

    $summary[] = $this->t('The CSS selector <code>@background_image_selector</code> will be created with the image set to the background-image property.', [
      '@background_image_selector' => $this->getSetting('background_image_selector') . '_id',
    ]);

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {

    $elements = [];

    $image_style = NULL;

    if (!$this->isBackgroundImageDisplay()) {
      return $elements;
    }

    $image_style = $this->getSetting('image_style');

    if (!empty($image_style)) {
      $image_style = ImageStyle::load($image_style);
    }

    foreach ($items as $delta => $item) {

      if (!$item->entity) {
        continue;
      }

      if (isset($item->entity->get('thumbnail')->entity)) {
        $image_uri = $item->entity->get('thumbnail')->entity->createFileUrl();

        $id = $item->entity->id();

        if ($image_style) {
          $image_uri = $item->entity->get('thumbnail')->entity->getFileUri();
          $image_url = $image_style->buildUrl($image_uri);
          $style_uri = $image_style->buildUri($image_uri);

          // Check if image style file has been generated.
          if (!file_exists($image_url)) {
            $image_style->createDerivative($image_uri, $style_uri);
          }

          // When page caching is enabled, try serving the image
          // from the correct HTTP protocol.
          [, $image_path] = explode('://', $image_url, 2);
          $image_uri = '//' . $image_path;
        }

        $selector = strip_tags($this->getSetting('background_image_selector'));

        // Only add an id when using inline styles.
        if ($this->getSetting('background_image_output_type') == 'inline') {
          $selector .= '_' . $id;
        }

        $theme = [
          '#background_image_selector' => $selector,
          '#image_uri' => $image_uri,
        ];

        switch ($this->getSetting('background_image_output_type')) {
          case 'css':

            $data = [
              '#tag' => 'style',
              '#value' => $this->generateCssString($theme),
            ];

            $elements['#attached']['html_head'][] = [
              $data,
              'background_image_formatter_' . $id,
            ];

            break;

          case 'inline':

            $theme['#theme'] = 'background_image_formatter_inline';

            $elements[$delta] = [
              '#markup' => \Drupal::service('renderer')->render($theme),
            ];

            break;
        }
      }
    }

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  protected function isBackgroundImageDisplay() {
    return $this->getPluginId() == 'background_media_image_formatter';
  }

  /**
   * {@inheritdoc}
   */
  protected function generateCssString($theme) {
    return $theme['#background_image_selector'] . ' {background-image: url("' . $theme['#image_uri'] . '");}';
  }

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(FieldDefinitionInterface $field_definition) {
    if (parent::isApplicable($field_definition)) {
      if ($field_definition->getFieldStorageDefinition()->getSetting('target_type') == 'media') {
        return TRUE;
      }
    }
    return FALSE;
  }

}
