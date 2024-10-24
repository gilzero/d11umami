<?php

namespace Drupal\ai_translate\Plugin\FieldTextExtractor;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Field\FieldConfigInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ai_translate\Attribute\FieldTextExtractor;
use Drupal\ai_translate\FieldTextExtractorInterface;

/**
 * A field text extractor plugin for text fields.
 */
#[FieldTextExtractor(
  id: "text",
  label: new TranslatableMarkup('Text'),
  field_types: [
    'title',
    'text',
    'text_with_summary',
    'text_long',
    'string',
    'string_long',
  ]
)]
class TextFieldExtractor implements FieldTextExtractorInterface {

  /**
   * {@inheritdoc}
   */
  public function extract(ContentEntityInterface $entity, string $fieldName): array {
    if ($entity->get($fieldName)->isEmpty()) {
      return [];
    }
    $textMeta = [];
    foreach ($entity->get($fieldName) as $delta => $fieldItem) {
      $textMeta[] = ['delta' => $delta] + $fieldItem->getValue();
    }
    return $textMeta;
  }

  /**
   * {@inheritdoc}
   */
  public function setValue(
    ContentEntityInterface $entity,
    string $fieldName,
    array $value,
  ) : void {
    $newValue = [];
    foreach ($value as $delta => $singleValue) {
      unset($singleValue['field_name'], $singleValue['field_type']);
      $newValue[$delta] = $singleValue;
    }
    $entity->set($fieldName, $newValue);
  }

  /**
   * {@inheritDoc}
   */
  public function shouldExtract(ContentEntityInterface $entity, FieldConfigInterface $fieldDefinition): bool {
    return $fieldDefinition->isTranslatable();
  }

}
