# Getting started

## Requirements
- CiviCRM 6.x (or compatible APIv4 build)
- PHP 8.1+

## Installation
1. Place **`de.systopia.cashautopay`** into your extensions directory.
2. Enable the extension in **Administer → System Settings → Extensions**.
3. Clear caches:
   ```bash
   cv flush
   cv api4 AssumedPayments.getActions
   cv api4 AssumedPayments.preview run_limit=5
