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

return [
  'cashautopay_payment_instruments' => [
    'group_name' => 'cashautopay',
    'name' => 'cashautopay_payment_instruments',
    'type' => 'Array',
    'html_type' => 'Select',
    'serialize' => CRM_Core_DAO::SERIALIZE_JSON,
    'default' => [],
    'is_domain' => 1,
    'is_contact' => 0,
    'quick_form_type' => 'Element',
    'html_attributes' => ['multiple' => 1],
  ],
  'cashautopay_max_catchup_cycles' => [
    'group_name' => 'cashautopay',
    'name' => 'cashautopay_max_catchup_cycles',
    'type' => 'Integer',
    'default' => 2,
    'is_domain' => 1,
    'is_contact' => 0,
  ],
  'cashautopay_run_limit' => [
    'group_name' => 'cashautopay',
    'name' => 'cashautopay_run_limit',
    'type' => 'Integer',
    'default' => 500,
    'is_domain' => 1,
    'is_contact' => 0,
  ],
  'cashautopay_grace_days' => [
    'group_name' => 'cashautopay',
    'name' => 'cashautopay_grace_days',
    'type' => 'Integer',
    'default' => 3,
    'is_domain' => 1,
    'is_contact' => 0,
  ],
  'cashautopay_debug' => [
    'group_name' => 'cashautopay',
    'name' => 'cashautopay_debug',
    'type' => 'Boolean',
    'default' => 0,
    'is_domain' => 1,
    'is_contact' => 0,
  ],
];
