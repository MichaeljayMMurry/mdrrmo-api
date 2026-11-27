<?php
declare(strict_types=1);
/** Legacy-compatible wrapper for the classroom database.php file. */
require_once __DIR__.'/db_connection.php';
if (!function_exists('mdrrmo_db')) {
    function mdrrmo_db(): PDO { return getDatabaseConnection(); }
}
