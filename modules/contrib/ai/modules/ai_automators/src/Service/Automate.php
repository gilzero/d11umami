<?php

namespace Drupal\ai_automators\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\ai_automators\Exceptions\AiAutomatorTypeNotFoundException;
use Drupal\ai_automators\Exceptions\AiAutomatorTypeNotRunnable;

/**
 * Automates anything using a disposable automator.
 */
class Automate {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * Get the automated fields for a bundle.
   *
   * @param string $type
   *   The type of the automator chain.
   *
   * @return array
   *   The fields that has automators on them.
   */
  public function getAutomatedFields(string $type) {
    $fields = $this->entityTypeManager->getStorage('ai_automator')->loadByProperties([
      'entity_type' => 'automator_chain',
      'bundle' => $type,
    ]);
    $output_fields = [];
    foreach ($fields as $field) {
      $output_fields[$field->get('field_name')] = $field->get('field_name');
    }
    return $output_fields;
  }

  /**
   * Run the automator chain.
   *
   * @param string $type
   *   The type of the automator chain.
   * @param mixed $inputs
   *   The inputs to the automator chain.
   *
   * @return array
   *   The output of the automator chain.
   */
  public function run(string $type, $inputs = []) {
    // Check so the type exists.
    try {
      /** @var \Drupal\ai_automators\Entity\AiAutomatorChainType */
      $automator_type = $this->entityTypeManager->getStorage('automator_chain_type')->load($type);
    }
    catch (\Exception $e) {
      throw new AiAutomatorTypeNotFoundException('Automator chain type does not exist.');
    }

    // Check so there is output fields.
    $output_fields = $this->getAutomatedFields($type);

    /** @var \Drupal\ai_automators\Entity\AutomatorChain */
    $automator = $this->entityTypeManager->getStorage('automator_chain')->create([
      'bundle' => $type,
    ]);
    // Set the inputs.
    foreach ($inputs as $field => $input) {
      $automator->set($field, $input);
    }
    // Try saving.
    try {
      $automator->save();
    }
    catch (\Exception $e) {
      throw new AiAutomatorTypeNotRunnable('Automator chain could not be saved:' . $e->getMessage());
    }

    // Return the values that has automators on them.
    $output = [];
    foreach ($output_fields as $field) {
      // Make sure to get the main value.
      $output[$field] = $automator->get($field)->getValue();
    }
    // Garbage collect.
    $automator->delete();
    return $output;
  }

}
