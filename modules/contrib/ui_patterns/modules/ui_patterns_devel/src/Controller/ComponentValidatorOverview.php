<?php

declare(strict_types=1);

namespace Drupal\ui_patterns_devel\Controller;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Component\Render\MarkupInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\AutowireTrait;
use Drupal\Core\Link;
use Drupal\Core\Logger\RfcLogLevel;
use Drupal\Core\Plugin\Component;
use Drupal\Core\Render\RendererInterface;
use Drupal\ui_patterns\ComponentPluginManager;
use Drupal\ui_patterns_devel\Validator;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Yaml\Yaml;

/**
 * Returns responses for UI Patterns Devel routes.
 *
 * @codeCoverageIgnore
 */
final class ComponentValidatorOverview extends ControllerBase {

  use AutowireTrait;

  /**
   * Number of messages by type for summary.
   */
  private array $countMessages = [];

  /**
   * Track component with critical error to avoid rendering.
   */
  private array $componentHasCritical = [];

  /**
   * Current severity to show.
   */
  private array $levels = [];

  /**
   * Flag if current component is viewed on a single page.
   */
  private bool $singleDisplay = FALSE;

  /**
   * The controller constructor.
   */
  public function __construct(
    #[Autowire(service: 'ui_patterns_devel.validator')]
    private readonly Validator $validator,
    #[Autowire(service: 'plugin.manager.sdc')]
    private readonly ComponentPluginManager $componentPluginManager,
    private readonly RendererInterface $renderer,
  ) {}

  /**
   * Builds the response.
   *
   * @return array|RedirectResponse
   *   The built response array.
   */
  public function overviewDetails(): array|RedirectResponse {

    $build = [];
    $components = $this->componentPluginManager->getAllComponents();

    foreach ($components as $component) {
      $overview = $this->overviewComponent($component);
      if (empty($overview)) {
        continue;
      }

      $component_id = $component->getPluginId();
      $build[$component_id] = $overview;
      $link = Link::createFromRoute($component_id, 'ui_patterns_devel.twig_validator.component', ['component_id' => $component_id]);
      $build[$component_id]['details']['#description'] = $this->t('Single page report for @link', ['@link' => $link->toString()]);
    }

    if (empty($build)) {
      $build['content'] = [
        '#markup' => $this->t('No component with this level of message.'),
      ];
    }

    $build['summary'] = $this->buildMessagesSummary();

    return $build;
  }

  /**
   * List all components messages in a flat list.
   *
   * @return array
   *   The built response array.
   */
  public function overview(): array {

    $components = $this->componentPluginManager->getAllComponents();

    $build = [];
    $rows = [];

    foreach ($components as $component) {
      $component_id = $component->getPluginId();
      $messages = $this->getComponentMessages($component_id, $component);
      $rows = \array_merge($rows, $this->buildOnMessageRows($messages, $component_id, TRUE));
    }

    $build['summary'] = $this->buildMessagesSummary();

    $empty = $this->t('No messages found.');
    if (!empty($this->levels) && count(array_filter($this->levels)) < 4) {
      $empty = $this->t('No messages found for this severity, check filter in this form.');
    }

    $header = [
      $this->t('Component'),
      $this->t('Severity'),
      $this->t('Message'),
      $this->t('Type'),
      $this->t('Line'),
      $this->t('Source'),
    ];
    $build['table'] = [
      '#theme' => 'table',
      '#responsive' => TRUE,
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => $empty,
    ];

    return $build;
  }

  /**
   * Builds the title for a single component.
   *
   * @param string $component_id
   *   The required component id.
   *
   * @return \Drupal\Component\Render\MarkupInterface
   *   The title.
   */
  public function overviewSingleTitle(string $component_id): MarkupInterface {
    return $this->t('Component report %component_id', ['%component_id' => $component_id]);
  }

  /**
   * Builds the response for a single component.
   *
   * @param string $component_id
   *   The required component id.
   *
   * @return array|RedirectResponse
   *   The built response array.
   */
  public function overviewSingle(string $component_id): array|RedirectResponse {

    if (!$this->componentPluginManager->hasDefinition($component_id)) {
      $this->messenger()->addError($this->t('Unknown component: @cid', ['@cid' => $component_id]));
      return $this->redirect('ui_patterns_devel.twig_validator');
    }

    $component = $this->componentPluginManager->createInstance($component_id);

    $this->singleDisplay = TRUE;
    return $this->overviewComponent($component);
  }

  /**
   * Builds the response for the component overview.
   *
   * @param \Drupal\Core\Plugin\Component $component
   *   The component to generate the overview for.
   *
   * @return array
   *   The built response array.
   */
  private function overviewComponent(Component $component): array {
    $component_id = $component->getPluginId();
    $messages = $this->getComponentMessages($component_id, $component);

    if (empty($messages)) {
      return $this->buildOnSuccess($component_id, $component);
    }

    $build = $this->buildOnMessage($component_id, $component, $messages);

    return $build;
  }

