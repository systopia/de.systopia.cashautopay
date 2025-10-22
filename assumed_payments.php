<?php
/*-------------------------------------------------------+
| AssumedPayments                                        |
| Copyright (C) 2025 SYSTOPIA                            |
| Author: J. Ortiz (ortiz -at- systopia.de)              |
| http://www.systopia.de/                                |
+--------------------------------------------------------+
| License: AGPLv3, see /LICENSE                          |
+--------------------------------------------------------*/
declare(strict_types = 1);

// phpcs:disable PSR1.Files.SideEffects.FoundWithSymbols
require_once 'assumed_payments.civix.php';
// phpcs:enable

/**
 * hook_civicrm_config()
 */
function assumed_payments_civicrm_config(&$config) {
  if (function_exists('_assumed_payments_civix_civicrm_config')) {
    _assumed_payments_civix_civicrm_config($config);
  }
}

/**
 * hook_civicrm_navigationMenu()
 */
function assumed_payments_civicrm_navigationMenu(&$menu) {
  if (function_exists('_assumed_payments_civix_insert_navigation_menu')) {
    _assumed_payments_civix_insert_navigation_menu($menu, 'Administer/System Settings', [
      'label'      => ts('Assumed Payments Settings'),
      'name'       => 'assumed_payments_settings',
      'url'        => 'civicrm/assumed-payments/settings',
      'permission' => 'administer CiviCRM',
      'operator'   => 'AND',
      'separator'  => 0,
    ]);
  }
}

/**
 * hook_civicrm_xmlMenu()
 */
function assumed_payments_civicrm_xmlMenu(&$files) {
  $dir = __DIR__ . '/xml/Menu';
  if (is_dir($dir)) {
    foreach (glob($dir . '/*.xml') as $xml) {
      $files[] = $xml;
    }
  }
}

/**
 * hook_civicrm_install()
 */
function assumed_payments_civicrm_install() {
  _assumed_payments_civix_civicrm_install();
}

/**
 * hook_civicrm_managed()
 */
function assumed_payments_civicrm_managed(&$entities) {

  $dir = __DIR__ . '/managed';
  if (is_dir($dir)) {
    foreach (glob($dir . '/*.mgd.php') as $mgd) {
      $data = include $mgd;
      if (is_array($data)) {
        $entities = array_merge($entities, $data);
      }
    }
  }
}

/**
 * hook_civicrm_alterSettingsFolders()
 */
function assumed_payments_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  $dir = __DIR__ . DIRECTORY_SEPARATOR . 'settings';
  if (is_dir($dir)) {
    if (!is_array($metaDataFolders)) {
      $metaDataFolders = [];
    }
    if (!in_array($dir, $metaDataFolders, TRUE)) {
      $metaDataFolders[] = $dir;
    }
  }
}
