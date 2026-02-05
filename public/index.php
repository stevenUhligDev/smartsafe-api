<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Steve\SmartsafeApi\Config\Database;
use Steve\SmartsafeApi\Repository\SafeRepository;
use Steve\SmartsafeApi\Model\SmartSafe;


// weiße Seiten vermeiden
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Client auf json vorbereiten
header('Content-Type: application/json; charset=utf-8');

// Methode und Pfad angeben + ggf Querystring rausfiltern 
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

// Health-Endpoint setzen
if ($method === 'GET' && $path === '/api/health') {
    http_response_code(200);
    echo json_encode(['ok' => true]);
    exit;
}

// wenn HTTP-Methode == POST, dann Body lesen und und in array packen
if ($method === 'POST' && $path === '/api/safes') {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);

    // prüfen ob json korrekt war
    if (!is_array($data)) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'Invalid JSON body']);
        exit;
    }

    $safe = SmartSafe::fromArray($data);
    $errors = $safe->validate();
    

    // wenn Fehler -> dann 400 zurück
    if ($errors) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'errors' => $errors]);
        exit;
    }

    // Verbindung + Aufruf des SafeRepositorys
    try {
        $pdo = (new Database())->connect();
        $repo = new SafeRepository($pdo);
        $repo->create(
            $safe->safeCode,
            $safe->safeLocation,
            $safe->cashLevel,
            $safe->doorState,
            $safe->safeStatus
        );

        

        // Mit Erfolg antworten ,201 steht für Created
        http_response_code(201);
        echo json_encode([
            'ok' => true,
            'created' => [
                'safe_code' => $safe->safeCode,
                'safe_location' => $safe->safeLocation,
                'cash_level' => $safe->cashLevel,
                'door_state' => $safe->doorState,
                'safe_status' => $safe->safeStatus
            ]
        ]);
        exit;
    } catch (PDOException $e) {
        // MSSQL Duplicate Key: 2601, 2627
        $sqlStat = $e->errorInfo[0] ?? null;
        $driverCode = $e->errorInfo[1] ?? null;

        if ($driverCode === 2601 || $driverCode === 2627) {
            http_response_code(409);
            echo json_encode([
                'ok' => false,
                'error' => 'safe_code existiert schon',
            ]);
            exit;
        }

        http_response_code(500);
        echo json_encode([
            'ok' => false,
            'error' => 'Database error',
            // nur für Entwicklung
            'details' => $e->getMessage(),
        ]);
        exit;
    } catch (Throwable $e) {
        // Serverproblem = 500
        http_response_code(500);
        echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
        exit;
    }
}

http_response_code(404);
echo json_encode([
    'ok' => false,
    'error' => 'Not found',
    'method' => $method,
    'path' => $path
]);
