<?php

declare(strict_types=1);

namespace Drupal\ui_patterns_field_formatters\Plugin\UiPatterns\Source;

use Drupal\Component\Plugin\Definition\PluginDefinitionInterface;
use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Field\FieldTypePluginManagerInterface;
use Drupal\Core\Field\FormatterInterface;
use Drupal\Core\Field\FormatterPluginManager;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\SubformState;
use Drupal\Core\Logger\LoggerChannelTrait;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\Exception\MissingDataException;
use Drupal\ui_patterns\Attribute\Source;
use Drupal\ui_patterns\Plugin\UiPatterns\Source\FieldValueSourceBase;
use Drupal\ui_patterns\SourcePluginBase;
use Drupal\ui_patterns_field_formatters\Plugin\Derivative\FieldFormatterSourceDeriver;
use Drupal\ui_patterns_field_formatters\Plugin\Field\FieldFormatter\ComponentFormatterBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Source plugin for field formatter.
 */
#[Source(
  id: 'field_formatter',
  label: new TranslatableMarkup('Field Formatter'),
  description: new TranslatableMarkup('Entity Field formatted with a field formatter'),
  deriver: FieldFormatterSourceDeriver::class
)]
class FieldFormatterSource extends FieldValueSourceBase {

  use LoggerChannelTrait;

  use FieldFormatterFormTrait;

  /**
   * The formatter plugin manager.
   *
   * @var \Drupal\Core\Field\FormatterPluginManager|null
   */
  protected ?FormatterPluginManager $formatterPluginManager;

