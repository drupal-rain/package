<?php

/**
 * @file
 */

namespace Drupal\package\Field_Formatter;

use Drupal\kore\Field_Formatter;

class package_download_box extends Field_Formatter\Field_Formatter_Abstract {

  public static function info() {
    return array(
      'package_download_box' => array(
        'label'       => t('Download box'),
        'field types' => array('package'),
      ),
    );
  }

  public static function view($entity_type, $entity, $field, $instance, $langcode, $items, $display) {
    return NULL;
  }

}
