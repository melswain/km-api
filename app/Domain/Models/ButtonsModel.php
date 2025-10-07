<?php

namespace App\Domain\Models;

use App\Exceptions\HttpInvalidParameterException;
use App\Helpers\Core\PDOService;
use Psr\Http\Message\ServerRequestInterface as Request;

class ButtonsModel extends BaseModel
{
    public function __construct(private PDOService $pdo)
    {
        parent::__construct($pdo);
    }

    /**
     * Queries database (table mouse_buttons) for the buttons of a mouse
     * and applies the provided filters using WHERE and other clauses
     * @param int $mouse_id The ID of the mouse whose buttons need querying
     * @param array $filters The filters to apply to the query from the query string
     * @param \Psr\Http\Message\ServerRequestInterface $request The server-side http request
     * @throws \App\Exceptions\HttpInvalidParameterException If a provided query string parameter is not supported
     * @return array The paginated data
     */
    public function findButtonsByMouseId(int $mouse_id, array $filters, Request $request): mixed
    {
        // Check for invalid filters (https://www.php.net/manual/en/function.array-diff.php)
        $valid_filters = ['name', 'programmable', 'page', 'limit'];
        $invalid_filters = array_diff(array_keys($filters), $valid_filters);
        if (!empty($invalid_filters)) {
            throw new HttpInvalidParameterException($request);
        }

        $args = [];
        $sql = " SELECT * FROM mouse_buttons WHERE mouse_id = :mouse_id ";
        $args['mouse_id'] = $mouse_id;

        if (!empty($filters['name'])) {
            $sql .= " AND name LIKE CONCAT('%', :buttons_name, '%') ";
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
