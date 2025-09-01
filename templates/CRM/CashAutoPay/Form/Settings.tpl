{crmScope extensionKey='de.systopia.cashautopay'}
  <div class="crm-block crm-form-block cashautopay-admin">

    <div class="cc-card">
      <h3 class="cc-title">{ts}Configuration{/ts}</h3>

      <table class="form-layout-compressed cc-form">
        <tr>
          <td class="label">
            {$form.cashautopay_payment_instruments.label}
            {help id="cashautopay_instruments"}
          </td>
          <td class="content">
            {$form.cashautopay_payment_instruments.html}
          </td>
        </tr>

        <tr>
          <td class="label">
            {$form.cashautopay_max_catchup_cycles.label}
            {help id="cashautopay_max_catch"}
          </td>
          <td class="content">
            {$form.cashautopay_max_catchup_cycles.html}
          </td>
        </tr>

        <tr>
          <td class="label">
            {$form.cashautopay_run_limit.label}
            {help id="cashautopay_run_limit"}
          </td>
          <td class="content">
            {$form.cashautopay_run_limit.html}
          </td>
        </tr>

        <tr>
          <td class="label">
            {$form.cashautopay_grace_days.label}
            {help id="cashautopay_grace"}
          </td>
          <td class="content">
            {$form.cashautopay_grace_days.html}
          </td>
        </tr>

{*        <tr>*}
{*          <td class="label">*}
{*            {$form.cashautopay_debug.label}*}
{*            {help id="cashautopay_debug"}*}
{*          </td>*}
{*          <td class="content">*}
{*            {$form.cashautopay_debug.html}*}
{*          </td>*}
{*        </tr>*}
      </table>
    </div>

    <div class="crm-submit-buttons">
      {include file="CRM/common/formButtons.tpl" location="bottom"}
    </div>
  </div>
{/crmScope}
