# Examples

Each file has its own inputs at the top. You can open the script, fill in the placeholders or edit them in the .env file.

Shared request helpers live in [functions.php](functions.php).

Install the PSR client implementations used by the examples:

```bash
composer require --dev symfony/http-client nyholm/psr7
```

Optional local .env file:

```bash
cp examples/.env.dist examples/.env
```

Then edit `examples/.env`. Scripts still allow per-file placeholders, and the supported variable names are listed in `examples/.env.dist`.

## Run

```bash
php examples/payments/13_happy_path.php
php examples/subscriptions/08_happy_path.php
php examples/unscheduled_subscriptions/09_happy_path.php
```

Replace the placeholders at the top of the file, and follow the comments in code for the exact flow.

## What's Included

- `examples/payments`: hosted and embedded payments, retrieval, updates, charge and refund flows.
- `examples/subscriptions`: scheduled subscription flows, bulk charge, bulk verification, and happy path.
- `examples/unscheduled_subscriptions`: unscheduled subscription retrieval, charging, bulk actions, verification, and happy path.

Copy values between steps by editing the next script or define shared values in `examples/.env`.