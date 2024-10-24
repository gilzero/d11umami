<?php

use Drupal\Core\Entity\EntityStorageException;

/**
 * Imports real estate data from csv data.
 *
 * @param \Drupal\taxonomy\TermInterface[] $terms
 * @param $context
 */
function import_real_estate($terms, &$context) {
  // Read real estate data.
  $demo_file = \Drupal::service('file_system')->realpath(\Drupal::service('extension.list.module')->getPath('styled_google_map_data') .'/csv/demo.csv');
  $file = new SplFileObject($demo_file, 'r');
  $file->setFlags(SplFileObject::READ_CSV);
  if (empty($context['sandbox'])) {
    $context['sandbox']['progress'] = 0;
    // Check for the number of lines in the file.
    $file->seek(PHP_INT_MAX);
    $context['sandbox']['max'] = $file->key() + 1;
    $file->rewind();
    $context['results']['max'] = $context['sandbox']['max'];
  }
  $data_storage = \Drupal::entityTypeManager()->getStorage('real_estate');
  $geo_wkt = \Drupal::service('geofield.wkt_generator');
  $offset = $context['sandbox']['progress'] + 1;
  foreach (new \LimitIterator($file, $offset, 100) as $row) {
    if (empty($row[0])) {
      continue;
    }
    $category = array_search($row[7], $terms);
    $context['results']['created'][] = $row[0];
    $item = $data_storage->create([
      'name' => $row[0],
      'price' => $row[9],
      'location' => $geo_wkt->WktBuildPoint([
        $row[11],
        $row[10],
      ]),
      'category' => $category,
    ]);
    try {
      $item->save();
    } catch (EntityStorageException $e) {
      \Drupal::logger('real_estate')->error($e->getMessage());
    }
  }
  // Update our progress information.
  $context['sandbox']['progress'] += 100;
  if ($context['sandbox']['progress'] <= $context['sandbox']['max']) {
    $context['finished'] = $context['sandbox']['progress'] / $context['sandbox']['max'];
  } else {
    $context['finished'] = 1;
  }
  $context['message'] = t('Running Batch, finished @percent%', [
    '@percent' => round($context['sandbox']['progress'] / $context['sandbox']['max'] * 100, 2),
  ]);
}

/**
 * @param $context
 */
function delete_real_estate(&$context) {
  $storage = \Drupal::entityTypeManager()->getStorage('real_estate');
  if (empty($context['sandbox'])) {
    $context['sandbox']['progress'] = 0;
    // Save data count for the termination message.
    $context['sandbox']['max'] = $storage->getQuery()->accessCheck(FALSE)->count()->execute();
    $context['results']['max'] = $context['sandbox']['max'];
    $context['results']['created'] = [];
  }
  $ids = $storage->getQuery()->accessCheck(FALSE)->range(0, 100)->execute();
  $context['results']['created'] = array_merge($context['results']['created'], $ids);
  $items = $storage->loadMultiple($ids);
  $storage->delete($items);
  // Update our progress information.
  $context['sandbox']['progress'] = $context['sandbox']['progress'] + count($items);
  if ($context['sandbox']['max']) {
    if ($context['sandbox']['progress'] <= $context['sandbox']['max']) {
      $context['finished'] = $context['sandbox']['progress'] / $context['sandbox']['max'];
    }
    else {
      $context['finished'] = 1;
    }
    $context['message'] = t('Running Batch, finished @percent%', [
      '@percent' => round($context['sandbox']['progress'] / $context['sandbox']['max'] * 100, 2),
    ]);
  } else {
    $context['finished'] = 1;
  }
}

/**
 * @param $success
 * @param $results
 * @param $operations
 */
function demo_real_estate_finished($success, $results, $operations) {
  if ($success) {
    \Drupal::messenger()->addMessage(t('Import was successful: imported @num', ['@num' => count($results['created'])]));
  }
  else {
    \Drupal::messenger()->addMessage(t('Import failed'));
  }
}
