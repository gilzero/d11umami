<?php

namespace Drupal\styled_google_map_demo\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Real estate edit forms.
 *
 * @ingroup styled_google_map_demo
 */
class RealEstateForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\styled_google_map_demo\Entity\RealEstate */
    $form = parent::buildForm($form, $form_state);

    $entity = $this->entity;

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;

    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Real estate.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Real estate.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.real_estate.canonical', ['real_estate' => $entity->id()]);
  }

}
