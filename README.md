# Cash AutoPay (de.systopia.cashautopay)

Automate the creation of **assumed cash** contributions for selected **recurring contributions** (`ContributionRecur`).

This extension provides:

- A planner/runner that determines due installments and **creates** the matching **Contribution + Payment** (idempotent).
- **Admin UI** under **Administer → System Settings → Cash AutoPay Settings**.
- **API v3** endpoints: `CashAutoPay.preview` and `CashAutoPay.run`.
- A **managed Scheduled Job** “CashAutoPay: Run”.

Typical use-case: members pay their dues in cash (or another offline method). You still want individual monthly/quarterly/etc. contributions recorded, even without a processor callback.

---

## Requirements

- CiviCRM **6.1+**
- PHP compatible with your CiviCRM version
- A payment instrument (OptionValue in group `payment_instrument`) that represents cash (e.g. `Cash` or `Cash (auto)`)

---

## Installation

1. Place the extension directory here:

   ```
   [civicrm.files]/ext/de.systopia.cashautopay
   ```

   On many sites, that is:
   ```
   web/sites/default/files/civicrm/ext/de.systopia.cashautopay
   ```

2. Enable:

  - **UI:** Administer → System Settings → Extensions → enable **de.systopia.cashautopay**
  - **CLI:**
    ```bash
    cv ext:enable de.systopia.cashautopay
    cv flush
    ```

3. Navigate to **Administer → System Settings → Cash AutoPay Settings**.

---

## Configuration (UI)

Open **Administer → System Settings → Cash AutoPay Settings**:

- **Payment instruments to automate**
  Which `payment_instrument_id` values should be auto-generated.
  *Example:* select **Cash**. If you use a dedicated value like **Cash (auto)**, select that instead.

- **Max catch-up cycles per recurrence (0 = unlimited)**
  How many **missed installments** to create for a single recurrence in one run.
  *Example:* monthly plan 10 months behind; with **3**, it creates **3** now and the rest in later runs.

- **Run limit (0 = unlimited)**
  Global cap on the number of contributions created per execution (summing all recurrings).
  *Example:* **200** means at most 200 contributions per run.

- **Grace days**
  Days **subtracted** from the planning date (`date_to`). Avoids creating the current period too early.
  *Example:* `date_to=2025-09-30` and `grace_days=3` ⇒ effective planning date `2025-09-27`.

- **Enable debug logging** (optional)
  Writes extra lines to the CiviCRM log during preview/run. Use for testing/troubleshooting.

> Non-monthly frequencies (weekly, quarterly, yearly, etc.) are supported. The engine advances by each recurrence’s `frequency_unit` and `frequency_interval`.

---

## How it plans and creates

- Scans **active** `ContributionRecur` with selected **payment_instrument_id(s)`.
- Plans all due periods **up to** `date_to - grace_days`.
- Enforces **Max catch-up cycles** (per recurrence) and **Run limit** (global).
- Creation is **idempotent** using `Contribution.source = "cashautopay:YYYY-MM-DD"`.
  Existing contributions with the same `source` are **not** duplicated.

---

## API (CLI) Usage

Run with **cv**. If you omit parameters, saved settings are used.

### Preview (no writes)

- Using saved settings:
  ```bash
  cv api CashAutoPay.preview
  ```
- With parameters:
  ```bash
  cv api CashAutoPay.preview payment_instrument_ids='[3]' date_to='2030-01-01' limit=10
  ```

**Expected shape:**
```json
{
  "is_error": 0,
  "values": [
    {
      "total": 10,
      "items": [
        {
          "recur_id": 42,
          "contact_id": 314,
          "amount": 10,
          "currency": "EUR",
          "financial_type_id": 2,
          "payment_instrument_id": 3,
          "receive_date": "2026-01-04",
          "period_key": "2026-01-04",
          "next_sched_contribution_date": "2026-02-04"
        }
      ]
    }
  ]
}
```

### Run (creates Contribution + Payment)

- Using saved settings:
  ```bash
  cv api CashAutoPay.run
  ```
- With parameters:
  ```bash
  cv api CashAutoPay.run payment_instrument_ids='[3]' date_to='2030-01-01' limit=5
  ```

**Expected shape:**
```json
{
  "is_error": 0,
  "values": [
    {
      "total_created": 5,
      "created": [
        { "contribution_id": 18, "payment_id": 56, "recur_id": 42, "period_key": "2025-06-04" }
      ],
      "errors": []
    }
  ]
}
```

### Verifying results

```bash
# Contributions created by CashAutoPay
cv api4 Contribution.get   select='["id","source","receive_date","contribution_recur_id"]'   where='[["source","LIKE","cashautopay:%"]]'   limit=20

