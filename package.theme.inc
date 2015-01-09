<?php

/**
 * @file Provide theme features.
 */

/**
 * Implements hook_theme().
 */
function package_theme() {
  return array(
    'package_download_item' => array(
      'render element' => 'element',
      'template' => 'templates/package_download_item',
    ),
  );
}

// -----------------------------------------------------------------------------
// Theme Registry: 'package_download_item'

/**
 * Template default preprocess for 'package_download_item'.
 */
function template_preprocess_package_download_item(&$vars) {
  $element = $vars['element'];
  $item = $element['#item'];
  $settings = $element['#settings'];

  // Prepare some variables
  $vars['title'] = $item['title'];
  $vars['description'] = $item['description'];
  $vars['display'] = $item['display'];

  // Prepare settings
  $date_type = isset($settings['date_type']) ? $settings['date_type'] : 'medium';
  $date_format = isset($settings['date_format']) ? $settings['date_format'] : '';

  // $archive
  if (isset($item['archive']) && $archive = file_load($item['archive'])) {
    $vars['archive'] = theme('file_link', array('file' => $archive)) . ' (' . format_size($archive->filesize) . ')';
  }

  // $files_list
  if ($settings['files']) {
    $rows = array();
    foreach ($item['package'] as $fid => $package_item) {
      $file = file_load($fid);
      $rows[] = array(
        format_date($file->timestamp, $date_type, $date_format),
        theme('file_link', array('file' => $file)),
        format_size($file->filesize),
      );
    }
    $vars['files_table'] = theme('table', array(
      'header' => array(t('Date'), t('File'), t('Size')),
      'rows' => $rows,
      'attributes' => array(
        'class' => array('full'),
      )
    ));
  }
}
