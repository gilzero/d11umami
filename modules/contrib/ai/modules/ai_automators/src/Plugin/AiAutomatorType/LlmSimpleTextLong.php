<?php

namespace Drupal\ai_automators\Plugin\AiAutomatorType;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ai_automators\Attribute\AiAutomatorType;
use Drupal\ai_automators\PluginBaseClasses\SimpleTextChat;
use Drupal\ai_automators\PluginInterfaces\AiAutomatorTypeInterface;

/**
 * The rules for a text_long field.
 */
#[AiAutomatorType(
  id: 'llm_simple_text_long',
  label: new TranslatableMarkup('LLM: Text (simple)'),
  field_rule: 'text_long',
  target: '',
)]
class LlmSimpleTextLong extends SimpleTextChat implements AiAutomatorTypeInterface {

  /**
   * {@inheritDoc}
   */
  public $title = 'LLM: Text (simple)';

}
