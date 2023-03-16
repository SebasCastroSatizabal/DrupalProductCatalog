<?php

/**
 * @file Contains the Product Catalog Importer class.
 */

namespace Drupal\product_catalog;

use Drupal\node\Entity\Node;

/**
 * Import data into Drupal nodes.
 */
class Importer {

  /**
   * The data to import.
   *
   * @var array
   */
  protected array $data;

  /**
   * The content type of the nodes.
   *
   * @var string
   */
  private string $bundle;

  /**
   * Construct a Importer object.
   *
   * @param string $bundle The content type of the nodes.
   * @param array $data The data to import.
   */
  public function __construct(string $bundle, array $data) {
    $this->data = $data;
    $this->bundle = $bundle;
  }

  /**
   * Batch operation to create the nodes.
   *
   * @param array $content The data of the nodes. Include all the fields to insert.
   * @param array $context Context of the batch operation
   *
   * @return void
   */
  public function insertNodeData(array $content, array &$context) {

    $results = [];
    foreach ($content as $node_info) {
      $node = Node::create(['type' => $this->bundle]);

      try {
       foreach ($node_info as $field => $value) {
         $node->set($field, $value);
       }
        $node->save();
        $results[] = true;
      } catch (\Exception $e) {
        $results[] = false;
      }
    }
    $context['message'] = 'Creating ALL Nodes...';
    $context['results'] = $results;
  }

  /**
   * Batch finish operation.
   *
   * @param bool $success Hwether the operation was finished successfully or no.
   * @param array $results The results of the operations.
   * @param array $operations The operations.
   *
   * @return void
   */
  public function finished(bool $success, array $results, array $operations) {
    if ($success) {
      $message = \Drupal::translation()->formatPlural(
        count(array_filter($results)),
        'One entity of "' . $this->bundle . '" was created.',
        '@count entities of "' . $this->bundle . '" were created.',
      );

      \Drupal::messenger()->addStatus($message);
    }
    else {
      \Drupal::messenger()->addError(t('Finished with an error.'));
    }

  }

  /**
   * Processes all the data and creates the nodes using a batch operation.
   *
   * @return void
   */
  public function processNodes() {
    $operations[] = [
      [$this, 'insertNodeData'],
      [$this->data],
    ];
    $batch = [
      'title' => t('Creating All Nodes...'),
      'operations' => $operations,
      'finished' => [$this, 'finished'],
    ];
    batch_set($batch);
  }
}
