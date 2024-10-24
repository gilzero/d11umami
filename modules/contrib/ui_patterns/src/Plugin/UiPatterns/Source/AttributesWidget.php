<?php

declare(strict_types=1);

namespace Drupal\ui_patterns\Plugin\UiPatterns\Source;

use Drupal\Component\Utility\Html;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ui_patterns\Attribute\Source;
use Drupal\ui_patterns\SourcePluginBase;

/**
 * Plugin implementation of the source.
 */
#[Source(
  id: 'attributes',
  label: new TranslatableMarkup('Attributes'),
  description: new TranslatableMarkup('...'),
  prop_types: ['attributes']
)]
class AttributesWidget extends SourcePluginBase {

  /**
   * {@inheritdoc}
   */
  public function getPropValue(): mixed {
    // In UI Patterns Settings, we built the Attribute object here. It is not
    // possible anymore because SDC will not validate it against the prop
    // type schema.
    $value = $this->getSetting('value');
    if (!is_string($value)) {
      return [];
    }
    return $this->convertStringToAttributesMapping($value);
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $form = parent::settingsForm($form, $form_state);
    // Attributes are associative arrays, but this source plugin is storing
    // them as string in config.
    // It would be better to use something else than a textfield one day.
    $form['value'] = [
      '#type' => 'textfield',
      '#default_value' => $this->getSetting('value'),
    ];
    $form['value']['#pattern'] = $this->buildRegexPattern();
    // To allow form errors to be displayed correctly.
    $form['value']['#title'] = '';
    $form['value']['#placeholder'] = 'class="hidden" title="Lorem ipsum"';
    $form['value']['#description'] = $this->t("HTML attributes with double-quoted values.");
    $this->addRequired($form['value']);
    return $form;
  }

  /**
   * Build regular expression pattern.
   *
   * See https://html.spec.whatwg.org/#attributes-2
   */
  protected function buildRegexPattern(): string {
    // Attribute names are a mix of ASCII lower and upper alphas.
    $attr_name = "[a-zA-Z\-]+";
    // Discard double quotes which are used for delimiting.
    $double_quoted_value = '[^"]*';
    $space = "\s*";
    $attr = sprintf("%s=\"%s\"%s", $attr_name, $double_quoted_value, $space);
    // Start and end delimiters are not expected here, they will be added:
    // - by \Drupal\Core\Render\Element\FormElementBase::validatePattern for
    //   server side validation
    // - in the HTML5 pattern attribute, for client side validation
    // https://developer.mozilla.org/en-US/docs/Web/HTML/Attributes/pattern
    return $space . "(" . $attr . ")*";
  }

  /**
   * Convert a string to an attribute mapping.
   */
  protected function convertStringToAttributesMapping(string $value): array {
    $parse_html = '<div ' . $value . '></div>';
    $attributes = [];
    foreach (Html::load($parse_html)->getElementsByTagName('div') as $div) {
      foreach ($div->attributes as $attr) {
        $attributes[$attr->nodeName] = $attr->nodeValue;
      }
    }
    return $attributes;
  }

}
