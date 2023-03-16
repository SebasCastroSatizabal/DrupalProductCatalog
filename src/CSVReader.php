<?php

/**
 * @file Contains the CSV reader Class
 */

namespace Drupal\product_catalog;

use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Get data from CSV files.
 */
class CSVReader {

  /**
   * Entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs Importer object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Entity type manager service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * Gets the content of the CSV file using the fid.
   *
   * @param int $id the fid of the file.
   * @param string $delimiter the CSV delimiter.
   *
   * @return array Array with the headers and data.
   */
  public function getCsvByFid(int $id, string $delimiter = ','): array {
    /**  @var \Drupal\file\Entity\File $file */
    $file = $this->getCsvFile($id);
    $results = [];

    if (($csv = fopen($file->uri->getString(), 'r')) !== FALSE) {
      // Get the field names from the first row
      $results['headers'] = fgetcsv($csv, 0, $delimiter);

      while (($row = fgetcsv($csv, 0, $delimiter)) !== FALSE) {
        $results['data'][] = $row;
      }
      fclose($csv);
    }

    return $results;
  }

  /**
   * Get the CSV file entity.
   *
   * @param int $id the fid.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   * The file entity. Null if not found.
   */
  private function getCsvFile(int $id): ?\Drupal\Core\Entity\EntityInterface {
    try {
      return $this->entityTypeManager->getStorage('file')->load($id);
    }
    catch (\Exception $e) {
      return null;
    }
  }

}