  /**
   * The field type plugin manager.
   *
   * @var \Drupal\Core\Field\FieldTypePluginManagerInterface|null
   */
  protected ?FieldTypePluginManagerInterface $fieldTypePluginManager;

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition,
  ) {
    $instance = parent::create(
      $container,
      $configuration,
      $plugin_id,
      $plugin_definition
    );
    $instance->entityFieldManager = $container->get('entity_field.manager');
    $instance->entityTypeManager = $container->get('entity_type.manager');
    $instance->formatterPluginManager = $container->get('plugin.manager.field.formatter');
    $instance->fieldTypePluginManager = $container->get('plugin.manager.field.field_type');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $form = parent::settingsForm($form, $form_state);
    $this->buildFieldFormatterForm($form, $form_state);
    return $form;
  }

  /**
   * Callback to build field formatter form.
   *
   * @param array $form
   *   The source plugin settings form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Form state.
   *
   * @return bool
   *   True if the form was generated.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  private function buildFieldFormatterForm(array &$form, FormStateInterface $form_state) {
    $field_definition = $this->getFieldDefinition();
    if (!$field_definition instanceof FieldDefinitionInterface) {
      return FALSE;
    }
    $field_storage = $field_definition->getFieldStorageDefinition();
    $this->generateFieldFormatterForm($form, $form_state, $field_definition, $field_storage);
    return TRUE;
  }

  /**
   * Generate the form formatter field formatter.
   *
   * @param array $form
   *   The source plugin settings form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current form state.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The field definition.
   * @param \Drupal\Core\Field\FieldStorageDefinitionInterface $field_storage
   *   Field storage.
   *
   * @return bool
   *   False if can't generate.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  private function generateFieldFormatterForm(array &$form, FormStateInterface $form_state, FieldDefinitionInterface $field_definition, FieldStorageDefinitionInterface $field_storage): bool {
    $formatter_options = $this->getAvailableFormatterOptions($field_storage, $field_definition);

    if (empty($formatter_options)) {
      return FALSE;
    }
    // @todo remove ui patterns formatters from the list of options ?
    // Get the formatter type from configuration.
    $type_path = [
      'settings',
      'type',
    ];
    $formatter_type = $this->getSettingsFromConfiguration($type_path);
    $uniqueID = Html::getId(implode("_", $this->formArrayParents ?? []) . "_field-formatter-settings-ajax");
    // Get the formatter settings from configuration.
    $settings = $this->getSettingsFieldFormatter($form_state, $field_storage, $formatter_options, $formatter_type);
    $form['type'] = [
      '#type' => 'select',
      '#required' => TRUE,
      '#title' => $this->t('Formatter'),
      '#options' => $formatter_options,
      '#default_value' => $formatter_type,
      '#empty_option' => $this->t('- Select -'),
      // Note: We cannot use ::foo syntax, because the form is the entity form
      // display.
      '#ajax' => [
        'callback' => [__CLASS__, 'onFormatterTypeChange'],
        'wrapper' => $uniqueID,
        'method' => 'replaceWith',
      ],
    ];

    $options = [
      'field_definition' => $field_definition,
      'configuration' => [
        'type' => $formatter_type,
        'settings' => $settings,
        'label' => '',
        'weight' => 0,
      ],
      'view_mode' => '_custom',
    ];

    // Get the formatter settings form.
    $form['settings'] = [
      '#value' => [],
      '#attributes' => [
        'id' => $uniqueID,
      ],
    ];
    if ($formatter = $this->formatterPluginManager->getInstance($options)) {
      if ($formatter instanceof ComponentFormatterBase) {
        // The Source is giving its context to the field formatter.
        $formatter->setContext($this->context);
        $formatter->setFormArrayParents(array_merge($this->formArrayParents, ["settings"]));
      }
      $subform_state = SubformState::createForSubform($form["settings"], $form, $form_state);
      $form['settings'] = $formatter->settingsForm($form['settings'], $subform_state);
      $form['settings']['#prefix'] = '<div id="' . $uniqueID . '" style="padding: 20px; background: red;">' . ($form['settings']['#prefix'] ?? '');
      $form['settings']['#suffix'] = ($form['settings']['#suffix'] ?? '') . '</div>';
    }
    return TRUE;
  }

  /**
   * Get the formatter settings from configuration.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Form state.
   * @param \Drupal\Core\Field\FieldStorageDefinitionInterface $field_storage
   *   Field storage.
   * @param array $formatter_options
   *   Array of formatters options.
   * @param string|null $formatter_type
   *   The formatter name.
   *
   * @return array|mixed|null
   *   The field formatter settings.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  private function getSettingsFieldFormatter(FormStateInterface $form_state, FieldStorageDefinitionInterface $field_storage, array $formatter_options, ?string $formatter_type = '') {
    if (!empty($formatter_type)) {
      $settings_path = [
        'settings',
        'settings',
      ];
      $settings = $this->getSettingsFromConfiguration($settings_path);
    }
    // Get default formatter type.
    if (empty($formatter_type) || !isset($formatter_options[$formatter_type])) {
      $formatter_type = $this->fieldTypePluginManager->getDefinition($field_storage->getType())['default_formatter'] ?? key($formatter_options);
      $settings = $this->formatterPluginManager->getDefaultSettings($field_storage->getType());
    }
    // Reset settings if we change the formatter.
    $triggering_element = $form_state->getTriggeringElement();
    if (!empty($triggering_element) && $triggering_element['#value'] === $formatter_type) {
      $settings = $this->formatterPluginManager->getDefaultSettings($formatter_type);
    }
    if (empty($settings) && !empty($formatter_type)) {
      $settings = $this->formatterPluginManager->getDefaultSettings($formatter_type);
    }
    return $settings;
  }

  /**
   * Create an instance of field formatter.
   *
   * @param string $formatter_id
   *   The formatter id.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The field definition of field to apply formatter.
   *
   * @return \Drupal\Core\Field\FormatterInterface
   *   The field formatter plugin.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  private function createInstanceFormatter(string $formatter_id, FieldDefinitionInterface $field_definition) {
    // @todo Ensure it is right to empty all values here, see:
    // https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Field%21FormatterPluginManager.php/class/FormatterPluginManager/8.2.x
    $configuration = [
      'field_definition' => $field_definition,
      'settings' => [],
      'label' => '',
      'view_mode' => '',
      'third_party_settings' => [],
    ];
    /** @var \Drupal\Core\Field\FormatterInterface $instance */
    $instance = $this->formatterPluginManager->createInstance($formatter_id, $configuration);
    return $instance;
  }

  /**
   * Get all available formatters by loading available ones and filtering out.
   *
   * @param \Drupal\Core\Field\FieldStorageDefinitionInterface $field_storage_definition
   *   The field storage definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   Field definition.
   *
   * @return string[]
   *   The field formatter labels keys by plugin ID.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  protected function getAvailableFormatterOptions(FieldStorageDefinitionInterface $field_storage_definition, FieldDefinitionInterface $field_definition): array {
    $formatters = $this->formatterPluginManager->getOptions($field_storage_definition->getType());
    $formatter_instances = [];
    foreach ($formatters as $formatter_id => $formatter) {
      $formatter_instances[$formatter_id] = $this->createInstanceFormatter($formatter_id, $field_definition);
    }

    $filtered_formatter_instances = $this->filterFormatter($formatter_instances, $field_definition);
    $options = array_map(
      static function (FormatterInterface $formatter) {
        $plugin_definition = $formatter->getPluginDefinition();
        return ($plugin_definition instanceof PluginDefinitionInterface) ? $plugin_definition->id() : $plugin_definition["label"];
      }, $filtered_formatter_instances);

    // Remove field_link itself.
    if (array_key_exists('field_link', $options)) {
      unset($options['field_link']);
    }
    // $options = ["" => $this->t('- Select -')] + $options;
    return $options;
  }

  /**
   * Render field item(s) with the field formatter.
   *
   * @param \Drupal\Core\Field\FieldItemListInterface $items
   *   Items.
   * @param int|null $field_delta
   *   Field delta.
   *
   * @return array
   *   Render array
   */
  private function viewFieldItems(FieldItemListInterface $items, $field_delta = NULL): array {
    $returned = [];
    $configuration = $this->getConfiguration();
    if (empty($configuration['settings']['type'])) {
      // No formatter has been configured.
      return $returned;
    }
    // We use third_party_settings to propagate context to the formatter.
    // Only our formatter will know how to use it.
    $formatter_config = [
      'type' => $configuration['settings']['type'],
      'settings' => $configuration['settings']['settings'] ?? [],
      'third_party_settings' => [
        'ui_patterns' => [
          'context' => $this->context,
        ],
      ],
    ];
    if ($field_delta === NULL) {
      $rendered_field = $items->view($formatter_config);
      for ($field_index = 0; $field_index < $items->count(); $field_index++) {
        if (!isset($rendered_field[$field_index])) {
          continue;
        }
        $returned[] = $rendered_field[$field_index];
      }
      return $returned;
    }
    try {
      /** @var \Drupal\Core\Field\FieldItemInterface $item */
      $item = $items->get($field_delta);
      return ($item instanceof FieldItemInterface) ? [$item->view($formatter_config)] : [];
    }
    catch (MissingDataException) {
      return [];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getPropValue(): mixed {
    $items = $this->getEntityFieldItemList();
    if (!$items instanceof FieldItemListInterface) {
      return [];
    }
    $field_index = (isset($this->context['ui_patterns:field:index'])) ? $this->getContextValue('ui_patterns:field:index') : NULL;
    return $this->viewFieldItems($items, $field_index);
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() : array {
    $dependencies = parent::calculateDependencies();
    $configuration = $this->getConfiguration();
    if (empty($configuration['settings']['type'])) {
      return $dependencies;
    }
    $formatter = $this->createInstanceFormatter($configuration['settings']['type'], $this->getFieldDefinition());
    if (!$formatter) {
      return $dependencies;
    }
    SourcePluginBase::mergeConfigDependencies($dependencies, $this->getPluginDependencies($formatter));
    SourcePluginBase::mergeConfigDependencies($dependencies, ["module" => ["ui_patterns_field_formatters"]]);
    return $dependencies;
  }

}
