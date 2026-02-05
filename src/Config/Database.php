<?php
declare(strict_types=1);

namespace Steve\SmartsafeApi\Config;
use PDO;
use Throwable;

final class Database
{
    
    private string $server;
    private string $database;
    private string $user;
    private string $password;

    public function __construct() {
        $config = require __DIR__ . '/config.local.php';

        $this->server = $config['server'];
        $this->database = $config['database'];
        $this->user = $config['user'];
        $this->password = $config['password'];
    }

    public function connect(): PDO
    {
        $dsn = "sqlsrv:Server={$this->server};Database={$this->database}";
        try {
            $pdo = new PDO(
                $dsn,
                $this->user,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
            return $pdo;
        } catch (Throwable $e) {
            throw $e;
        }
    }
}



