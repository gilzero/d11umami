<?php

declare(strict_types=1);

namespace Drupal\ui_patterns\Plugin\UiPatterns\Source;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ui_patterns\Attribute\Source;
use Drupal\ui_patterns\SourcePluginPropValue;

/**
 * Plugin implementation of the source.
 */
#[Source(
  id: 'checkboxes',
  label: new TranslatableMarkup('Checkboxes'),
  description: new TranslatableMarkup('A set of checkboxes.'),
  prop_types: ['enum_list'],
  tags: ['widget']
)]
class CheckboxesWidget extends SourcePluginPropValue {

  /**
   * {@inheritdoc}
   */
  public function getPropValue(): mixed {
    $value = parent::getPropValue() ?? [];
    $value = is_scalar($value) ? [$value] : $value;
    return array_filter($value);
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $form = parent::settingsForm($form, $form_state);
    $form['value'] = [
      '#type' => 'checkboxes',
      '#default_value' => $this->getSetting('value') ?? [],
      "#options" => $this->getOptions(),
    ];
    $this->addRequired($form['value']);
    return $form;
  }

  /**
   * Get checkboxes options.
   */
  protected function getOptions(): array {
    $options = array_combine($this->propDefinition['items']['enum'], $this->propDefinition['items']['enum']);
    foreach ($options as $key => $label) {
      if (is_string($label)) {
        $options[$key] = ucwords($label);
      }
    }
    if (!isset($this->propDefinition['items']['meta:enum'])) {
      return $options;
    }
    $meta = $this->propDefinition['items']['meta:enum'];
    // Remove meta:enum items not found in options.
    return array_intersect_key($meta, $options);
  }

}
