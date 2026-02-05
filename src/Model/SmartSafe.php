<?php
declare(strict_types=1);

namespace Steve\SmartsafeApi\Model;

final class SmartSafe
{

    public function __construct(
        public string $safeCode,
        public string $safeLocation,
        public string $cashLevel,
        public string $doorState,
        public string $safeStatus
    ) {}

    public static function fromArray(array $data) :self {
        return new self(
            trim((string)($data['safe_code'] ?? '')),
            trim((string)($data['safe_location'] ?? '')),
            number_format((float)($data['cash_level'] ?? 0), 2, '.', ''),
            trim((string)($data['door_state'] ?? '')),
            trim((string)($data['safe_status'] ?? ''))
        );
    }

    public function validate():array {
        $errors = [];
        if($this->safeCode === ''){
            $errors[] = 'safe_code ist erforderlich';
        }
        if($this->safeLocation === ''){
            $errors[] = 'safe_location ist erforderlich';
        }
        if(!is_numeric($this->cashLevel)){
            $errors[] = 'cash_level muss eine Zahl >= 0 sein';
        }
        if($this->doorState === ''){
            $errors[] = 'door_state ist erforderlich';
        }
        if($this->safeStatus === ''){
            $errors[] = 'safe_status ist erforderlich';
        }
        return $errors;
    }

    
}
