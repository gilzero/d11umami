<?php

/**
 * @file
 * Solo Theme Alter.
 */

use Drupal\comment\CommentInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hook_form_alter().
 */
function solo_form_alter(&$form, FormStateInterface $form_state, $form_id): void {
  if (isset($form['actions']['submit']) && (count($form['actions'])) <= 2) {
    $form['actions']['submit']['#attributes']['class'][] = 'button--primary';
  }
}

/**
 * Implements hook_form_FORM_ID_alter().
 */
function solo_form_node_preview_form_select_alter(&$form, FormStateInterface $form_state, $form_id): void {
  $form['backlink']['#options']['attributes']['class'][] = 'button';
  $form['backlink']['#options']['attributes']['class'][] = 'button--small';
  $form['backlink']['#options']['attributes']['class'][] = 'button--icon-back';
  $form['backlink']['#options']['attributes']['class'][] = 'button--primary';
  $form['view_mode']['#attributes']['class'][] = 'form-element--small';
}

/**
 * Implements hook_form_FORM_ID_alter() for search_block_form.
 */
function solo_form_search_block_form_alter(&$form, FormStateInterface $form_state): void {
  // Add placeholder text to keys input.
  $form['keys']['#attributes']['placeholder'] = t('Search by keyword or phrase.');

  // Add a class to the submit button.
  $form['actions']['submit']['#attributes']['class'][] = 'search-button-block';
}

/**
 * Implements hook_form_FORM_ID_alter() for search_block_form.
 */
function solo_form_search_form_alter(&$form, FormStateInterface $form_state): void {
  if (isset($form['help_link'])) {
    unset($form['help_link']);
  }
  // Add placeholder text to keys input.
  $form['keys']['#attributes']['placeholder'] = t('Search by keyword or phrase.');
  $form['basic']['submit']['#attributes']['class'][] = 'button--primary';
  $form['advanced']['submit']['#attributes']['class'][] = 'button--primary';
}

/**
 * Implements hook_form_FORM_ID_alter() for node_form.
 */
function solo_form_node_form_alter(&$form, FormStateInterface $form_state): void {
  // Set the theme for the form.
  $form['#theme'] = ['node_edit_form'];

  // Define the 'advanced' and 'meta' containers.
  $form['advanced']['#type'] = 'container';
  $form['meta']['#type'] = 'container';

  // Set access for the 'meta' container.
  $form['meta']['#access'] = TRUE;

  // Add 'container-inline' class to 'changed' and 'author' wrappers.
  $form['meta']['changed']['#wrapper_attributes']['class'][] = 'container-inline';
  $form['meta']['author']['#wrapper_attributes']['class'][] = 'container-inline';

  // Define the 'revision_information' container and group it under 'meta'.
  $form['revision_information']['#type'] = 'container';
  $form['revision_information']['#group'] = 'meta';
}

/**
 * Implements hook_form_FORM_ID_alter().
 */
function solo_form_comment_form_alter(&$form, FormStateInterface $form_state, $form_id): void {
  $is_comment = $form_state->getFormObject()->getEntity();

  if ($is_comment instanceof CommentInterface) {
    $form['actions']['submit']['#value'] = $is_comment->hasParentComment() ? t('Reply to comment') : t('Post comment');
  }
}

/**
 * Implements hook_form_views_exposed_form_alter().
 */
function solo_form_views_exposed_form_alter(&$form): void {
  $form['#attributes']['class'][] = 'form--inline';
}
