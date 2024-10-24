<?php

namespace Drupal\ai_ckeditor\PluginInterfaces;

use Drupal\Component\Plugin\ConfigurableInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\PluginFormInterface;

/**
 * Provides an interface for implementing ai_ckeditor plugins.
 */
interface AiCKEditorPluginInterface extends PluginFormInterface, ConfigurableInterface {

  /**
   * Returns the translated plugin label.
   *
   * @return string
   *   The translated title.
   */
  public function label();

  /**
   * Returns the translated plugin description.
   *
   * @return string
   *   The translated description.
   */
  public function description();

  /**
   * Returns the built form for CKEditor.
   *
   * @param array $form
   *   The form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @return array
   *   The form array.
   */
  public function buildCkEditorModalForm(array $form, FormStateInterface $form_state);

  /**
   * Validates the form for CKEditor.
   *
   * @param array $form
   *   The form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @return array
   *   The form array.
   */
  public function validateCkEditorModalForm(array $form, FormStateInterface $form_state);

  /**
   * Submits the form for CKEditor.
   *
   * @param array $form
   *   The form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @return array
   *   The form array.
   */
  public function submitCkEditorModalForm(array $form, FormStateInterface $form_state);

}