  /**
   * Validate Component and get messages.
   *
   * @param string $component_id
   *   The required component id.
   * @param \Drupal\Core\Plugin\Component $component
   *   The component to generate the overview for.
   *
   * @return \Drupal\ui_patterns_devel\ValidatorMessage[]
   *   The response array of messages.
   */
  private function getComponentMessages(string $component_id, Component $component): array {
    $this->validator->validate($component_id, $component);
    $messages = $this->validator->getMessagesSortedByGroupAndLine();
    $this->validator->resetMessages();

    return $messages;
  }

  /**
   * Builds the render for a component with messages.
   *
   * @param string $component_id
   *   The required component id.
   * @param \Drupal\Core\Plugin\Component $component
   *   The component to generate the overview for.
   * @param \Drupal\ui_patterns_devel\ValidatorMessage[] $messages
   *   The component messages.
   *
   * @return array
   *   The response build array.
   */
  private function buildOnMessage(string $component_id, Component $component, array $messages): array {
    $build = [];

    $rows = $this->buildOnMessageRows($messages, $component_id);

    if ($this->singleDisplay) {
      $build['summary'] = $this->buildMessagesSummary();
    }
    else {
      $build['details'] = [
        '#type' => 'details',
        '#title' => '<span class="color-error">&#10005; ' . $component_id . '</span>',
        '#open' => TRUE,
      ];
    }

    $empty = $this->t('No messages found.');
    if (!empty($this->levels) && count(array_filter($this->levels)) < 4) {
      $empty = $this->t('No messages found for this severity, check filter in this form.');
    }

    $header = [
      $this->t('Severity'),
      $this->t('Message'),
      $this->t('Type'),
      $this->t('Line'),
      $this->t('Source'),
    ];
    $build['details']['table'] = [
      '#theme' => 'table',
      '#responsive' => TRUE,
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => $empty,
      '#weight' => -1,
    ];

    $build['details']['source'] = $this->buildSource($component);
    $build['details']['preview'] = $this->buildPreview($component_id, $component);

    return $build;
  }

  /**
   * Builds the table rows for a component with messages.
   *
   * @param \Drupal\ui_patterns_devel\ValidatorMessage[] $messages
   *   The component messages.
   * @param string $component_id
   *   If included will add the component id as first column.
   * @param bool $with_name
   *   Flag to add the component id as first column.
   *
   * @return array
   *   The rows to be used in a table.
   */
  private function buildOnMessageRows(array $messages, string $component_id, bool $with_name = FALSE): array {
    $levels = RfcLogLevel::getLevels();

    $default = [RfcLogLevel::WARNING, RfcLogLevel::ERROR, RfcLogLevel::CRITICAL, RfcLogLevel::NOTICE];
    // @phpstan-ignore-next-line
    $this->levels = $this->state()->get('ui_patterns_devel_overview_severity', $default);
    $rows = [];

    foreach ($messages as $message) {
      $level = $levels[$message->level()] ?? 'Unknown';
      if ($message->level() < RfcLogLevel::CRITICAL) {
        $level = new FormattableMarkup('<strong>@level</strong>', ['@level' => $level]);
      }
      // Summary messages.
      if (!empty($level)) {
        $this->countMessages[$message->level()] = ($this->countMessages[$message->level()] ?? 0) + 1;
      }

      if ($message->level() <= RfcLogLevel::CRITICAL) {
        $this->componentHasCritical[$component_id] = TRUE;
      }

      if (!in_array($message->level(), $this->levels)) {
        continue;
      }

      $line = (0 === $message->line()) ? '-' : $message->line();
      $source = new FormattableMarkup('<pre><code>@code</code></pre>', ['@code' => $message->getSourceCode()]);
      $data = [];
      if ($with_name) {
        $data = [
          Link::createFromRoute($component_id, 'ui_patterns_devel.twig_validator.component', ['component_id' => $component_id]),
          $level,
          $message->messageWithTip(),
          $message->getType(),
          $line,
          $source,
        ];
      }
      else {
        $data = [
          $level,
          $message->messageWithTip(),
          $message->getType(),
          $line,
          $source,
        ];
      }

      $color = ($message->level() < RfcLogLevel::ERROR) ? 'error' : \strtolower((string) $level);
      $rows[] = [
        'data' => $data,
        'class' => ['color-' . $color],
      ];
    }

    return $rows;
  }

  /**
   * Builds the render for a component without messages.
   *
   * @param string $component_id
   *   The required component id.
   * @param \Drupal\Core\Plugin\Component $component
   *   The component to generate the overview for.
   *
   * @return array
   *   The response build array.
   */
  private function buildOnSuccess(string $component_id, Component $component): array {
    $build = [];

    $build['details'] = [
      '#type' => 'details',
      '#title' => '<span class="color-success">&check; ' . $component_id . '</span>',
      '#open' => FALSE,
    ];

    $build['details']['source'] = $this->buildSource($component);

    return $build;
  }

