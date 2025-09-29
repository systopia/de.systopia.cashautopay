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

use Civi\Api4\OptionValue;
class CRM_CashAutoPay_Form_Settings extends CRM_Core_Form {

  public function buildQuickForm() {
    CRM_Utils_System::setTitle(ts('Cash AutoPay Settings'));

    $options = [];

    try {
      $rows = OptionValue::get(FALSE)
          ->addSelect('value', 'label')
          ->addWhere('option_group_id:name', '=', 'payment_instrument')
          ->setLimit(0)
          ->execute();

      foreach ($rows as $row) {
          $options[(int) $row['value']] = (string) $row['label'];
      }
    } catch (\Throwable $e) {
    }

    $this->add('select',
      'cashautopay_payment_instruments',
      ts('Payment instruments to automate'),
      $options,
      TRUE,
      ['class' => 'crm-select2 huge', 'multiple' => TRUE]
    );

    $this->add('text', 'cashautopay_max_catchup_cycles', ts('Max catch-up cycles per recurrence (0 = unlimited)'));
    $this->addRule('cashautopay_max_catchup_cycles', ts('Must be a non-negative integer.'), 'integer');

    $this->add('text', 'cashautopay_run_limit', ts('Run limit (0 = unlimited)'));
    $this->addRule('cashautopay_run_limit', ts('Must be a non-negative integer.'), 'integer');

    $this->add('text', 'cashautopay_grace_days', ts('Grace days'));
    $this->addRule('cashautopay_grace_days', ts('Must be a non-negative integer.'), 'integer');

    $this->add('checkbox', 'cashautopay_debug', ts('Enable debug logging'));

    $this->addButtons([
      ['type' => 'next',   'name' => ts('Save'),   'isDefault' => TRUE],
      ['type' => 'cancel', 'name' => ts('Cancel')],
    ]);

    $this->setDefaults($this->getDefaultValues());
    $this->addFormRule([__CLASS__, 'validateValues']);
    $this->assign('helpText', $this->getHelpText());

  }

  protected function getDefaultValues(): array {
    $s = Civi::settings();
    return [
      'cashautopay_payment_instruments' => (array) $s->get('cashautopay_payment_instruments') ?: [],
      'cashautopay_max_catchup_cycles'  => (int) ($s->get('cashautopay_max_catchup_cycles') ?? 2),
      'cashautopay_run_limit'           => (int) ($s->get('cashautopay_run_limit') ?? 500),
      'cashautopay_grace_days'          => (int) ($s->get('cashautopay_grace_days') ?? 3),
      'cashautopay_debug'               => (int) ($s->get('cashautopay_debug') ?? 0),
    ];
  }


  /**
  * @param array<string,mixed> $values
  * @return true|array<string,string>
  */
  public static function validateValues(array $values): true|array {
    $errors = [];
    $inst = isset($values['cashautopay_payment_instruments']) ? (array) $values['cashautopay_payment_instruments'] : [];
    if (empty($inst)) {
      $errors['cashautopay_payment_instruments'] = ts('Select at least one payment instrument.');
    }
    foreach (['cashautopay_max_catchup_cycles','cashautopay_run_limit','cashautopay_grace_days'] as $k) {
      if ($values[$k] === '' || $values[$k] === NULL) continue;
      if (!is_numeric($values[$k]) || (int)$values[$k] < 0) {
        $errors[$k] = ts('Must be a non-negative integer.');
      }
    }
    return empty($errors) ? TRUE : $errors;
  }

  public function postProcess() {
    $values = $this->exportValues();
    $instruments = array_values(array_unique(array_map('intval', (array) ($values['cashautopay_payment_instruments'] ?? []))));
    $maxCatch    = max(0, (int) ($values['cashautopay_max_catchup_cycles'] ?? 0));
    $runLimit    = max(0, (int) ($values['cashautopay_run_limit'] ?? 0));
    $graceDays   = max(0, (int) ($values['cashautopay_grace_days'] ?? 0));
    $debug       = (int) (!empty($values['cashautopay_debug']));

    $s = Civi::settings();
    $s->set('cashautopay_payment_instruments', $instruments);
    $s->set('cashautopay_max_catchup_cycles',  $maxCatch);
    $s->set('cashautopay_run_limit',           $runLimit);
    $s->set('cashautopay_grace_days',          $graceDays);
    $s->set('cashautopay_debug',               $debug);

    CRM_Core_Session::setStatus(ts('Settings saved.'), ts('Saved'), 'success');
    parent::postProcess();
  }

  protected function getHelpText(): string {
    $html  = '<p><strong>' . ts('How planning works') . '</strong></p>';
    $html .= '<ul>';
    $html .= '<li>' . ts('<code>date_to</code> is the planning horizon used by CLI/API and the scheduled job.') . '</li>';
    $html .= '<li>' . ts('<code>grace_days</code> is subtracted from <code>date_to</code> to compute the effective horizon.') . '</li>';
    $html .= '<li>' . ts('<code>max_catchup_cycles</code> limits missed periods created per recurrence (0 = unlimited).') . '</li>';
    $html .= '<li>' . ts('<code>run_limit</code> caps the total number of items created per execution (0 = unlimited).') . '</li>';
    $html .= '<li>' . ts('Idempotence uses <code>source=cashautopay:YYYY-MM-DD</code> to avoid duplicates.') . '</li>';
    $html .= '</ul>';
    $html .= '<p>' . ts('These settings are read by:') . '</p>';
    $html .= '<ul>';
    $html .= '<li>' . ts('API v3: <code>CashAutoPay.preview</code> and <code>CashAutoPay.run</code>.') . '</li>';
    $html .= '<li>' . ts('Scheduled Job: “CashAutoPay: Run” (frequency configurable in Scheduled Jobs).') . '</li>';
    $html .= '</ul>';
    return $html;
  }
}
