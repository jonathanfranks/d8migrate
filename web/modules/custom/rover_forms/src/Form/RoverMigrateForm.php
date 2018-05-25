<?php

namespace Drupal\rover_forms\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\migrate\MigrateExecutable;
use Drupal\migrate\MigrateMessage;

/**
 * Class RoverMigrateForm.
 */
class RoverMigrateForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'rover_migrate_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $parentTerm = NULL;
    $vid = 'rover';
    $rovers = $this::termOptions($parentTerm, $vid);

    $form['select_a_rover'] = [
      '#type' => 'radios',
      '#title' => $this->t('Select a Rover'),
      '#description' => $this->t('Select one of NASA&#039;s Mars Rovers'),
      '#options' => $rovers,
      '#weight' => '0',
    ];

    $form['select_a_date'] = [
      '#type' => 'date',
      '#title' => $this->t('Select a date'),
      '#weight' => '0',
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $rover_id = $form_state->getValue('select_a_rover');
    $rover_name = $form['select_a_rover']['#options'][$rover_id];
    $date = $form_state->getValue('select_a_date');
    $message = $this->t('Retrieving images from @rover on @date.', [
      '@rover' => $rover_name,
      '@date' => $date,
    ]);
    drupal_set_message($message);

    $migration_ids = [
      'rover_photos',
    ];

    foreach ($migration_ids as $migration_id) {
      $executable = $this->getMigration($migration_id, $rover_name, $date);
      $ex = $executable->import();
    }
    $view_route = 'view.mars_rover_photos.page_1';
    $route_params = [
      'field_rover_target_id' => $rover_id,
      'field_camera_target_id' => 'All',
      'field_earth_date_value' => $date,
    ];
    $form_state->setRedirect($view_route, $route_params);
  }

  /**
   * Creates the select field options for Rover term selection.
   *
   * @param string $parentTerm
   *   The parent term to get children from.
   * @param string $vid
   *   The vocabulary ID.
   *
   * @return array
   *   The appropriate test date term options.
   */
  public static function termOptions($parentTerm, $vid) {
    $term_options = [];
    if ($parentTerm) {
      $terms = taxonomy_term_load_multiple_by_name($parentTerm, $vid);
      if ($terms) {
        $term = reset($terms);
        $tid = $term->id();

        $terms = \Drupal::entityTypeManager()
          ->getStorage('taxonomy_term')
          ->loadTree($vid, $tid);
        foreach ($terms as $term) {
          $term_options[$term->tid] = $term->name;
        }
      }
    }
    else {
      $terms = \Drupal::entityTypeManager()
        ->getStorage('taxonomy_term')
        ->loadTree($vid);
      foreach ($terms as $term) {
        $term_options[$term->tid] = $term->name;
      }
    }
    return $term_options;
  }

  /**
   * @param $migration_id
   * @param $rover_name
   * @param $date
   *
   * @return \Drupal\migrate\MigrateExecutable
   */
  protected function getMigration($migration_id, $rover_name, $date) {
    /** @var \Drupal\migrate\Plugin\Migration $migration */
    $migration = \Drupal::service('plugin.manager.migration')
      ->createInstance($migration_id);
    $executable = new MigrateExecutable($migration, new MigrateMessage());
    $source = $migration->getSourceConfiguration();
    $url = $source['urls'];
    $url = str_replace('/curiosity/', '/' . strtolower($rover_name) . '/', $url);
    $url = str_replace('2015-6-3', $date, $url);
    $source['urls'] = $url;
    $migration->set('source', $source);
    return $executable;
  }

}
