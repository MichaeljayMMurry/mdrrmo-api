# MDRRMO API — PHP REST API Starter

Educational scaffold for the **MDRRMO Constitutes Website**. This project follows the repository structure shown in the Step 1 classroom slide. **It is not a live emergency response system.** The incidents endpoints intentionally return HTTP 501 and do not accept, store, or display incident data until security, authorization, verification, and operational procedures are approved by the relevant MDRRMO office.

## Project structure

```text
mdrrmo-api/
├── public/
│   ├── index.php
│   └── api/
│       └── incidents/
│           ├── create.php
│           ├── list.php
│           └── view.php
├── src/
│   ├── database.php
│   └── db_connection.php
├── .dockerignore
├── .gitignore
├── .env.example
├── Dockerfile
├── README.md
└── test-db.php
```

## GitHub classroom step

1. At [github.com/new](https://github.com/new), create a repository named `mdrrmo-api` under **your own account**.
2. Select **Private** unless your instructor explicitly requires Public, since the API project may eventually include sensitive development work. Never commit passwords or personal emergency data.
3. Add repository files using **Add file → Upload files**, preserving the directory paths, or use Git locally.
4. Commit the files. Verify the tree looks like the slide.

### Git commands (optional)

```sh
git init
git add .
git commit -m "Initial MDRRMO PHP REST API scaffold"
git branch -M main
git remote add origin https://github.com/YOUR_USERNAME/mdrrmo-api.git
git push -u origin main
```

## Local testing

Use PHP 8.3+ and the PDO PostgreSQL driver. With PHP's built-in server:

```sh
php -S localhost:8000 -t public
```

Open `http://localhost:8000`. The incident endpoints are placeholders and respond with 501 until an approved secure implementation exists.

## Database configuration

Environment variables: `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`. Do not commit your actual values. Set them in the hosting provider's secret/environment settings. `test-db.php` is CLI-only and checks the configured PostgreSQL connection.

## Render note

The Dockerfile runs Apache with PHP and the PostgreSQL PDO extension. Build using a **Render Web Service** with Docker, then configure required environment variables in Render. **Do not use this API as a real emergency dispatch endpoint.** It needs a documented authorization and incident verification workflow, abuse protection, audit trails, secure handling of personal/location data, backups and appropriate real responder integration before any production use.
