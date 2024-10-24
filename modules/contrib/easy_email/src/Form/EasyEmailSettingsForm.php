<?php

namespace Drupal\easy_email\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configuration form for Easy Email global settings.
 */
class EasyEmailSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'easy_email_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'easy_email.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    $config = $this->config('easy_email.settings');

    $form['#tree'] = TRUE;

    $form['purge'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Automatic Email Deletion'),
    ];

    $form['purge']['cron'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Delete emails automatically on cron run'),
      '#default_value' => $config->get('purge_on_cron'),
      '#description' => $this->t('Emails will be automatically deleted based on the settings for each template.
        This checkbox determines whether that happens on cron run or whether you will run the included Drush command separately.'),
    ];

    $form['purge']['cron_limit'] = [
      '#type' => 'number',
      '#title' => $this->t('Maximum number of emails to delete per cron run'),
      '#default_value' => $config->get('purge_cron_limit'),
      '#states' => [
        'required' => [
          ':input[name="purge[cron]"]' => ['checked' => TRUE],
        ],
      ]
    ];

    $form['attachments'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Attachments'),
    ];

    $allowed_paths = $config->get('allowed_attachment_paths');
    if (is_array($allowed_paths)) {
      $allowed_paths = implode("\n", $allowed_paths);
    }
    $form['attachments']['allowed_paths'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Allowed Attachment Paths'),
      '#description' => $this->t('Paths to files that are allowed to be attached to emails. One path per line. Use * as a wildcard. Example: public://*.txt'),
      '#default_value' => $allowed_paths,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('easy_email.settings');
    $config->set('purge_on_cron', $form_state->getValue(['purge', 'cron']));
    $config->set('purge_cron_limit', $form_state->getValue(['purge', 'cron_limit']));
    $allowed_attachment_paths = preg_split('/\r\n|[\r\n]/', $form_state->getValue(['attachments', 'allowed_paths']));
    $config->set('allowed_attachment_paths', $allowed_attachment_paths);
    $config->save();
    parent::submitForm($form, $form_state);
  }

}
