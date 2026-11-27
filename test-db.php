<?php
declare(strict_types=1);
/** Run from the command line: php test-db.php; never expose database diagnostics on the web. */
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__.'/src/db_connection.php';
try {
    getDatabaseConnection()->query('SELECT 1');
    echo "Database connection successful\n";
} catch (Throwable $e) {
    fwrite(STDERR,"Database connection failed. Check the Render DATABASE_URL environment variable.\n");
    exit(1);
}
