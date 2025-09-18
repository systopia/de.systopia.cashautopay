<?php
function civicrm_api3_cash_auto_pay_preview($params) {
  $runner = new CRM_CashAutoPay_Service_Runner();
  $res = $runner->preview($params);
  return civicrm_api3_create_success([$res], $params, 'CashAutoPay', 'Preview');
}
function civicrm_api3_cash_auto_pay_run($params) {
  $runner = new CRM_CashAutoPay_Service_Runner();
  $res = $runner->run($params);
  return civicrm_api3_create_success([$res], $params, 'CashAutoPay', 'Run');
}
if (!function_exists('civicrm_api3_cashautopay_preview')) {
  function civicrm_api3_cashautopay_preview($params) {
    return civicrm_api3_cash_auto_pay_preview($params);
  }
}
if (!function_exists('civicrm_api3_cashautopay_run')) {
  function civicrm_api3_cashautopay_run($params) {
    return civicrm_api3_cash_auto_pay_run($params);
  }
}
