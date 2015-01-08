<?php

/**
 * @file Field formatter: package_default.
 *
 * Simple list all the package items grouped by package.
 */

namespace Drupal\package\Field_Formatter;

use Drupal\kore\Field_Formatter;

class package_default extends Field_Formatter\Field_Formatter_Abstract {

  public static function info() {
    return array(
      'package_default' => array(
        'label' => t('Simple list'),
        'field types' => array('package'),
      ),
    );
  }

  public static function view($entity_type, $entity, $field, $instance, $langcode, $items, $display) {
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

}
