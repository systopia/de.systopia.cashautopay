{crmScope extensionKey='assumed-payments'}
  <div class="crm-block crm-form-block assumed-payments-admin">

    <div class="cc-card">
      <h3 class="cc-title">{ts}Configuration{/ts}</h3>

      <table class="form-layout-compressed cc-form">
        <tr>
          <td class="label">
            {$form.assumed_payments_payment_instruments.label}
            {help id="assumed_payments_instruments"}
          </td>
          <td class="content">
            {$form.assumed_payments_payment_instruments.html}
          </td>
        </tr>

        <tr>
          <td class="label">
            {$form.assumed_payments_max_catchup_cycles.label}
            {help id="assumed_payments_max_catch"}
          </td>
          <td class="content">
            {$form.assumed_payments_max_catchup_cycles.html}
          </td>
        </tr>

        <tr>
          <td class="label">
            {$form.assumed_payments_run_limit.label}
            {help id="assumed_payments_run_limit"}
          </td>
          <td class="content">
            {$form.assumed_payments_run_limit.html}
          </td>
        </tr>

        <tr>
          <td class="label">
            {$form.assumed_payments_grace_days.label}
            {help id="assumed_payments_grace"}
          </td>
          <td class="content">
            {$form.assumed_payments_grace_days.html}
          </td>
        </tr>

{*        <tr>*}
{*          <td class="label">*}
{*            {$form.assumed_payments_debug.label}*}
{*            {help id="assumed_payments_debug"}*}
{*          </td>*}
{*          <td class="content">*}
{*            {$form.assumed_payments_debug.html}*}
{*          </td>*}
{*        </tr>*}
      </table>
    </div>

    <div class="crm-submit-buttons">
      {include file="CRM/common/formButtons.tpl" location="bottom"}
    </div>
  </div>
{/crmScope}
