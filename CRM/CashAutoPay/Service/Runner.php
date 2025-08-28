<?php
use Civi\Api4\ContributionRecur;
use Civi\Api4\Contribution;
use Civi\Api4\Payment;
class CRM_CashAutoPay_Service_Runner {
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
        $contrib = Contribution::create(FALSE)->addValue([
          'contact_id' => $item['contact_id'],
          'financial_type_id' => $item['financial_type_id'],
          'total_amount' => $item['amount'],
          'currency' => $item['currency'],
          'receive_date' => $item['receive_date'],
          'contribution_recur_id' => $item['recur_id'],
          'payment_instrument_id' => $item['payment_instrument_id'],
          'source' => 'cashautopay:' . $item['period_key'],
          'status_id' => 'Completed',
        ])->execute()->first();
        $payment = Payment::create(FALSE)->addValue([
          'contribution_id' => $contrib['id'],
          'total_amount' => $item['amount'],
          'trxn_date' => $item['receive_date'],
          'payment_instrument_id' => $item['payment_instrument_id'],
        ])->execute()->first();
        $created[] = [
          'contribution_id' => (int) $contrib['id'],
          'payment_id' => (int) $payment['id'],
          'recur_id' => (int) $item['recur_id'],
          'period_key' => $item['period_key'],
        ];
        ContributionRecur::update(FALSE)
          ->addWhere('id', '=', $item['recur_id'])
          ->addValue('next_sched_contribution_date', $item['next_sched_contribution_date'])
          ->execute();
      } catch (\Throwable $e) {
        $errors[] = [
          'recur_id' => (int) $item['recur_id'],
          'period_key' => $item['period_key'],
          'message' => $e->getMessage(),
        ];
      }
    }
    return ['total_created' => count($created), 'created' => $created, 'errors' => $errors];
  }
  private function plan($params) {
    $settings = \Civi::settings();
    $ids = isset($params['payment_instrument_ids']) && $params['payment_instrument_ids'] !== null ? (array) $params['payment_instrument_ids'] : (array) $settings->get('cashautopay_payment_instruments');
    $dateTo = isset($params['date_to']) && $params['date_to'] ? new \DateTime($params['date_to']) : new \DateTime('now');
    $grace = (int) ($settings->get('cashautopay_grace_days') ?? 0);
    if ($grace > 0) $dateTo->modify('-' . $grace . ' days');
    $limit = isset($params['limit']) && $params['limit'] !== null ? (int) $params['limit'] : ($settings->get('cashautopay_run_limit') ?: null);
    $toCreate = [];
    if (empty($ids)) return ['to_create' => [], 'limit' => $limit];
    $recurList = ContributionRecur::get(FALSE)
      ->addSelect('id','contact_id','amount','currency','financial_type_id','payment_instrument_id','frequency_unit','frequency_interval','start_date','next_sched_contribution_date','end_date','status')
      ->addWhere('status', '=', 'Active')
      ->addWhere('payment_instrument_id', 'IN', $ids)
      ->execute();
    foreach ($recurList as $r) {
      $start = $r['next_sched_contribution_date'] ? new \DateTime($r['next_sched_contribution_date']) : new \DateTime($r['start_date']);
      $end = $r['end_date'] ? new \DateTime($r['end_date']) : null;
      $unit = $r['frequency_unit'];
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
            'recur_id' => $r['id'],
            'contact_id' => $r['contact_id'],
            'amount' => $r['amount'],
            'currency' => $r['currency'],
            'financial_type_id' => $r['financial_type_id'],
            'payment_instrument_id' => $r['payment_instrument_id'],
            'receive_date' => $cursor->format('Y-m-d'),
            'period_key' => $periodKey,
            'next_sched_contribution_date' => $nextDate->format('Y-m-d'),
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
    if ($unit === 'year') return $d2->modify('+' . $n . ' year');
    if ($unit === 'week') return $d2->modify('+' . $n . ' week');
    return $d2->modify('+' . $n . ' day');
  }
}
