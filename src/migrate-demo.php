<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    exit(1);
}

require_once __DIR__ . '/db_connection.php';

try {
    $sql = file_get_contents(__DIR__ . '/demo-schema.sql');
    if ($sql === false) {
        throw new RuntimeException('Migration file is missing.');
    }
    $db = getDatabaseConnection();
    $db->beginTransaction();
    $db->query("SELECT pg_advisory_xact_lock(hashtext('mdrrmo classroom incident schema'))");
    $db->exec($sql);
    $db->query('SELECT postgis_version(), COUNT(*) FROM public.mdrrmo_classroom_incidents WHERE false');
    $db->commit();
    fwrite(STDOUT, "Classroom database connection, PostGIS and incident table verified.\n");
} catch (Throwable $error) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    $code = $error instanceof PDOException ? $error->getCode() : 'configuration';
    fwrite(STDERR, "Classroom database migration failed ($code).\n");
    exit(1);
}
