<?php

declare(strict_types=1);

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

    // Daten aus JSON holen und normalisieren
    $safeCode = isset($data['safe_code']) ? trim((string)$data['safe_code']) : '';
    $safeLocation = isset($data['safe_location']) ? trim((string)$data['safe_location']) : '';
    $doorState = isset($data['door_state']) ? trim((string)$data['door_state']) : '';
    $safeStatus = isset($data['safe_status']) ? trim((string)$data['safe_status']) : '';
    $cashLevel = $data['cash_level'] ?? null;

    // Validierung sammeln und am ene entscheiden
    $errors = [];
    if ($safeCode === '') {
        $errors[] = 'safe_code ist erforderlich';
    }
    if ($safeLocation === '') {
        $errors[] = 'safe_location ist erforderlich';
    }
    if ($doorState === '') {
        $errors[] = 'door_state ist erforderlich';
    }
    if ($safeStatus === '') {
        $errors[] = 'safe_status ist erforderlich';
    }

    // sicherstellen das cash_level ein numerischerwert ist und wert auf zwei nachkommastellen normalisieren
    if (!is_int($cashLevel) && !is_float($cashLevel) && !is_string($cashLevel)) {
        $errors[] = 'cash_level ist erforderlich';
    } else {
        if (!is_numeric($cashLevel)) {
            $errors[] = 'cash_level muss numeric sein';
        } else {
            $cashLevel = number_format((float)$cashLevel, 2, '.', '');
            if ($cashLevel < 0) {
                $errors[] = 'cash_level muss >= 0 sein';
            }
        }
    }

    // wenn Fehler -> dann 400 zurück
    if ($errors) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'errors' => $errors]);
        exit;
    }

    // Verbindung + Aufruf des SafeRepositorys
    try {
        require __DIR__ . '/../src/Config/Database.php';
        $pdo = (new Database())->connect();

        require __DIR__ . '/../src/Repository/SafeRepository.php';
        $repo = new SafeRepository($pdo);
        $repo->create($safeCode,$safeLocation,$cashLevel,$doorState,$safeStatus);

        

        // Mit Erfolg antworten ,201 steht für Created
        http_response_code(201);
        echo json_encode([
            'ok' => true,
            'created' => [
                'safe_code' => $safeCode,
                'safe_location' => $safeLocation,
                'cash_level' => $cashLevel,
                'door_state' => $doorState,
                'safe_status' => $safeStatus
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
