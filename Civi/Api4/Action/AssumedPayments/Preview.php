<?php
declare(strict_types=1);

namespace Civi\Api4\Action\AssumedPayments;

use Civi\Api4\Generic\AbstractAction;

/**
 * APIv4 Action: AssumedPayments.preview
 */
final class Preview extends AbstractAction {

    public $run_limit = null;
    public $grace_days = null;
    public $from_date = null;
    public $debug = null;

    public $setRun_limit = null;
    public $setGrace_days = null;
    public $setFrom_date = null;
    public $setDebug = null;

    public function _run($result) {
        $params = [];

        $runLimit = ($this->run_limit !== null) ? $this->run_limit : $this->setRun_limit;
        if ($runLimit !== null && $runLimit !== '') {
            $params['run_limit'] = (int) $runLimit;
        }

        $graceDays = ($this->grace_days !== null) ? $this->grace_days : $this->setGrace_days;
        if ($graceDays !== null && $graceDays !== '') {
            $params['grace_days'] = (int) $graceDays;
        }

        $fromDate = ($this->from_date !== null) ? $this->from_date : $this->setFrom_date;
        if ($fromDate !== null && $fromDate !== '') {
            $params['from_date'] = (string) $fromDate;
        }

        $dbg = ($this->debug !== null) ? $this->debug : $this->setDebug;
        if ($dbg !== null && $dbg !== '') {
            $params['debug'] = filter_var($dbg, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($params['debug'] === null) {
                $params['debug'] = (bool) $dbg;
            }
        }

        $runner = new \Civi\CashAutoPay\Service\Runner();
        $data = $runner->preview($params);

        $result[] = $data;
    }
}
