<?php
declare(strict_types=1);
/** MDRRMO PostgreSQL PDO connection helper: requires DATABASE_URL on Render. */
if (!function_exists('getDatabaseConnection')) {
    function getDatabaseConnection(): PDO {
        static $connection = null;
        if ($connection instanceof PDO) return $connection;
        $url = getenv('DATABASE_URL');
        if (!$url) throw new RuntimeException('Database configuration is unavailable.');
        $parsed = parse_url($url);
        if ($parsed === false || empty($parsed['host']) || empty($parsed['user']) || !isset($parsed['pass']) || empty($parsed['path']) || $parsed['path']==='/') {
            throw new RuntimeException('Database configuration is invalid.');
        }
        parse_str($parsed['query'] ?? '', $query);
        $sslmode = $query['sslmode'] ?? (getenv('PGSSLMODE') ?: 'require');
        if (!in_array($sslmode, ['require','verify-ca','verify-full'], true)) {
            throw new RuntimeException('Database SSL configuration is invalid.');
        }
        $host=$parsed['host'];$port=(int)($parsed['port']??5432);
        $name=rawurldecode(ltrim($parsed['path'],'/'));
        $user=rawurldecode($parsed['user']);$password=rawurldecode($parsed['pass']);
        $dsn=sprintf('pgsql:host=%s;port=%d;dbname=%s;sslmode=%s',$host,$port,$name,$sslmode);
        try {
            $connection=new PDO($dsn,$user,$password,[
                PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES=>false,
                PDO::ATTR_TIMEOUT=>10
            ]);
            return $connection;
        } catch(PDOException $e) {
            error_log('MDRRMO database connection failed: '.$e->getMessage());
            throw new RuntimeException('Database connection failed.',0,$e);
        }
    }
}
