<?php
declare(strict_types=1);

namespace Civi\Api4\Action\AssumedPayments;

use Civi\Api4\Generic\AbstractAction;
use Civi\Api4\Generic\Result;

final class Run extends AbstractAction {

    public ?int $run_limit = null;
    public ?int $grace_days = null;
    public ?string $from_date = null;

    // Compat con wrappers antiguos
    public $setRun_limit = null;
    public $setGrace_days = null;
    public $setFrom_date = null;

    public function _run(Result $result) {
        $params = [];

        $runLimit = $this->run_limit !== null ? $this->run_limit : $this->setRun_limit;
        if ($runLimit !== null && $runLimit !== '') {
            $params['run_limit'] = (int) $runLimit;
        }

        $graceDays = $this->grace_days !== null ? $this->grace_days : $this->setGrace_days;
        if ($graceDays !== null && $graceDays !== '') {
            $params['grace_days'] = (int) $graceDays;
        }

        $fromDate = $this->from_date !== null ? $this->from_date : $this->setFrom_date;
        if ($fromDate !== null && $fromDate !== '') {
            $params['from_date'] = (string) $fromDate;
        }

        $runner = new \Civi\CashAutoPay\Service\Runner();
        $data = $runner->run($params);

        $result[] = $data;
    }
}
