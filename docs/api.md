# API (CLI / APIv4)

The extension exposes a virtual APIv4 entity **`AssumedPayments`** with the actions:
- `preview`
- `run`

## Examples (CLI)
```bash
# List actions
cv api4 AssumedPayments.getActions

# Preview (permissive)
cv api4 AssumedPayments.preview run_limit=50 grace_days=0 from_date=2020-01-01

# Run
cv api4 AssumedPayments.run run_limit=10 grace_days=0 from_date=2020-01-01
