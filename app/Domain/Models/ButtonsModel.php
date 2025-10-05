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

class ButtonsModel extends BaseModel
{
    public function __construct(private PDOService $pdo)
    {
        parent::__construct($pdo);
    }

    public function findButtonsByMouseId(int $mouse_id, array $filters, Request $request): mixed
    {
        // Check for invalid filters (https://www.php.net/manual/en/function.array-diff.php)
        $valid_filters = ['name', 'programmable', 'name_contains'];
        $invalid_filters = array_diff(array_keys($filters), $valid_filters);
        if (!empty($invalid_filters)) {
            throw new HttpInvalidParameterException($request);
        }

        $args = [];
        $sql = " SELECT * FROM mouse_buttons WHERE mouse_id = :mouse_id ";
        $args['mouse_id'] = $mouse_id;

        // both 'name' and 'name_contains' cannot be used at the same time
        if (!empty($filters['name_contains']) && !empty($filters['name'])) {
            throw new HttpTooManyParametersException($request);
        }
        if (!empty($filters['name_contains'])) {
            $sql .= " AND name LIKE CONCAT('%', :buttons_name, '%') ";
            $args['buttons_name'] = $filters['name_contains'];
        }
        if (!empty($filters['name'])) {
            $sql .= " AND name = :buttons_name ";
            $args['buttons_name'] = $filters['name'];
        }
        if (!empty($filters['programmable'])) {
            // Input either true or false, and it will search using boolean integers (0 or 1)
            // https://www.php.net/manual/en/filter.constants
            $programmable = filter_var($filters['programmable'], FILTER_VALIDATE_BOOLEAN);
            $sql .= " AND programmable = :programmable ";
            $args['programmable'] = $programmable ? 1 : 0;
        }

        return $this->paginate($sql, $args);
    }
}
