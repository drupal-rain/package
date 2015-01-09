<?php

/**
 * @file
 */

namespace Drupal\package\Field_Formatter;

use Drupal\kore\Field_Formatter;

class package_archive extends Field_Formatter\Field_Formatter_Abstract {

  public static function info() {
    return array(
      'package_archive' => array(
        'label'       => t('Package archives'),
        'field types' => array('package'),
        'settings'    => array(
          'style'            => 'list',
          'inline_delimiter' => ', ',
          'icon'             => TRUE, // If display file icon // @todo
          'link'             => TRUE, // If display file as link
        ),
      ),
    );
  }

  public static function settings_form($field, $instance, $view_mode, $form, &$form_state) {
    $display = $instance['display'][$view_mode];
    $settings = $display['settings'];
    $element = array();

    $element['style'] = array(
      '#type' => 'select',
      '#title' => t('List style'),
      '#options' => array(
        'list' => 'List',
        'inline' => 'Inline',
      ),
      '#default_value' => $settings['style'],
    );
    $element['inline_delimiter'] = array(
      '#type' => 'textfield',
      '#title' => t('Inline delimiter'),
      '#size' => 10,
      '#default_value' => $settings['inline_delimiter'],
      '#states' => array(
        'visible' => array(
          ':input[name$="[style]"]' => array('value' => 'inline'), // @todo Potential issue
        ),
      ),
    );
    $element['icon'] = array(
      '#type' => 'checkbox',
      '#title' => t('Show file icon'),
      '#default_value' => $settings['icon'],
    );
    $element['link'] = array(
      '#type' => 'checkbox',
      '#title' => t('Show file as link'),
      '#default_value' => $settings['link'],
    );

    return $element;
  }

  public static function view($entity_type, $entity, $field, $instance, $langcode, $items, $display) {
    // If Archive functionality is off
    if (!$field['settings']['archive']) {
      return NULL;
    }

    // Prepare archive items
    $archives = array();
    foreach ($items as $delta => $item) {
      if ($item['display'] || !$field['settings']['display_field']) {
        if (isset($item['archive']) && $archive = file_load($item['archive'])) {
          $archives[] = $archive;
        }
      }
    }

    //
    $element[0] = array(
      '#theme' => 'files',
      '#files' => $archives,
      '#link'  => $display['settings']['link'],
      '#style'  => $display['settings']['style'],
    );
    if ($display['settings']['style'] == 'inline') {
      $element[0]['#settings']['delimiter'] = $display['settings']['inline_delimiter'];
    }

    return $element;
  }

}
