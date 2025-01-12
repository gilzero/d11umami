<?php

namespace Drupal\ai_automators\Plugin\AiAutomatorType;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ai_automators\Attribute\AiAutomatorType;
use Drupal\ai_automators\PluginBaseClasses\SimpleTextChat;
use Drupal\ai_automators\PluginInterfaces\AiAutomatorTypeInterface;

/**
 * The rules for a text_with_summary field.
 */
#[AiAutomatorType(
  id: 'llm_simple_text_with_summary',
  label: new TranslatableMarkup('LLM: Text (simple)'),
  field_rule: 'text_with_summary',
  target: '',
)]
class LlmSimpleTextWithSummary extends SimpleTextChat implements AiAutomatorTypeInterface {

  /**
   * {@inheritDoc}
   */
  public $title = 'LLM: Text (simple)';

}
