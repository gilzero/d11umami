<?php

declare(strict_types=1);

namespace Drupal\ui_patterns\Plugin\UiPatterns\Source;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\Context\ContextDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ui_patterns\Attribute\Source;

/**
 * Plugin implementation of the source.
 */
#[Source(
  id: 'entity_field',
  label: new TranslatableMarkup('Data from a field'),
  description: new TranslatableMarkup('Data from a field'),
  context_definitions: [
    'entity' => new ContextDefinition('entity', label: new TranslatableMarkup('Entity'), required: TRUE),
  ]
)]
class EntityFieldSource extends DerivableContextSourceBase {

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $form = parent::settingsForm($form, $form_state);
    $form["derivable_context"]["#title"] = $this->t("Field");
    // When no derivable contexts exist, allow this form still be valid.
    if (isset($form["derivable_context"]["#options"]) && empty($form["derivable_context"]["#options"])) {
      $form["derivable_context"]["#required"] = FALSE;
    }
    return $form;
  }

  /**
   * {@inheritDoc}
   */
  protected function getSourcesTagFilter(): array {
    return [
      "widget:dismissible" => FALSE,
      "widget" => FALSE,
      "field" => TRUE,
    ];
  }

}
