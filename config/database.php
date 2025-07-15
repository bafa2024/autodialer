<?php

namespace Config;

class Database
{
    private static $config = [
        'development' => [
            'host' => 'localhost',
            'port' => 3306,
            'database' => 'autodialer_dev',
            'username' => 'autodialer_user',
            'password' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ]
        ],
        'production' => [
            'host' => 'localhost',
            'port' => 3306,
            'database' => 'autodialer_prod',
            'username' => 'autodialer_user',
            'password' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ]
        ]
    ];

    public static function getConfig($environment = null)
    {
        $env = $environment ?? ($_ENV['APP_ENV'] ?? 'development');
        return self::$config[$env] ?? self::$config['development'];
    }

    public static function getDSN($environment = null)
    {
        $config = self::getConfig($environment);
        return "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
    }
} 