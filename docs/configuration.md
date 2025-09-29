# Configuration

Open **Administer → System Settings → Cash AutoPay Settings**.

## Main options
- **Payment instruments** (`cashautopay_payment_instruments`) — instruments to consider (e.g. *Cash*).
- **Run limit** (`cashautopay_run_limit`) — max items created per run.
- **Grace days** (`cashautopay_grace_days`) — days to subtract from the cut-off date.
- **Max catch-up cycles** (`cashautopay_max_catchup_cycles`) — cap per recurring series.

> Tip: Start with **grace=0** and a small **run limit** in testing.