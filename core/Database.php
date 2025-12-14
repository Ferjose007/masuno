<?php
namespace Core;

use PDO;

class Database {
    private static $instance = null;

    private function __construct() {}

    public static function getInstance() {
        if (!self::$instance) {
            $config = require __DIR__ . '/../config/database.php';
            self::$instance = new PDO(
                "mysql:host={$config['host']};dbname={$config['dbname']}",
                $config['user'],
                $config['pass']
            );
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$instance;
    }
}
