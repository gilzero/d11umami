<?php

namespace Drupal\ai_search\Plugin\search_api\processor;

use Drupal\Core\Database\Query\AlterableInterface;
use Drupal\Core\Database\Query\SelectInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\search_api\Entity\Server;
use Drupal\search_api\IndexInterface;
use Drupal\search_api\Query\QueryInterface;

/**
 * Prepend AI Search results into the database search..
 *
 * @SearchApiProcessor(
 *   id = "database_boost_by_ai_search",
 *   label = @Translation("Boost Database by AI Search"),
 *   description = @Translation("Prepend results from the AI Search into the database results ready for subsequent filtering (if any) to improve relevance."),
 *   stages = {
 *     "preprocess_query" = 0,
 *   }
 * )
 */
class DatabaseBoostByAiSearch extends BoostByAiSearchBase {

  /**
   * {@inheritdoc}
   */
  public static function supportsIndex(IndexInterface $index): bool {
    if ($index->getServerInstance()->getBackendId() == 'search_api_database') {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::validateConfigurationForm($form, $form_state);

    if ($this->index->getServerId() && $server = Server::load($this->index->getServerId())) {
      if ($server->getBackendId() !== 'search_api_db') {
        $form_state->setErrorByName('search_api_ai_index', $this->t('This processor plugin only supports "search_api_db", but the backend of this index is "@backend"', [
          '@backend' => $server->getBackendId(),
        ]));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function preprocessSearchQuery(QueryInterface $query) {
    parent::preprocessSearchQuery($query);

    // Only do something if we have search terms. It is possible that the
    // index is being filtered only without any terms, in which case we have
    // nothing more to do.
    if ($query_string_keys = $query->getKeys()) {
      $ai_results = $this->getAiSearchResults($query_string_keys);
      if ($ai_results) {
        $query->addTag('database_boost_by_ai_search');
        $query->addTag('ai_search_ids:' . implode(',', array_keys($ai_results)));
      }
    }
  }

  /**
   * Alter the database query.
   *
   * This method is called from the hook_query_TAG_alter() function found in
   * ai_search.module.
   *
   * @param \Drupal\Core\Database\Query\AlterableInterface $query
   *   The database query.
   */
  public static function queryAlter(AlterableInterface $query) {

    // The 'search_api_db' tag is added in
    // Drupal\search_api_db\Plugin\search_api\backend\Database::createDbQuery()
    // which is passed the Search API Query itself as metadata.
    $search_api_query = $query->getMetaData('search_api_query');
    if (
      $query instanceof SelectInterface
      && $search_api_query instanceof QueryInterface
    ) {

      // Extract IDs from the query tag added in
      // DatabaseBoostByAiSearch::preprocessSearchQuery().
      $item_ids = [];
      $tags = $search_api_query->getTags();
      if (!empty($tags)) {
        foreach ($tags as $tag) {
          if (str_starts_with($tag, 'ai_search_ids:')) {
            $tag = str_replace('ai_search_ids:', '', $tag);
            $item_ids = explode(',', $tag);
          }
        }
      }

      // If we have entity IDs, alter the query.
      if ($item_ids && $query instanceof SelectInterface) {

        // Add an expression to boost AI result entity IDs.
        // Ensure the entity IDs are all integers.
        $placeholders = [];
        $expression_parts = [];
        $total = count($item_ids);
        foreach ($item_ids as $key => $item_id) {
          $expression_parts[] = 'WHEN t.item_id = :entity_' . $key . ' THEN ' . ($total - $key);
          $placeholders[':entity_' . $key] = $item_id;
        }
        $expression = 'CASE ' . implode(' ', $expression_parts) . ' ELSE 0 END';
        $query->addExpression($expression, 'ai_boost', $placeholders);

        // Sort by the ai_boost field first, followed by this index's score,
        // so AI results appear first.
        $order_by_parts =& $query->getOrderBy();
        $new_order = ['ai_boost' => 'DESC'] + $order_by_parts;
        $order_by_parts = $new_order;
      }
    }
  }

}
