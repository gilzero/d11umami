<?php

namespace Drupal\ai_automators\PluginBaseClasses;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Field\FieldDefinitionInterface;

/**
 * This is a base class that can be used for LLMs simple text chat/instructions.
 */
class SimpleTextChat extends RuleBase {

  /**
   * {@inheritDoc}
   */
  public function helpText() {
    return "This is a simple text to text model. It will give back the raw output.";
  }

  /**
   * {@inheritDoc}
   */
  public function generate(ContentEntityInterface $entity, FieldDefinitionInterface $fieldDefinition, array $automatorConfig) {
    // Generate the real prompt if needed.
    $prompts = parent::generate($entity, $fieldDefinition, $automatorConfig);

    $total = [];
    $instance = $this->prepareLlmInstance('chat', $automatorConfig);
    foreach ($prompts as $prompt) {
      $value[] = $this->runRawChatMessage($prompt, $automatorConfig, $instance)->getText();
      if (!empty($value[0])) {
        $total = array_merge_recursive($total, $value);
      }
    }
    return $total;
  }

  /**
   * {@inheritDoc}
   */
  public function storeValues(ContentEntityInterface $entity, array $values, FieldDefinitionInterface $fieldDefinition, array $automatorConfig) {
    $config = $fieldDefinition->getConfig($entity->bundle())->getSettings();
    if (!empty($config['max_length'])) {
      $values = array_map(function ($value) use ($config) {
        return substr($value, 0, $config['max_length']);
      }, $values);
    }
    $entity->set($fieldDefinition->getName(), $values);
  }

}
