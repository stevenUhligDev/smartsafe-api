<?php

/*declare(strict_types=1);

header('Content-Type: application/json');

$server = 'steven\SQLEXPRESS';
$database = 'smartsafe-monitoring';
$user = 'phpuser';
$password = 'PotPie234!';

$dsn = "sqlsrv:Server=$server;Database=$database";

try {
    $pdo = new PDO(
        $dsn,
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    $stmt = $pdo->query("SELECT GETDATE() AS now");
    $row = $stmt->fetch();

    $code = http_response_code(200);
    echo json_encode([
        'ok' => true,
        'code' => $code,
        'db-time' => $row['now']
    ]);
    
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'code' => $e->getCode(),
        'error' => $e->getMessage()
    ]);

}
*/