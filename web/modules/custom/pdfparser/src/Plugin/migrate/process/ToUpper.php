<?php

namespace Drupal\pdfparser\Plugin\migrate\process;

use Drupal\migrate\MigrateException;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Smalot\PdfParser\Parser;

/**
 * Extracts text from a PDF source.
 *
 * @MigrateProcessPlugin(
 *   id = "to_upper"
 * )
 *
 * @code
 *   pdf_text:
 *     plugin: to_upper
 *     source: text
 * @endcode
 */
class ToUpper extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (!$value) {
      throw new MigrateException('Input must not be null or empty.');
    }
    if (is_array($value)) {
      throw new MigrateException('Input must not be an array.');
    }
    $text = strtoupper($value);
    return $text;
  }

}
