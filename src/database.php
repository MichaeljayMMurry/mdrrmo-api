<?php
/** PDO connection factory. Configure via environment variables on the server. */
function mdrrmo_db(): PDO {
    $host = getenv('DB_HOST') ?: 'localhost';
    $port = getenv('DB_PORT') ?: '5432';
    $dbname = getenv('DB_NAME') ?: 'mdrrmo';
    $user = getenv('DB_USER');
    $password = getenv('DB_PASSWORD');
    if ($user === false || $password === false) {
        throw new RuntimeException('Database credentials are not configured');
    }
    $dsn = "pgsql:host={$host};port={$port};dbname={$dbname};sslmode=require";
    return new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
}
