<?php
declare(strict_types = 1);

namespace Civi\Api4\Action\AssumedPayments;

use Civi\Api4\Generic\AbstractAction;
use Civi\Api4\Generic\Result;

final class Run extends AbstractAction {

  public ?int $run_limit = NULL;
  public ?int $grace_days = NULL;
  public ?string $from_date = NULL;

  // Compat con wrappers antiguos
  public $setRun_limit = NULL;
  public $setGrace_days = NULL;
  public $setFrom_date = NULL;

  public function _run(Result $result) {
    $params = [];

    $runLimit = $this->run_limit !== NULL ? $this->run_limit : $this->setRun_limit;
    if ($runLimit !== NULL && $runLimit !== '') {
      $params['run_limit'] = (int) $runLimit;
    }

    $graceDays = $this->grace_days !== NULL ? $this->grace_days : $this->setGrace_days;
    if ($graceDays !== NULL && $graceDays !== '') {
      $params['grace_days'] = (int) $graceDays;
    }

    $fromDate = $this->from_date !== NULL ? $this->from_date : $this->setFrom_date;
    if ($fromDate !== NULL && $fromDate !== '') {
      $params['from_date'] = (string) $fromDate;
    }

    $runner = new \Civi\AssumedPayments\Service\Runner();
    $data = $runner->run($params);

    $result[] = $data;
  }

}
