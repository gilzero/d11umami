<?php

declare(strict_types=1);

namespace Drupal\ui_patterns_views\Plugin\UiPatterns\Source;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\Context\EntityContextDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ui_patterns\Attribute\Source;
use Drupal\views\ViewExecutable;

/**
 * Plugin implementation of the source_provider.
 */
#[Source(
  id: 'view_rows',
  label: new TranslatableMarkup('View rows'),
  description: new TranslatableMarkup('View rows results.'),
  prop_types: ['slot'], tags: ['views'],
  context_requirements: ['views:style'],
  context_definitions: [
    'entity' => new EntityContextDefinition('entity:view', label: new TranslatableMarkup('View')),
  ]
)]
class ViewRowsSource extends ViewsSourceBase {

  /**
   * {@inheritdoc}
   */
  public function getPropValue(): mixed {
    $view = $this->getView();
    $rows = $this->getContextValue("ui_patterns_views:rows");
    if (!$view || !is_array($rows) || count($rows) < 1) {
      return [];
    }
    // If a field is selected, render only that field in rows.
    $field_name = $this->getSetting('ui_patterns_views_field') ?? "";
    $field_options = $this->getViewsFieldOptions($view);
    if ($field_name && isset($field_options[$field_name])) {
      return $this->renderRowsWithField($view, $rows, $field_name);
    }
    // Return the view rows.
    return $this->renderOutput($rows);

  }

  /**
   * Render rows with a specific field.
   *
   * @param \Drupal\views\ViewExecutable $view
   *   The view.
   * @param array $rows
   *   The rows.
   * @param string $field_name
   *   The field name.
   *
   * @return array
   *   The rendered rows.
   *
   * @throws \Drupal\Component\Plugin\Exception\ContextException
   */
  protected function renderRowsWithField(ViewExecutable $view, array $rows, string $field_name): array {
    $returned = [];
    $view_style_plugin = $view->style_plugin;
    if ($view_style_plugin) {
      foreach ($rows as $row_index => $row) {
        $index = isset($row["#row"], $row["#row"]->index) ? $row["#row"]->index : $row_index;
        $field_output = $view_style_plugin->getField($index, $field_name);
        if ($this->isViewFieldHidden($field_name, $field_output, $view)) {
          $field_output = NULL;
        }
        $returned[] = $this->renderOutput($field_output ?? []);
      }
    }
    return $returned;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $form = parent::settingsForm($form, $form_state);
    $field_options = $this->getViewsFieldOptions($this->getView());
    if (is_array($field_options)) {
      $form['ui_patterns_views_field'] = [
        '#type' => 'select',
        '#title' => $this->t('Fields rendered in rows'),
        '#description' => $this->t('Render only this field in the rows.'),
        '#options' => $field_options,
        '#default_value' => $this->getSetting('ui_patterns_views_field') ?? "",
        '#required' => FALSE,
        '#empty_option' => $this->t('All'),
      ];
    }
    return $form;
  }

}
