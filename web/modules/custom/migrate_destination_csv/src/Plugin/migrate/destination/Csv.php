<?php

namespace Drupal\migrate_destination_csv\Plugin\migrate\destination;

use Drupal\migrate\Plugin\migrate\destination\DestinationBase;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Row;

/**
 * Outputs migration as CSV file.
 *
 * Available configuration keys:
 * - path: Path for output file.
 *
 * @MigrateDestination(
 *   id = "csv"
 * )
 *
 * @package Drupal\migrate_destination_csv\Plugin\migrate\destination
 */
class Csv extends DestinationBase {

  protected $supportsRollback = TRUE;

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    $ids['value']['type'] = 'string';
    return $ids;
  }

  /**
   * {@inheritdoc}
   */
  public function fields(MigrationInterface $migration = NULL) {
  }

  /**
   * Import the row.
   *
   * Derived classes must implement import(), to construct one new object
   * (pre-populated) using ID mappings in the Migration.
   *
   * @param \Drupal\migrate\Row $row
   *   The row object.
   * @param array $old_destination_id_values
   *   (optional) The old destination IDs. Defaults to an empty array.
   *
   * @return mixed
   *   The entity ID or an indication of success.
   */
  public function import(Row $row, array $old_destination_id_values = []) {
    $path = $this->migration->getDestinationPlugin()->configuration['path'];

    if (!file_exists($path)) {
      // File doesn't exist.
      // Create it and add column headers.
      $keys = array_keys($row->getDestination());
      $csv = implode(',', $keys);
      $csv .= PHP_EOL;

      file_put_contents($path, $csv, FILE_APPEND);
    }

    $csv_row = [];
    foreach ($row->getDestination() as $field => $value) {
      $csv_row[] = $value;
    }
    $csv = implode(',', $csv_row);
    $csv .= PHP_EOL;

    file_put_contents($path, $csv, FILE_APPEND);

    // For the purposes of this example, we'll use the first 255 characters of
    // the new row as the key. We'll use this to roll back.
    return ['csv' => substr($csv, 0, 255)];
  }

  /**
   * {@inheritdoc}
   */
  public function rollback(array $destination_identifier) {
    $path = $this->migration->getDestinationPlugin()->configuration['path'];
    if (file_exists($path)) {
      $file_contents = file_get_contents($path);
      $rows = explode(PHP_EOL, $file_contents);
      foreach ($rows as $rownum => $row) {
        $sub = substr($row, 0, 255) . PHP_EOL;
        foreach ($destination_identifier as $rollback_row) {
          if ($sub == $rollback_row) {
            // Remove row from contents.
            unset($rows[$rownum]);
          }
        }
      }
      $text = implode($rows, PHP_EOL);
      // Overwrite file contents without this rolled-back row.
      file_put_contents($path, $text);
    }

    parent::rollback($destination_identifier);
  }

}
