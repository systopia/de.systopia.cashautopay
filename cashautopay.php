<?php
/*-------------------------------------------------------+
| AssumedPayments                                        |
| Copyright (C) 2025 SYSTOPIA                            |
| Author: J. Ortiz (ortiz -at- systopia.de)              |
| http://www.systopia.de/                                |
+--------------------------------------------------------+
| License: AGPLv3, see /LICENSE                          |
+--------------------------------------------------------*/
declare(strict_types=1);

if (file_exists(__DIR__ . '/cashautopay.civix.php')) {
    require_once __DIR__ . '/cashautopay.civix.php';
}

/**
 * hook_civicrm_config()
 */
function cashautopay_civicrm_config(&$config) {
    if (function_exists('_cashautopay_civix_civicrm_config')) {
        _cashautopay_civix_civicrm_config($config);
    }
}

/**
 * hook_civicrm_navigationMenu()
 */
function cashautopay_civicrm_navigationMenu(&$menu) {
    if (function_exists('_cashautopay_civix_insert_navigation_menu')) {
        _cashautopay_civix_insert_navigation_menu($menu, 'Administer/System Settings', [
            'label'      => ts('Cash AutoPay Settings'),
            'name'       => 'cashautopay_settings',
            'url'        => 'civicrm/cashautopay/settings',
            'permission' => 'administer CiviCRM',
            'operator'   => 'AND',
            'separator'  => 0,
        ]);
    }
}

/**
 * hook_civicrm_xmlMenu()
 */
function cashautopay_civicrm_xmlMenu(&$files) {
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
function cashautopay_civicrm_install() {
    _cashautopay_civix_civicrm_install();
}

/**
 * hook_civicrm_managed()
 */
function cashautopay_civicrm_managed(&$entities) {

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
function cashautopay_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
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
