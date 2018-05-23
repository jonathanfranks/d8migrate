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
 *   id = "parse_pdf"
 * )
 *
 * @code
 *   pdf_text:
 *     plugin: parse_pdf
 *     source: filename
 * @endcode
 */
class ParsePDF extends ProcessPluginBase {

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
    if (!file_exists($value)) {
      throw new MigrateException('File ' . $value . ' not found.');
    }
    $parser = new Parser();
    $pdf = $parser->parseFile($value);
    $text = $pdf->getText();
    return $text;
  }

}
