<?php

declare(strict_types=1);

namespace Drupal\ui_patterns_views;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\Context\Context;
use Drupal\Core\Plugin\Context\ContextDefinition;
use Drupal\Core\Plugin\Context\EntityContext;
use Drupal\ui_patterns\Form\ComponentSettingsFormBuilderTrait;
use Drupal\ui_patterns\Plugin\Context\RequirementsContext;
use Drupal\ui_patterns\SourcePluginBase;
use Drupal\views\ViewEntityInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Views plugin UI patterns trait.
 */
trait ViewsPluginUiPatternsTrait {

  use ComponentSettingsFormBuilderTrait;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * Initialize the trait.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The container.
   */
  public function initialize(ContainerInterface $container) : void {
    $this->entityTypeManager = $container->get('entity_type.manager');
    $this->moduleHandler = $container->get('module_handler');
  }

  /**
   * Get the source contexts for the component.
   *
   * @return array
   *   Source contexts.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function getComponentSourceContexts() : array {
    $context = [];
    $plugin_definition = $this->getPluginDefinition();
    if (is_array($plugin_definition) && isset($plugin_definition['plugin_type'])) {
      $context = RequirementsContext::addToContext(['views:' . $plugin_definition['plugin_type']], $context);
    }
    $view = $this->view;
    // Add view to context.
    $context['ui_patterns_views:view'] = new Context(new ContextDefinition('any'), $view);
    // Add plugin options to context.
    $context['ui_patterns_views:plugin:options'] = new Context(new ContextDefinition('any'), $this->options);
    // Add plugin options to context.
    $context['ui_patterns_views:plugin:display_handler'] = new Context(new ContextDefinition('any'), $this->displayHandler);
    // Build view entity context.
    $view_entity = $this->entityTypeManager->getStorage('view')->load($view->id());
    if ($view_entity instanceof ViewEntityInterface) {
      $context['entity'] = EntityContext::fromEntity($view_entity);
    }
    return $context;
  }

  /**
   * Add dependencies to the plugin.
   *
   * @param array<string, mixed> $dependencies
   *   Initial dependencies.
   *
   * @return array<string, mixed>
   *   The dependencies.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function addDependencies(array $dependencies) {
    $component_settings = $this->getComponentSettings();
    $component_id = $component_settings["ui_patterns"]["component_id"] ?? NULL;
    if (!$component_id) {
      return $dependencies;
    }
    $component_dependencies = $this->calculateComponentDependencies($component_id, $this->getComponentSourceContexts());
    SourcePluginBase::mergeConfigDependencies($dependencies, $component_dependencies);
    SourcePluginBase::mergeConfigDependencies($dependencies, ["module" => ["ui_patterns_views"]]);
    return $dependencies;
  }

  /**
   * Return the views ui ajax form url.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current form state.
   *
   * @return \Drupal\Core\Url|null
   *   The views ajax url.
   */
  protected function getViewsUiBuildFormUrl(FormStateInterface $form_state) {
    if ($this->moduleHandler->moduleExists('views_ui') && function_exists('views_ui_build_form_url')) {
      return views_ui_build_form_url($form_state);
    }
    return NULL;
  }

}