# Optional: review payments (available fields vary by CiviCRM version)
cv api4 Payment.get limit=20
```

---

## Scheduled Job (Cron)

The extension includes a managed job **“CashAutoPay: Run”**.

Enable/adjust under **Administer → System Settings → Scheduled Jobs**:

- **Enable** the job
- Set **Run frequency** (e.g. **Daily**)
- **Parameters** (optional). Leave blank to use saved settings, or override:
  ```
  payment_instrument_ids=[3]&limit=500&grace_days=3&max_catchup_cycles=6
  ```

### Running the job via CLI

- Find the job ID:
  ```bash
  cv api Job.get name='CashAutoPay: Run' return=id
  ```
- Execute manually:
  ```bash
  cv api Job.execute id=<ID>
  ```
- Example cron entry (daily at 03:30):
  ```
  30 3 * * * cd /path/to/site && /path/to/cv api Job.execute id=<ID> >/dev/null 2>&1
  ```

### Running via HTTP (alternative)

```
https://example.org/sites/default/civicrm/bin/cron.php?entity=Job&action=execute&id=<ID>&key=<site_key>&name=<cms_user>&pass=<cms_pass>
```

Replace with your site key and a valid CMS user.

---

## Logging & Troubleshooting

- Turn on **Enable debug logging** in settings, then run a preview or run.
- Recent log file:
  ```
  [civicrm.files]/ConfigAndLog/CiviCRM.*.log
  ```
- Quick grep:
  ```bash
  LOGDIR=$(cv ev 'echo Civi::paths()->getPath("[civicrm.files]/ConfigAndLog");')
  LOGFILE=$(ls -1t "$LOGDIR"/CiviCRM*.log* | head -n1)
  grep -a -n 'cashautopay' "$LOGFILE" | tail -n 80
  ```
- If compressed, use `zgrep`. Some stacks may route logs to the CMS logger (e.g. Drupal dblog).

---

## Edge Cases & Notes

- **Frequencies:** monthly, quarterly, yearly, etc. are supported.
- **Ended recurrings:** nothing is created past `end_date`.
- **Idempotency:** `Contribution.source = cashautopay:YYYY-MM-DD`.
- **Caps:** `max_catchup_cycles` is per recurrence; `run_limit` is global.
- **Permissions:** settings page requires **administer CiviCRM**.

---

## Uninstall / Disable

- Disable the job in **Scheduled Jobs** if not needed.
- Disable the extension (UI) or:
  ```bash
  cv ext:disable de.systopia.cashautopay
  ```
- Remove the directory/symlink from `[civicrm.files]/ext`.

---

## FAQ

**Why did it create fewer items than expected?**
Check `max_catchup_cycles`, `run_limit`, and `grace_days`. Also confirm the recurrence’s frequency and dates produce the expected periods up to `date_to`.

**Can I target only some cash recurrings?**
Yes. Create a distinct payment instrument (e.g. **Cash (auto)**), assign it to those recurrings, and select only that instrument in settings.

**APIv4?**
This extension exposes v3 endpoints for stability. A v4 mirror can be added later if desired.

---

## Support & Contributing

Issues and contributions are welcome. Please include:

- CiviCRM version
- Extension version
- CLI command (with parameters)
- Log snippets (with **debug** enabled)

**License:** AGPL-3.0
