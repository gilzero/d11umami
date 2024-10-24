<?php

declare(strict_types=1);

namespace Drupal\ui_patterns\Plugin\UiPatterns\Source;

use Drupal\Component\Plugin\Exception\ContextException;
use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\Context\ContextDefinition;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\Render\Markup;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ui_patterns\Attribute\Source;
use Drupal\ui_patterns\SourcePluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the source.
 *
 * Slot is explicitly added to prop_types to allow getPropValue
 * to return a renderable array in case of slot prop type.
 */
#[Source(
  id: 'token',
  label: new TranslatableMarkup('Token'),
  description: new TranslatableMarkup('Text with placeholder variables, replaced before display.'),
  prop_types: ['slot', 'string'],
  tags: [],
  context_definitions: [
    'entity' => new ContextDefinition('entity', label: new TranslatableMarkup('Entity'), required: FALSE),
  ]
)]
class TokenSource extends SourcePluginBase {

  /**
   * The token manager.
   *
   * @var \Drupal\Core\Utility\Token
   */
  protected $tokenManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $plugin = parent::create(
      $container,
      $configuration,
      $plugin_id,
      $plugin_definition
    );
    $plugin->tokenManager = $container->get('token');
    return $plugin;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultSettings(): array {
    return [
      'value' => "",
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getPropValue(): mixed {
    $value = $this->getSetting('value') ?? "";
    $isSlot = ($this->propDefinition["ui_patterns"]["type_definition"]->getPluginId() === "slot");
    if (empty($value) || !is_scalar($value)) {
      return $isSlot ? [] : "";
    }
    if ($isSlot) {
      $bubbleable_metadata = new BubbleableMetadata();
      $build = [
        "#markup" => Markup::create($this->tokenManager->replace((string) $value, $this->getTokenData(), ['clear' => TRUE], $bubbleable_metadata)),
      ];
      $bubbleable_metadata->applyTo($build);
      return $build;
    }
    return Html::escape($this->tokenManager->replacePlain((string) $value, $this->getTokenData(), ['clear' => TRUE]));
  }

  /**
   * Return the token type for the given entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   *
   * @return string
   *   The token type.
   */
  protected function getTokenTypeForEntity(EntityInterface $entity): string {
    $entity_type_id = $entity->getEntityTypeId();
    // Use token module service when available.
    if ($this->moduleHandler->moduleExists('token')) {
      // @phpstan-ignore-next-line
      return \Drupal::service('token.entity_mapper')->getTokenTypeForEntityType($entity_type_id);
    }
    // Emulate token module service.
    return str_starts_with($entity_type_id, 'taxonomy_') ? str_replace('taxonomy_', '', $entity_type_id) : $entity_type_id;
  }

  /**
   * Get token data.
   */
  protected function getTokenData(): array {
    try {
      $entity = $this->getContextValue('entity');
      if ($entity instanceof EntityInterface) {
        return [
          $this->getTokenTypeForEntity($entity) => $entity,
        ];
      }
    }
    catch (ContextException) {
    }
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $form = parent::settingsForm($form, $form_state);
    $form['value'] = [
      '#type' => 'textfield',
      '#default_value' => $this->getSetting('value'),
      // Tokens always start with a [ and end with a ].
      // '#pattern' => '^\[.+\]$',.
    ];
    $this->addRequired($form['value']);
    if ($this->moduleHandler->moduleExists('token')) {
      $form['help'] = [
        '#theme' => 'token_tree_link',
        '#token_types' => array_keys($this->getTokenData()),
      ];
    }
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() : array {
    $dependencies = parent::calculateDependencies();
    if ($this->moduleHandler->moduleExists('token')) {
      static::mergeConfigDependencies($dependencies, ["module" => ["token"]]);
    }
    return $dependencies;
  }

}
