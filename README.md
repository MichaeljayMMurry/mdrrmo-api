# MDRRMO PHP API — classroom database setup

This ZIP contains a reconstructed, compatible classroom project based on the repository structure in the provided slides and PHP snippets. **It is not a verified byte-for-byte download of the teacher's QR code files.** It is also **not a production emergency reporting system**.

## Step 1 — Repository structure

```text
mdrrmo-api/
  public/
    index.php
    api/incidents/create.php
    api/incidents/list.php
    api/incidents/view.php
  src/
    db_connection.php
    database.php
  Dockerfile
  .dockerignore
  .gitignore
  .env.example
  test-db.php
  README.md
```

## Step 2 — Database configuration

- `src/db_connection.php`: creates a PostgreSQL PDO connection using `DATABASE_URL`, decodes URL credentials, enables SSL and hides detailed errors from public responses.
- `src/database.php`: compatibility wrapper for older files that call `mdrrmo_db()`.
- Set a real `DATABASE_URL` under **Render → your web service → Environment**, not inside GitHub.
- The `pdo_pgsql` driver is installed by the Dockerfile.
- `test-db.php` is CLI-only. It returns a success/failure message without revealing credentials.

## Deployment

1. Open the existing repository `MichaeljayMMurry/mdrrmo-api` and upload the contents of this folder while keeping the paths above. Replace the same-named files after comparing changes.
2. On Render, ensure `mdrrmo-api` points at the correct GitHub repository and branch, with Docker runtime.
3. Create or connect an approved PostgreSQL database, then set `DATABASE_URL` securely in Render Environment.
4. Deploy and verify `/` shows `status: starter`. Use the Render logs to diagnose deployment errors. Do not paste the database URL publicly.

## Important scope

The incident endpoints intentionally return HTTP 501. They must not accept or publish real emergency reports before verified authorization, staff access control, abuse protection, audit trails, operational procedures, and approved database schema exist. The snippets supplied in chat differ in paths and schemas; copying them without reconciliation would not be reliable. The separate Hatchable classroom website retains a non-submitting preview form.
