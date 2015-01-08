<?php

/**
 * @file Field formatter: package_download.
 *
 * Show package archive download and table show all the items in it.
 */

namespace Drupal\package\Field_Formatter;

use Drupal\kore\Field_Formatter;

class package_download extends Field_Formatter\Field_Formatter_Abstract {

  public static function info() {
    return array(
      'package_download' => array(
        'label'       => t('Download list'),
        'field types' => array('package'),
        'settings'    => array(
          'archive'        => TRUE, // Show archive
          'files'          => TRUE, // Show files list
          'files_expanded' => FALSE, // Expand files list by default
        ),
      ),
    );
  }

  public static function settings_form($field, $instance, $view_mode, $form, &$form_state) {
    $display = $instance['display'][$view_mode];
    $settings = $display['settings'];
    $element = array();

    $element['archive'] = array(
      '#type' => 'checkbox',
      '#title' => t('Show archive'),
      '#default_value' => $settings['archive'],
    );
    $element['files'] = array(
      '#type' => 'checkbox',
      '#title' => t('Show files list'),
      '#default_value' => $settings['files'],
    );
    $element['files_expanded'] = array(
      '#type' => 'checkbox',
      '#title' => t('Expand files list by default'),
      '#default_value' => $settings['files_expanded'],
    );

    return $element;
  }

  public static function view($entity_type, $entity, $field, $instance, $langcode, $items, $display) {
    foreach ($items as $delta => $item) {
      $element[$delta] = array(
        '#theme' => 'package_download_item',
        '#entity_type' => $entity_type,
        '#entity' => $entity,
        '#item' => $item,
        '#settings' => $display['settings'],
      );
    }

    return $element;
  }

}
