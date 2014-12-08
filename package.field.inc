<?php

// -----------------------------------------------------------------------------
// Info hooks

/**
 * Implements hook_field_info().
 */
function package_field_info() {
  return array(
    'package' => array(
      'label' => t('Package'),
      'description' => t("This field allow user to create file package for files."),
      'settings' => array(),
      'default_widget' => 'package_table',
      'default_formatter' => 'package_default',
    ),
  );
}

/**
 *  Implements hook_field_widget_info().
 */
function package_field_widget_info() {
  return array(
    'package_table' => array(
      'label' => t('Visual table'),
      'field types' => array('package'),
    ),
    // @todo
    /*
    'package_textarea' => array(
      'label' => t('Text area'),
      'field types' => array('package'),
      'settings' => array('rows' => 5),
    ),
    */
  );
}

/**
 * Implements hook_field_formatter_info().
 */
function package_field_formatter_info() {
  return array(
    'package_default' => array(
      'label' => t('Default'),
      'field types' => array('package'),
    ),
  );
}

// -----------------------------------------------------------------------------
// Field implementation

/**
 * Implements hook_field_settings_form().
 */
function package_field_settings_form($field, $instance, $has_data) {
  $settings = $field['settings'];

  $form = array();

  return $form;
}

function package_field_instance_settings_form() {

}

/**
 * Implements hook_Field_is_empty().
 */
function package_field_is_empty($item, $field) {
  if (
    !empty($item['title'])
    || !empty($item['description'])
    || !empty($item['pack'])) {
    return FALSE;
  }

  return TRUE;
}

/**
 *  Implements hook_field_presave().
 */
function package_field_presave($entity_type, $entity, $field, $instance, $langcode, &$items) {
  // Initiate
  /*
  foreach ($items as $delta => $item) {
    $items[$delta]['package'] = serialize(array());
  }
  */

  // Set value
  switch ($instance['widget']['type']) {
    case 'package_table':
      foreach ($items as $delta => $item) {
        // @issue Only works in PHP 5.6
        // @temporary Unset index.
        //$pack_input = array_filter($item['package'], 'package_value_filter', ARRAY_FILTER_USE_BOTH);
        $pack_input = $item['package'];
        unset($pack_input['attach']);
        unset($pack_input['pack']);
        $items[$delta]['package'] = serialize($pack_input);
      }
      break;
    case 'package_textarea':
      // @todo Make it compatible with 'package_table' input.
      foreach ($items as $delta => $item) {
        $pack_input = $item['package'];
        $items[$delta]['package'] = serialize(explode(',', $pack_input));
      }
      break;
  }

}

/**
 *  Implements hook_field_load().
 */
function package_field_load($entity_type, $entities, $field, $instances, $langcode, &$items, $age) {
  foreach ($entities as $id => $entity) {
    foreach ($items[$id] as $delta => $item) {
      $package = unserialize($item['package']);
      $items[$id][$delta]['package'] = $package;
    }
  }
}

/**
 *  Implements hook_field_widget_form().
 */
function package_field_widget_form(&$form, &$form_state, $field, $instance, $langcode, $items, $delta, $element) {
  $element += (array) $element;

  $title = isset($items[$delta]['title']) ? $items[$delta]['title'] : NULL;
  $description = isset($items[$delta]['description']) ? $items[$delta]['description'] : NULL;
  $package = isset($items[$delta]['package']) ? $items[$delta]['package'] : NULL;

  $element['title'] = array(
    '#type' => 'textfield',
    '#title' => t('Title'),
    '#default_value' => $title,
  );
  $element['description'] = array(
    '#type' => 'textarea',
    '#title' => t('Description'),
    '#default_value' => $description,
    '#rows' => 3,
  );

  switch ($instance['widget']['type']) {
    case 'package_table':
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
      break;
    case 'package_textarea':
      if ($package) {
        $package_input = implode(',', $package);
      }
      else {
        $package_input = NULL;
      }
      $element['package'] = array(
        '#type' => 'textarea',
        '#title' => t('Pack'),
        '#default_value' => $package_input,
        '#rows' => $instance['widget']['settings']['rows'],
        '#attributes' => array('class' => array('text-full')),
      );
      break;
  }

  return $element;
}

/**
 * Callback to get the options list for attach form.
 */
function package_field_attach_options_list($element, &$form_state, $complete_form) {
  if (!$form_state['rebuild']) {
    $value = $element['#default_value'];
  }
  else {
    $value = $element['#value'];
  }
  $ids_exist = array();
  foreach ($value as $id => $item) {
    $ids_exist[] = $id;
  }

  // Ignore field ui demo form.
  if (!isset($complete_form['#entity'])) {
    $ids_available = array();
  }
  else {
    $ids_available = package_field_entity_file_usage($complete_form['#entity_type'], $complete_form['#entity']);
  }

  // Differ the available and existing.
  $ids = array_diff($ids_available, $ids_exist);
  $options = array();
  foreach ($ids as $id) {
    $file = file_load($id);
    $options[$id] = $file->filename;
  }

  return $options;
}

/**
 *  Implements hook_field_formatter_view().
 */
function package_field_formatter_view($entity_type, $entity, $field, $instance, $langcode, $items, $display) {
  $element = array();

  foreach ($items as $delta => $item) {
    $markup = '<div class="package">';
    $markup .= "<h3>{$item['title']}</h3>";
    $markup .= "<p>{$item['description']}</p>";
    $markup .= "<ul>";
    foreach ($item['package'] as $fid => $item) {
      $file = file_load($fid);
      //$markup .= "<li>{$file->filename}</li>";
      $markup .= "<li>";
      /*
      $markup .= theme('file_entity_download_link', array(
        'file' => $file,
        'text' => $file->filename,
      ));
      */
      $markup .= theme('file_link', array(
        'file' => $file,
      ));
      $markup .= "</li>";
    }
    $markup .= "</ul>";
    $markup .= "</div>";
    $element[$delta]['#markup'] = $markup;
  }

  return $element;
}

// -----------------------------------------------------------------------------
// Helper, Callback

/**
 * Helper function to get the files attached to this entity.
 */
function package_field_entity_file_usage($entity_type, $entity) {
  list($id, $vid, $bundle) = entity_extract_ids($entity_type, $entity);
  $result = db_select('file_usage', 'fu')
    ->fields('fu', array('fid'))
    ->condition('type', $entity_type)
    ->condition('id', $id)
    ->execute()
    ->fetchCol();

  return $result;
}
