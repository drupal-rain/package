<?php

/**
 * @file
 */


namespace Drupal\package\Field;

use Drupal\kore\Field;


class package extends Field\Field_Abscract {

  public static function info() {
    return array(
      'package' => array(
        'label' => t('Package'),
        'description' => t("This field allow user to create file package for files."),
        'settings' => array(
          'display_field' => FALSE, // Enable display field
          'display_default' => TRUE, // Display by default
          'archive' => FALSE, // Create archive for package files
          'archive_scheme' => 'public', // Archive file scheme, destination
          'archive_directory' => '', // Directory to save archive
        ),
        'instance_settings' => array(
          'description_field' => FALSE, // Enable description field
        ),
        'default_widget' => 'package_default',
        'default_formatter' => 'package_default',
      ),
    );
  }

  public static function schema($field) {
    return array(
      'columns' => array(
        'title' => array(
          'description' => 'The title of this package.',
          'type' => 'varchar',
          'length' => 255,
          'not null' => TRUE,
          'default' => '',
        ),
        'description' => array(
          'description' => 'A description of the package.',
          'type' => 'text',
          'not null' => FALSE,
        ),
        'display' => array(
          'description' => 'Flag to control whether this package should be displayed when viewing content.',
          'type' => 'int',
          'size' => 'tiny',
          'unsigned' => TRUE,
          'not null' => TRUE,
          'default' => 1,
        ),
        'package' => array(
          'description' => 'Serialized data.',
          'type' => 'text',
          'size' => 'big',
          'serialize' => TRUE,
          'serialized default' => 'a:0:{}',
        ),
        'archive' => array(
          'description' => 'The {file_managed}.fid being referenced in this field.',
          'type' => 'int',
          'not null' => FALSE,
          'unsigned' => TRUE,
        ),
      ),
      'indexes' => array(
        'archive' => array('archive'),
      ),
      'foreign keys' => array(
        'archive_fid' => array(
          'table' => 'file_managed',
          'columns' => array('archive' => 'fid'),
        ),
      ),
    );
  }

  public static function settings_form($field, $instance, $has_data) {
    $settings = $field['settings'];
    $form = array();
    // Display field
    $form['display_field'] = array(
      '#type' => 'checkbox',
      '#title' => t('Enable %display field', array('%display' => 'display')),
      '#default_value' => $settings['display_field'],
    );
    $form['display_default'] = array(
      '#type' => 'checkbox',
      '#title' => t('Turn on %display field by default', array('%display' => 'display')),
      '#default_value' => $settings['display_default'],
    );
    // Archive functionality
    $form['archive_fieldset'] = array(
      '#type' => 'fieldset',
      '#title' => t('Archive settings'),
      '#parents' => array('field', 'settings'),
    );
    $form['archive_fieldset']['archive'] = array(
      '#type' => 'checkbox',
      '#title' => t('Enable archive functionality'),
      '#description' => t('Archive the whole package items into an archive file.'),
      '#default_value' => $settings['archive'],
      '#disabled' => $has_data,
    );
    $form['archive_fieldset']['archive_scheme'] = array(
      '#type' => 'radios',
      '#title' => t('Scheme'),
      '#destination' => t('Storage destination for archive'),
      '#options' => \Drupal\ko\File::schemeOptions(),
      '#default_value' => $settings['archive_scheme'],
      '#disabled' => $has_data,
    );
    $form['archive_fieldset']['archive_directory'] = array(
      '#type' => 'textfield',
      '#title' => t('Directory'),
      '#default_value' => $settings['archive_directory'],
      '#description' => theme('token_tree_link', array('token_types' => array($instance['entity_type'], 'file', 'user'))),
      '#element_validate' => array('token_element_validate'),
      '#token_types' => array($instance['entity_type'], 'file', 'user'),
      '#disabled' => $has_data,
    );

    return $form;
  }

  public static function instance_settings_form($field, $instance) {
    $settings = $instance['settings'];
    $form = array();

    // Description field
    $form['description_field'] = array(
      '#type' => 'checkbox',
      '#title' => t('Enable %description field', array('%description' => 'description')),
      '#default_value' => $settings['description_field'],
    );

    return $form;
  }

  public static function is_empty($item, $field) {
    if (
      !empty($item['title'])
      || !empty($item['description'])
      || !empty($item['pack'])) {
      return FALSE;
    }

    return TRUE;
  }

  public static function load($entity_type, $entities, $field, $instances, $langcode, &$items, $age) {
    foreach ($entities as $id => $entity) {
      foreach ($items[$id] as $delta => $item) {
        $package = unserialize($item['package']);
        $items[$id][$delta]['package'] = $package;
      }
    }
  }

  public static function presave($entity_type, $entity, $field, $instance, $langcode, &$items) {
    global $user;

    // Archive the package if needed.
    // Default path: public://
    if ($field['settings']['archive']) {
      foreach ($items as $delta => $item) {
        if (!isset($item['archive'])) {
          $dir = token_replace($field['settings']['archive_directory'], array(
            'user' => $user,
            $entity_type => $entity,
          ));
          // @todo Rename the file
          // @todo Better name solution, url friendly, filename pattern.
          $archive_file = $field['settings']['archive_scheme'] . '://' . $dir . '/' . $item['title'] . '.zip';
        }
        else {
          $archive_file = $item['archive'];
        }
        // @todo Check if need update to avoid update frequently.
        $archiver = new \ArchiverZipFile($archive_file);
        $archiver->removeAll();
        foreach ($item['package'] as $fid => $package_item) {
          $archiver->addFile($fid);
        }
        $archive_file = $archiver->save();
        // New archive file
        if (!isset($item['archive']) && $archive_file) {
          $items[$delta]['archive'] = $archive_file->fid;
        }
      }
    }

    // Serialize package value, because it's an array.
    foreach ($items as $delta => $item) {
      $items[$delta]['package'] = serialize($item['package']);
    }

  }

}
