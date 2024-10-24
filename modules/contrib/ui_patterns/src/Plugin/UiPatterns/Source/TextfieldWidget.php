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
  id: 'textfield',
  label: new TranslatableMarkup('Textfield'),
  description: new TranslatableMarkup('One-line text field.'),
  prop_types: ['string', 'machine_name'],
  tags: ['widget', 'widget:dismissible']
)]
class TextfieldWidget extends SourcePluginPropValue {

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $form = parent::settingsForm($form, $form_state);
    $form['value'] = [
      '#type' => 'textfield',
      '#default_value' => $this->getSetting('value'),
    ];
    $this->addRequired($form['value']);
    $description = [];
    if (isset($this->propDefinition["pattern"])) {
      $form['value']['#pattern'] = $this->propDefinition["pattern"];
      $description[] = $this->t("Constraint: @pattern", ["@pattern" => $this->propDefinition["pattern"]]);
    }
    if (isset($this->propDefinition["maxLength"])) {
      $form['value']['#maxlength'] = $this->propDefinition["maxLength"];
      $form['value']['#size'] = $this->propDefinition["maxLength"];
      $description[] = $this->t("Max length: @length", ["@length" => $this->propDefinition["maxLength"]]);
    }
    if (!isset($this->propDefinition["pattern"]) && isset($this->propDefinition["minLength"])) {
      $form['value']['#pattern'] = "^.{" . $this->propDefinition["minLength"] . ",}$";
      $description[] = $this->t("Min length: @length", ["@length" => $this->propDefinition["minLength"]]);
    }
    $form['value']["#description"] = implode("; ", $description);
    return $form;
  }

}
