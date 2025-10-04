<?php

namespace App\Domain\Models;

use App\Helpers\Core\PDOService;

class KeyboardsModel extends BaseModel
{
    public function __construct(private PDOService $pdo)
    {
        parent::__construct($pdo);
    }

    public function getKeyboards(): array
    {
        $sql = "SELECT * FROM keyboards";
        return $this->fetchAll($sql);
    }

    public function findKeyboardById(int $keyboard_id): mixed
    {
        $sql = " SELECT * FROM keyboards WHERE keyboard_id = :keyboard_id ";
        $keyboard = $this->fetchSingle($sql, ["keyboard_id" => $keyboard_id]);

        return $keyboard;
    }
}
