<?php
function civicrm_api3_cashautopay_run($params) {
  $runner = new CRM_CashAutoPay_Service_Runner();
  $res = $runner->run($params);
  return civicrm_api3_create_success([$res], $params, 'CashAutoPay', 'Run');
}
