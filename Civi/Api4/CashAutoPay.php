<?php
namespace Civi\Api4;
use Civi\Api4\Generic\AbstractEntity;
class CashAutoPay extends AbstractEntity {
  public static function getFields() {
    $getter = function() {
      return [
        ['name' => 'recur_id', 'title' => 'ContributionRecur ID', 'data_type' => 'Integer', 'readonly' => TRUE],
        ['name' => 'contact_id', 'title' => 'Contact ID', 'data_type' => 'Integer', 'readonly' => TRUE],
        ['name' => 'financial_type_id', 'title' => 'Financial Type ID', 'data_type' => 'Integer', 'readonly' => TRUE],
        ['name' => 'amount', 'title' => 'Amount', 'data_type' => 'Money', 'readonly' => TRUE],
        ['name' => 'currency', 'title' => 'Currency', 'data_type' => 'String', 'readonly' => TRUE],
        ['name' => 'receive_date', 'title' => 'Receive Date', 'data_type' => 'Timestamp', 'readonly' => TRUE],
        ['name' => 'payment_instrument_id', 'title' => 'Payment Instrument', 'data_type' => 'Integer', 'readonly' => TRUE],
        ['name' => 'period_key', 'title' => 'Period Key', 'data_type' => 'String', 'readonly' => TRUE],
        ['name' => 'contribution_id', 'title' => 'Contribution ID', 'data_type' => 'Integer', 'readonly' => TRUE],
        ['name' => 'payment_id', 'title' => 'Payment ID', 'data_type' => 'Integer', 'readonly' => TRUE],
      ];
    };
    return new \Civi\Api4\Generic\BasicGetFieldsAction(static::getEntityName(), __FUNCTION__, $getter);
  }
  public static function permissions() {
    return [
      'run' => ['administer CiviCRM'],
      'preview' => ['administer CiviCRM'],
    ];
  }
}
