<?php
return [
  [
    'name' => 'Job_CashAutoPay_Run',
    'entity' => 'Job',
    'params' => [
      'version' => 3,
      'name' => 'CashAutoPay: Run',
      'description' => 'Generate scheduled cash contributions',
      'api_entity' => 'CashAutoPay',
      'api_action' => 'Run',
      'run_frequency' => 'Daily',
      'is_active' => 0,
    ],
  ],
];
