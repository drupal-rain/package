<?php

/**
 * @file
 */


namespace Drupal\package\Field_Widget;

use Drupal\kore\Field_Widget;

class package_default extends Field_Widget\Field_Widget_Abstract {

  public static function info() {
    return array(
      'package_default' => array(
        'label' => t('Attach table'),
        'field types' => array('package'),
      ),
    );
  }

  public static function settings_form($field, $instance) {
    return array();
  }

  public static function form(&$form, &$form_state, $field, $instance, $langcode, $items, $delta, $element) {
    $element += (array) $element;

    // Title, Description
    $element['title'] = array(
      '#type' => 'textfield',
      '#title' => t('Title'),
      '#default_value' => isset($items[$delta]['title']) ? $items[$delta]['title'] : NULL,
    );
    if ($instance['settings']['description_field']) {
      $element['description'] = array(
        '#type' => 'textarea',
        '#title' => t('Description'),
        '#default_value' => isset($items[$delta]['description']) ? $items[$delta]['description'] : NULL,
        '#rows' => 3,
      );
    }
    if ($field['settings']['display_field']) {
      $element['display'] = array(
        '#type' => 'checkbox',
        '#title' => t('Display'),
        '#default_value' => isset($items[$delta]['display']) ? $items[$delta]['display'] : $field['settings']['display_default'],
      );
    }

    // Package
    $default_value = array();
    if (isset($form['#entity']) && isset($items[$delta]['package'])) {
      $default_value = $items[$delta]['package'];
    }
    $element['package'] = array(
      '#type' => 'package',
      '#package' => package_element_default_settings(),
      '#default_value' => $default_value,
    );
    $element['package']['#package']['options_list'] = 'package_field_attach_options_list';

    // Put in archive value
    $element['archive'] = array(
      '#type' => 'value',
      '#value' => isset($items[$delta]['archive']) ? $items[$delta]['archive'] : NULL,
    );

    return $element;
  }

}
