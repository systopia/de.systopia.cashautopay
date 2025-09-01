<?php

/**
 */

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

    $file = __DIR__ . '/api/v3/CashAutoPay.php';
    if (file_exists($file)) {
        require_once $file;
    }
}

/**
 * hook_civicrm_apiWrappers()
 */
function cashautopay_civicrm_apiWrappers(&$wrappers, $apiRequest) {
    $file = __DIR__ . '/api/v3/CashAutoPay.php';
    if (file_exists($file)) {
        require_once $file;
    }
}

/**
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
 */
function cashautopay_civicrm_xmlMenu(&$files) {
    if (function_exists('_cashautopay_civix_civicrm_xmlMenu')) {
        _cashautopay_civix_civicrm_xmlMenu($files);
        return;
    }
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
    if (function_exists('_cashautopay_civix_civicrm_install')) {
        _cashautopay_civix_civicrm_install();
    }
}

/**
 * hook_civicrm_uninstall()
 */
function cashautopay_civicrm_uninstall() {
    if (function_exists('_cashautopay_civix_civicrm_uninstall')) {
        _cashautopay_civix_civicrm_uninstall();
    }
}

/**
 * hook_civicrm_enable()
 */
function cashautopay_civicrm_enable() {
    if (function_exists('_cashautopay_civix_civicrm_enable')) {
        _cashautopay_civix_civicrm_enable();
    }
}

/**
 * hook_civicrm_disable()
 */
function cashautopay_civicrm_disable() {
    if (function_exists('_cashautopay_civix_civicrm_disable')) {
        _cashautopay_civix_civicrm_disable();
    }
}

/**
 * hook_civicrm_upgrade()
 */
function cashautopay_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
    if (function_exists('_cashautopay_civix_civicrm_upgrade')) {
        return _cashautopay_civix_civicrm_upgrade($op, $queue);
    }
    return TRUE;
}

/**
 * hook_civicrm_managed()
 */
function cashautopay_civicrm_managed(&$entities) {
    if (function_exists('_cashautopay_civix_civicrm_managed')) {
        _cashautopay_civix_civicrm_managed($entities);
        return;
    }
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
 * hook_civicrm_angularModules()
 */
function cashautopay_civicrm_angularModules(&$angularModules) {
    if (function_exists('_cashautopay_civix_civicrm_angularModules')) {
        _cashautopay_civix_civicrm_angularModules($angularModules);
    }
}

/**
 * hook_civicrm_alterSettingsFolders()
 */
function cashautopay_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
    if (function_exists('_cashautopay_civix_civicrm_alterSettingsFolders')) {
        _cashautopay_civix_civicrm_alterSettingsFolders($metaDataFolders);
        return;
    }
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

