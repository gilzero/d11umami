<?php

namespace Drupal\provider_ollama;

use Drupal\ai\OperationType\Chat\StreamedChatMessage;
use Drupal\ai\OperationType\Chat\StreamedChatMessageIterator;

/**
 * Ollama Chat message iterator.
 */
class OllamaChatMessageIterator extends StreamedChatMessageIterator {

  /**
   * {@inheritdoc}
   */
  public function getIterator(): \Generator {
    foreach ($this->iterator->getIterator() as $data) {
      yield new StreamedChatMessage(
        $data->choices[0]->delta->role ?? '',
        $data->choices[0]->delta->content ?? '',
        $data->usage ?? []
      );
    }
  }

}