  /**
   * Builds the messages summary.
   *
   * @return array
   *   The built response array containing summary.
   */
  private function buildMessagesSummary(): array {
    // @phpstan-ignore-next-line
    $form = $this->formBuilder()->getForm('Drupal\ui_patterns_devel\\Form\\OverviewForm', $this->countMessages);

    $warning = $this->countMessages[RfcLogLevel::WARNING] ?? 0;
    $error = $this->countMessages[RfcLogLevel::ERROR] ?? 0;
    $critical = $this->countMessages[RfcLogLevel::CRITICAL] ?? 0;
    $notice = $this->countMessages[RfcLogLevel::NOTICE] ?? 0;
    if ($notice > 0) {
      $notice = $this->formatPlural($notice, 'Total of @count notice.', 'Total of @count notices.');
    }
    else {
      $notice = $this->t('No notice.');
    }

    $open = FALSE;
    if (!empty($this->levels) && count(array_filter($this->levels)) < 4) {
      $open = TRUE;
    }

    return [
      'report' => [
        '#theme' => 'status_report_page',
        '#counters' => [
        [
          '#theme' => 'status_report_errors',
          '#amount' => $warning,
          '#text' => $this->formatPlural($warning, 'Warning', 'Warnings'),
          '#severity' => (0 === $warning) ? 'checked' : 'warning',
        ],
        [
          '#theme' => 'status_report_errors',
          '#amount' => $error,
          '#text' => $this->formatPlural($error, 'Error', 'Errors'),
          '#severity' => (0 === $error) ? 'checked' : 'error',
        ],
        [
          '#theme' => 'status_report_errors',
          '#amount' => $critical,
          '#text' => $this->formatPlural($critical, 'Critical', 'Criticals'),
          '#severity' => (0 === $critical) ? 'checked' : 'error',
        ],
        ],
      ],
      'notices' => [
        '#markup' => $notice,
      ],
      'form' => [
        '#type' => 'details',
        '#title' => $this->t('Filter by severity'),
        '#open' => $open,
        'form' => $form,
      ],
      '#weight' => -1,
    ];
  }

  /**
   * Builds the component source files.
   *
   * @param \Drupal\Core\Plugin\Component $component
   *   The component to generate the overview for.
   *
   * @return array
   *   The built response array containing preview.
   */
  private function buildSource(Component $component): array {
    $definition = (array) $component->getPluginDefinition();
    $build = [];

    $schema = '';
    if (isset($definition['_discovered_file_path'])) {
      $schema = \file_get_contents($definition['_discovered_file_path']);
      if ($schema) {
        $schema = \htmlentities((string) $schema);
      }
    }

    $build['schema'] = [
      '#type' => 'details',
      '#title' => $this->t('Definition YAML'),
      '#description' => $definition['_discovered_file_path'] ?? '',
      '#open' => FALSE,
      '#weight' => 2,
      ['#markup' => '<pre><code>' . $schema . '</code></pre>'],
    ];

    $build['schema_full'] = [
      '#type' => 'details',
      '#title' => $this->t('Definition discovered'),
      '#description' => $this->t('Definition processed by the code before rendering.'),
      '#open' => FALSE,
      '#weight' => 3,
      ['#markup' => '<pre><code>' . \htmlentities((string) Yaml::dump($component->getPluginDefinition(), 10, 2)) . '</code></pre>'],
    ];

    $build['source'] = [
      '#type' => 'details',
      '#title' => $this->t('Twig source'),
      '#description' => $component->getTemplatePath(),
      '#open' => FALSE,
      '#weight' => 4,
      ['#markup' => '<pre><code>' . \htmlentities((string) \file_get_contents($component->getTemplatePath() ?? '')) . '</code></pre>'],
    ];

    return $build;
  }

  /**
   * Builds the component preview based on stories.
   *
   * @param string $component_id
   *   The component id.
   * @param \Drupal\Core\Plugin\Component $component
   *   The component to generate the overview for.
   *
   * @return array
   *   The built response array containing preview.
   */
  private function buildPreview(string $component_id, Component $component): array {
    $build = [];

    $definition = $component->getPluginDefinition();
    // @phpstan-ignore-next-line
    if (!isset($definition['stories']) && empty($definition['stories'])) {
      return ['#markup' => $this->t('<i>Note</i>: No preview available for this component, add <code>stories</code> value to this component definition YAML to enable preview.')];
    }

    if (isset($this->componentHasCritical[$component_id])) {
      return ['#markup' => $this->t('<b>Error</b>: No render available as there is a Runtime error with this component, see critical above!')];
    }

    foreach (\array_keys($definition['stories']) as $story_id) {
      $build[$story_id] = ['#weight' => 4];
      $element = [
        '#type' => 'component',
        '#component' => $component_id,
        '#story' => $story_id,
        '#attributes' => [],
      ];
      $build[$story_id]['render'] = [
        '#type' => 'details',
        '#title' => $this->t('Render: @story', ['@story' => $story_id]),
        '#open' => FALSE,
        [$element],
      ];

      $markup = $this->renderer->renderInIsolation($element);

      $build[$story_id]['html'] = [
        '#type' => 'details',
        '#title' => $this->t('HTML rendered: @story', ['@story' => $story_id]),
        '#open' => FALSE,
        ['#markup' => '<pre><code>' . \htmlentities((string) $markup) . '</code></pre>'],
      ];
    }

    return $build;
  }

}
