<?php
// CLI-only connection test: php test-db.php
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
require_once __DIR__ . '/src/db_connection.php';
try {
    mdrrmo_db()->query('SELECT 1');
    echo "Database connection successful\n";
} catch (Throwable $error) {
    fwrite(STDERR, "Connection failed. Check DB_HOST, DB_PORT, DB_NAME, DB_USER, and DB_PASSWORD.\n");
    exit(1);
}
