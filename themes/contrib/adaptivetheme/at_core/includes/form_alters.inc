<?php

/**
 * @file
 */

use Drupal\Core\Form\FormStateInterface;
use Drupal\at_core\Theme\ThemeConfig;
use Drupal\Component\Utility\Html;

/**
 * Implements hook_form_alter().
 *
 * @param $form
 * @param \Drupal\Core\Form\FormStateInterface $form_state
 * @param $form_id
 */
function at_core_form_alter(&$form, FormStateInterface $form_state, $form_id) {
  $theme = &drupal_static(__FUNCTION__);
  if (!isset($theme)) {
    $data = new ThemeConfig(\Drupal::theme()->getActiveTheme()->getName());
    $theme = $data->getConfig();
  }
  $config = $theme['config'];

  switch ($form_id) {
    case 'comment_comment_form':
      $form['#theme_wrappers']['form__node_comment'] = [
        '#attributes' => [
          'class' => [
            'comment-form',
            'comment-form--node',
          ]
        ],
      ];
      break;

    case 'user_login_form':
      if ($theme['extensions']['is_enabled'] === TRUE) {
        if (isset($config['enable_markup_overrides']) && $config['enable_markup_overrides'] === 1) {
          if (isset($config['login_block_placeholder_labels']) && $config['login_block_placeholder_labels'] === 1) {
            $form['name']['#attributes']['placeholder'] = $form['name']['#title'];
            $form['pass']['#attributes']['placeholder'] = $form['pass']['#title'];
            $form['name']['#title_display'] = 'invisible';
            $form['pass']['#title_display'] = 'invisible';
            $form['#attributes']['class'][] = 'has-placeholders';
          }
          if (isset($config['horizontal_login_block']) && $config['horizontal_login_block'] === 1) {
            $form['#attributes']['class'][] = 'is-horizontal-form';
          }
        }
      }
      break;

    case 'search_block_form':
      // Remove the size attribute.
      $form['keys']['#size'] = '';
      // Classes.
      $form['actions']['submit']['#attributes']['class'][] = 'search-form__submit';
      if ($theme['extensions']['is_enabled'] === TRUE) {
        if (isset($config['enable_markup_overrides']) && $config['enable_markup_overrides'] === 1) {
          if (isset($config['search_block_hide_submit']) && $config['search_block_hide_submit'] === 1) {
            $form['actions']['#attributes']['class'][] = 'visually-hidden';
            $form['#attributes']['class'][] = 'submit-is-hidden';
          }
          else {
            $form['#attributes']['class'][] = 'submit-is-visible';
            // Submit value. This could be used if the button has a visible value (instead of an icon).
            // if (isset($config['search_block_submit_value']) && !empty($config['search_block_submit_value'])) {
            // $form['actions']['submit']['#value'] = Html::escape($config['search_block_submit_value']);
            // }.
          }
          // Placeholders.
          if (isset($config['search_block_placeholder_text']) && !empty($config['search_block_placeholder_text'])) {
            $form['keys']['#attributes']['placeholder'] = Html::escape($config['search_block_placeholder_text']);
            $form['keys']['#attributes']['class'][] = 'has-attribute-placeholder';
            $form['#attributes']['class'][] = 'has-placeholder';
          }
        }
      }

      // This is cool for browsers that support it, but it's hard to theme,
      // really it needs the modernizr test "input[search] search event" so we
      // can theme webkit on Mac without messing with other browsers.
      // $form['keys']['#attributes']['results'] = 5;
      // $form['keys']['#attributes']['autosave'] = $theme . '-search-results-form-submissions';.
      break;

    case 'search_form':
      $form['basic']['#attributes']['class'][] = 'search-basic';
      $form['basic']['submit']['#attributes']['class'][] = 'search-form__submit';
      unset($form['basic']['#attributes']['class'][array_search('container-inline', $form['basic']['#attributes']['class'])]);
      break;

    case 'node_preview_form_select':
      $form['backlink']['#options']['attributes']['class'][] = 'button';
      break;
  }
}

/**
 * Implements hook_form_BASE_FORM_ID_alter() for \Drupal\node\NodeForm.
 *
 * Changes vertical tabs to container and adds meta information.
 *
 * @param $form
 * @param \Drupal\Core\Form\FormStateInterface $form_state
 */
function at_core_form_node_form_alter(&$form, FormStateInterface $form_state) {
  $theme = &drupal_static(__FUNCTION__);
  if (!isset($theme)) {
    $data = new ThemeConfig(\Drupal::theme()->getActiveTheme()->getName());
    $theme = $data->getConfig();
  }

  /** @var \Drupal\node\NodeInterface $node */
  $node = $form_state->getFormObject()->getEntity();

  $form['#theme'] = ['node_edit_form'];
  $form['#attached']['library'][] = $theme['provider'] . '/node_form';

  $form['advanced']['#type'] = 'container';
  $is_new = !$node->isNew() ? \Drupal::service('date.formatter')->format($node->getChangedTime(), 'short') : t('Not saved yet');

  $form['meta'] = [
    '#attributes' => ['class' => ['entity-meta__header']],
    '#type' => 'container',
    '#group' => 'advanced',
    '#weight' => -100,
    'published' => [
      '#type' => 'html_tag',
      '#tag' => 'h3',
      '#value' => $node->isPublished() ? t('Published') : t('Not published'),
      '#access' => !$node->isNew(),
      '#attributes' => [
        'class' => [
          'entity-meta__title',
          $node->isPublished() ? 'is-published' : 'unpublished',
        ],
      ],
    ],
    'changed' => [
      '#type' => 'item',
      '#wrapper_attributes' => ['class' => ['entity-meta__last-saved']],
      '#markup' => '<h4 class="label inline">' . t('Last saved') . '</h4> ' . $is_new,
    ],
    'author' => [
      '#type' => 'item',
      '#wrapper_attributes' => ['class' => ['entity-meta__author']],
      /*'#markup' => '<h4 class="label inline">' . t('Author') . '</h4> ' . $node->getOwner()->getUsername(),*/
    // '#markup' => '<h4 class="label inline">' . t('Author') . '</h4> ' . $node->getOwner()->​getAccountName(),
      '#markup' => '<h4 class="label inline">' . t('Author') . '</h4> ' . $node->getOwner()->getDisplayName(),
    ],
  ];

  $form['revision_information']['#type'] = 'container';
  $form['revision_information']['#group'] = 'meta';
}
