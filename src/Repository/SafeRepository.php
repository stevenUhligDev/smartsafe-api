<?php
declare(strict_types=1);

namespace Steve\SmartsafeApi\Repository;
use PDO;
use PDOException;

final class SafeRepository
{

    public function __construct(private PDO $pdo) {}
    public function create(
        string $safeCode,
        string $safeLocation,
        string $cashLevel,
        string $doorState,
        string $safeStatus
    ) {
        try {
            

            $sql = "INSERT INTO dbo.smartsafes (safe_code, safe_location, cash_level, door_state, safe_status, updated_at)
            VALUES (:safe_code, :safe_location, :cash_level, :door_state, :safe_status, GETDATE())";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':safe_code' => $safeCode,
                ':safe_location' => $safeLocation,
                ':cash_level' => $cashLevel,
                ':door_state' => $doorState,
                ':safe_status' => $safeStatus
            ]);
        } catch (PDOException $e) {
           throw $e;
        }
    }
}
