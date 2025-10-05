<?php

namespace App\Domain\Models;

use App\Exceptions\HttpInvalidDateException;
use App\Exceptions\HttpInvalidParameterException;
use App\Exceptions\HttpInvalidParameterValueException;
use App\Exceptions\HttpRangeFilterException;
use App\Exceptions\HttpTooManyParametersException;
use App\Helpers\Core\PDOService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class LayoutsModel extends BaseModel
{
    public function __construct(private PDOService $pdo)
    {
        parent::__construct($pdo);
    }

    public function getLayouts(array $filters, Request $request): array
    {
        // Check for invalid filters (https://www.php.net/manual/en/function.array-diff.php)
        $valid_filters = [];
        $invalid_filters = array_diff(array_keys($filters), $valid_filters);
        if (!empty($invalid_filters)) {
            throw new HttpInvalidParameterException($request);
        }

        $args = [];
        $sql = " SELECT * FROM layouts WHERE 1 ";
        return $this->paginate($sql, $args);
    }

    public function findLayoutById(int $layout_id): mixed
    {
        $sql = " SELECT * FROM layouts WHERE layout_id = :layout_id ";
        $layout = $this->fetchSingle($sql, ['layout_id' => $layout_id]);

        return $layout;
    }
}
