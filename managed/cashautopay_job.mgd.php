<?php

return [
  [
    'name'   => 'Job_CashAutoPay_Run',
    'entity' => 'Job',
    'module' => 'de.systopia.cashautopay',
    'params' => [
      'version'       => 3,
      'domain_id'     => 'current_domain',
      'name'          => 'CashAutoPay: Run',
      'description'   => 'Generate scheduled cash contributions',
      'api_entity'    => 'CashAutoPay',
      'api_action'    => 'Run',
      'run_frequency' => 'Daily',
      'is_active'     => 0,
    ],
    'update' => 'always',
  ],
];
