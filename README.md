# Barangay Sumadat — Secretary System (CodeIgniter 4 + Bootstrap 5)

A CodeIgniter 4 rebuild of the Barangay Sumadat secretary system, styled with
Bootstrap 5. No database server required — records are kept as plain PHP
arrays and persisted to a single JSON file (`writable/data/db.json`), the
same way the original prototype used `localStorage`.

## Requirements

- PHP 8.2+
- The `intl` and `mbstring` extensions (bundled with most PHP installs)

No Composer install and no database setup needed — the framework (`system/`)
is already included, and data lives in a JSON file that's created
automatically on first run.

## Running it

From the project root:

```bash
php spark serve
```

Then open the URL it prints (defaults to `http://localhost:8080`).

Alternatively, point PHP's built-in server at the `public/` folder:

```bash
php -S localhost:8080 -t public
```

Or deploy `public/` as the web root on Apache/Nginx like any normal CI4 app.

## Demo accounts

- **admin / admin123**
- **secretary / secretary123**

You can also create a new account from the "Create Account" tab on the
login screen.

## What's inside

- `app/Libraries/DataStore.php` — the array/JSON "database" (no SQL).
- `app/Controllers/Auth.php` — login, registration, logout.
- `app/Controllers/Barangay.php` — dashboard, reports, backup/restore, and
  one generic controller (index/store/update/delete) that drives every
  module (residents, households, certificates, requests, complaints/blotter,
  meetings, documents, payments, officials, users) off a single field
  schema — add a module by adding an entry to `schemas()`.
- `app/Views/barangay/module.php` — the generic list + add/edit modal +
  delete view shared by every module above.
- `writable/data/db.json` — created automatically; delete it (or use
  Backup & Restore → Reset) to go back to the demo dataset.

## Backup & Restore

Under **Backup & Restore** in the sidebar you can download the entire
dataset as a JSON file, restore from a previously exported file, or reset
back to the demo data.
