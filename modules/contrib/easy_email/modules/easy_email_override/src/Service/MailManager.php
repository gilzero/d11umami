<?php

namespace Drupal\easy_email_override\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Render\RenderContext;
use Drupal\Core\Render\RendererInterface;
use Traversable;

/**
 * Class MailManager.
 *
 * Decorates the MailManager::mail method to apply Easy Email overrides.
 *
 * @package Drupal\easy_email
 */
class MailManager extends DefaultPluginManager implements MailManagerInterface {

  /**
   * Decorated service object.
   *
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  protected $decorated;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * Constructs the EmailManager object.
   *
   * @param \Drupal\Core\Mail\MailManagerInterface $decorated
   * @param \Traversable $namespaces
   * @param ModuleHandlerInterface $module_handler
   * @param \Drupal\Core\Render\RendererInterface $renderer
   */
  public function __construct(MailManagerInterface $decorated, \Traversable $namespaces, ModuleHandlerInterface $module_handler, RendererInterface $renderer, EntityTypeManagerInterface $entityTypeManager) {
    parent::__construct('Plugin/Mail', $namespaces, $module_handler, 'Drupal\Core\Mail\MailInterface', 'Drupal\Core\Annotation\Mail');
    $this->decorated = $decorated;
    $this->renderer = $renderer;
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * @inheritDoc
   */
  public function getInstance(array $options) {
    return $this->decorated->getInstance($options);
  }

  /**
   * @inheritDoc
   */
  public function mail($module, $key, $to, $langcode, $params = [], $reply = NULL, $send = TRUE) {
    $email_handler = \Drupal::service('easy_email.handler');
    /** @var \Drupal\easy_email_override\Entity\EmailOverrideInterface[] $email_overrides */
    $email_overrides = \Drupal::entityTypeManager()
      ->getStorage('easy_email_override')
      ->loadByProperties([
        'module' => $module,
        'key' => $key,
      ]);
    if (!empty($email_overrides)) {
      // If we find more than one override for a given module/key combo, we'll send them all.
      // Not sure if that will be useful, but perhaps.
      foreach ($email_overrides as $email_override) {
        $email_template = $email_override->getEasyEmailType();
        $email = $email_handler->createEmail([
          'type' => $email_template,
        ]);
        $param_map = $email_override->getParamMap();
        foreach ($param_map as $pm) {
          $email->set($pm['destination'], $params[$pm['source']]);
        }

        $result = $this->renderer->executeInRenderContext(new RenderContext(), function () use ($email_handler, $email_template, $email) {
          $sent_emails = $email_handler->sendEmail($email);
          /** @var \Drupal\easy_email\Entity\EasyEmailTypeInterface $easy_email_type */
          $easy_email_type = $this->entityTypeManager->getStorage('easy_email_type')->load($email_template);
          if ($easy_email_type->getSaveEmail()) {
            $email->save();
          }
          $first_sent_email = reset($sent_emails);
          return $first_sent_email->isSent();
        });

        $send = FALSE;
      }
    }
    $message = $this->decorated->mail($module, $key, $to, $langcode, $params, $reply, $send);
    if (!isset($message['result']) && !empty($email_overrides)) {
      $message['result'] = TRUE;
    }

    return $message;
  }

}
