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

namespace Civi\CashAutoPay\Service;

use Civi\Api4\ContributionRecur;
use Civi\Api4\Contribution;
use Civi\Api4\Payment;

final class Runner {

    public function preview($params = []) {
        $plan = $this->plan($params);
        return ['total' => count($plan['to_create']), 'items' => $plan['to_create']];
    }

    public function run($params = []) {
        $plan = $this->plan($params);
        $created = [];
        $errors = [];
        $limit = $plan['limit'];

        foreach ($plan['to_create'] as $item) {
            if ($limit !== null && count($created) >= $limit) break;

            try {
                $contrib = Contribution::create(FALSE)
                    ->setValues([
                        'contact_id'             => $item['contact_id'],
                        'financial_type_id'      => $item['financial_type_id'],
                        'total_amount'           => $item['amount'],
                        'currency'               => $item['currency'],
                        'receive_date'           => $item['receive_date'],
                        'contribution_recur_id'  => $item['recur_id'],
                        'payment_instrument_id'  => $item['payment_instrument_id'],
                        'source'                 => 'cashautopay:' . $item['period_key'],
                    ])
                    ->execute()
                    ->first();

                $payment = Payment::create(FALSE)
                    ->setValues([
                        'contribution_id'        => $contrib['id'],
                        'total_amount'           => $item['amount'],
                        'trxn_date'              => $item['receive_date'],
                        'payment_instrument_id'  => $item['payment_instrument_id'],
                    ])
                    ->execute()
                    ->first();

                $created[] = [
                    'contribution_id' => (int) $contrib['id'],
                    'payment_id'      => (int) $payment['id'],
                    'recur_id'        => (int) $item['recur_id'],
                    'period_key'      => $item['period_key'],
                ];

                ContributionRecur::update(FALSE)
                    ->addWhere('id', '=', $item['recur_id'])
                    ->addValue('next_sched_contribution_date', $item['next_sched_contribution_date'])
                    ->execute();

            } catch (\Throwable $e) {
                $errors[] = [
                    'recur_id'   => (int) $item['recur_id'],
                    'period_key' => $item['period_key'],
                    'message'    => $e->getMessage(),
                ];
            }
        }

        return ['total_created' => count($created), 'created' => $created, 'errors' => $errors];
    }

    private function plan($params) {
        $settings = \Civi::settings();

        $idsParam = $params['payment_instrument_ids'] ?? null;
        $ids = $idsParam !== null ? $this->normalizeIds($idsParam) : (array) $settings->get('cashautopay_payment_instruments');

        $dateTo = isset($params['date_to']) && $params['date_to'] ? new \DateTime($params['date_to']) : new \DateTime('now');
        $grace = (int) ($settings->get('cashautopay_grace_days') ?? 0);
        if ($grace > 0) $dateTo->modify('-' . $grace . ' days');

        $limit = isset($params['limit']) && $params['limit'] !== null ? (int) $params['limit'] : ($settings->get('cashautopay_run_limit') ?: null);

        $toCreate = [];
        if (empty($ids)) return ['to_create' => [], 'limit' => $limit];

        $recurList = ContributionRecur::get(FALSE)
            ->addSelect('id','contact_id','amount','currency','financial_type_id','payment_instrument_id','frequency_unit','frequency_interval','start_date','next_sched_contribution_date','end_date')
            ->addWhere('payment_instrument_id', 'IN', array_map('intval', $ids))
            ->execute();

        foreach ($recurList as $r) {
            $baseDate = $r['next_sched_contribution_date'] ?: $r['start_date'];
            if (!$baseDate) continue;

            $start = new \DateTime($baseDate);
            $end   = $r['end_date'] ? new \DateTime($r['end_date']) : null;

            $unit     = $r['frequency_unit'];
            $interval = (int) $r['frequency_interval'];
            if ($interval < 1) $interval = 1;

            $cursor = clone $start;
            while ($cursor <= $dateTo) {
                if ($end && $cursor > $end) break;

                $periodKey = $cursor->format('Y-m-d');

                $exists = Contribution::get(FALSE)
                    ->addSelect('id')
                    ->addWhere('contribution_recur_id', '=', $r['id'])
                    ->addWhere('source', '=', 'cashautopay:' . $periodKey)
                    ->setLimit(1)
                    ->execute()
                    ->first();

                if (!$exists) {
                    $nextDate = $this->addInterval($cursor, $unit, $interval);
                    $toCreate[] = [
                        'recur_id'                       => $r['id'],
                        'contact_id'                     => $r['contact_id'],
                        'amount'                         => $r['amount'],
                        'currency'                       => $r['currency'],
                        'financial_type_id'              => $r['financial_type_id'],
                        'payment_instrument_id'          => $r['payment_instrument_id'],
                        'receive_date'                   => $cursor->format('Y-m-d'),
                        'period_key'                     => $periodKey,
                        'next_sched_contribution_date'   => $nextDate->format('Y-m-d'),
                    ];
                }

                $cursor = $this->addInterval($cursor, $unit, $interval);
                if ($limit !== null && count($toCreate) >= $limit) break 2;
            }
        }

        $maxCatch = (int) ($settings->get('cashautopay_max_catchup_cycles') ?? 0);
        if ($maxCatch > 0) {
            $grouped = [];
            foreach ($toCreate as $t) { $grouped[$t['recur_id']][] = $t; }
            $limited = [];
            foreach ($grouped as $items) { $limited = array_merge($limited, array_slice($items, 0, $maxCatch)); }
            $toCreate = $limited;
        }

        return ['to_create' => $toCreate, 'limit' => $limit];
    }

    private function addInterval(\DateTime $d, $unit, $n) {
        $d2 = clone $d;
        if ($unit === 'month') return $d2->modify('+' . $n . ' month');
        if ($unit === 'year')  return $d2->modify('+' . $n . ' year');
        if ($unit === 'week')  return $d2->modify('+' . $n . ' week');
        return $d2->modify('+' . $n . ' day');
    }

    private function normalizeIds($v) {
        if (is_array($v)) {
            return array_values(array_filter(array_map('intval', $v), function($x){ return $x !== 0 || $x === 0; }));
        }
        if (is_scalar($v)) {
            $s = trim((string) $v);
            if ($s === '') return [];
            if ($s[0] === '[') {
                $arr = json_decode($s, true);
                if (is_array($arr)) return array_values(array_filter(array_map('intval', $arr)));
            }
            if (strpos($s, ',') !== false) {
                return array_values(array_filter(array_map('intval', array_map('trim', explode(',', $s)))));
            }
            return [intval($s)];
        }
        return [];
    }
}
