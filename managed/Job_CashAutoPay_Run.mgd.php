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
    [
        'name'   => 'Job_CashAutoPay_Run',
        'entity' => 'Job',
        'update'  => 'always',
        'cleanup' => 'never',
        'params'  => [
            'version'       => 3,
            'api_version'   => 4,
            'name'          => 'Cash AutoPay: Run',
            'description'   => 'Create assumed cash contributions and payments for overdue recurring contributions.',
            'api_entity'    => 'AssumedPayments',
            'api_action'    => 'run',
            'parameters'    => json_encode([
                'run_limit'  => 100,
                'grace_days' => 0,
            ]),
            'run_frequency' => 'Daily',
            'is_active'     => 0,
        ],
    ],
];
