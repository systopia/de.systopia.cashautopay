<?php
namespace Civi\Api4\Action\CashAutoPay;
use Civi\Api4\Generic\AbstractAction;
use Civi\Api4\Generic\Result;
use CRM_CashAutoPay_Service_Runner as Runner;
class Run extends AbstractAction {
  protected function _run(Result $result) {
    $runner = new Runner();
    $out = $runner->run([
      'payment_instrument_ids' => $this->getParam('payment_instrument_ids'),
      'date_to' => $this->getParam('date_to'),
      'limit' => $this->getParam('limit'),
    ]);
    $result->push($out);
  }
}
