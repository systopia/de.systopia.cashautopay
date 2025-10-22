# Configuration

Open **Administer → System Settings → Assumed Payments Settings**.

## Main options
- **Payment instruments** (`assumed_payments_payment_instruments`) — instruments to consider (e.g. *Cash*).
- **Run limit** (`assumed_payments_run_limit`) — max items created per run.
- **Grace days** (`assumed_payments_grace_days`) — days to subtract from the cut-off date.
- **Max catch-up cycles** (`assumed_payments_max_catchup_cycles`) — cap per recurring series.

> Tip: Start with **grace=0** and a small **run limit** in testing.
