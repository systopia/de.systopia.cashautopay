<?php
declare(strict_types = 1);

namespace Civi\Api4\Action\AssumedPayments;

use Civi\Api4\Generic\AbstractAction;

/**
 * APIv4 Action: AssumedPayments.preview
 */
final class Preview extends AbstractAction {

  public $run_limit = NULL;
  public $grace_days = NULL;
  public $from_date = NULL;
  public $debug = NULL;

  public $setRun_limit = NULL;
  public $setGrace_days = NULL;
  public $setFrom_date = NULL;
  public $setDebug = NULL;

  public function _run($result) {
    $params = [];

    $runLimit = ($this->run_limit !== NULL) ? $this->run_limit : $this->setRun_limit;
    if ($runLimit !== NULL && $runLimit !== '') {
      $params['run_limit'] = (int) $runLimit;
    }

    $graceDays = ($this->grace_days !== NULL) ? $this->grace_days : $this->setGrace_days;
    if ($graceDays !== NULL && $graceDays !== '') {
      $params['grace_days'] = (int) $graceDays;
    }

    $fromDate = ($this->from_date !== NULL) ? $this->from_date : $this->setFrom_date;
    if ($fromDate !== NULL && $fromDate !== '') {
      $params['from_date'] = (string) $fromDate;
    }

    $dbg = $this->debug ?? $this->setDebug;
    if ($dbg !== NULL && $dbg !== '') {
      $params['debug'] = filter_var($dbg, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
      if ($params['debug'] === NULL) {
        $params['debug'] = (bool) $dbg;
      }
    }

    $runner = new \Civi\AssumedPayments\Service\Runner();
    $data = $runner->preview($params);

    $result[] = $data;
  }

}
