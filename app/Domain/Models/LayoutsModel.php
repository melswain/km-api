<?php

namespace App\Domain\Models;

use App\Exceptions\HttpInvalidParameterException;
use App\Helpers\Core\PDOService;
use Psr\Http\Message\ServerRequestInterface as Request;

class LayoutsModel extends BaseModel
{
    public function __construct(private PDOService $pdo)
    {
        parent::__construct($pdo);
    }

    /**
     * Queries database (table layouts) for all the layouts (and applies
     * no filters)
     * @param array $filters The filters to be applied to the query
     * @param \Psr\Http\Message\ServerRequestInterface $request The server-side http request
     * @throws \App\Exceptions\HttpInvalidParameterException If a provided query string parameter is not supported (none are supported)
     * @return array The paginated data
     */
    public function getLayouts(array $filters, Request $request): array
    {
        // Check for invalid filters (https://www.php.net/manual/en/function.array-diff.php)
        $valid_filters = ['page', 'limit'];
        $invalid_filters = array_diff(array_keys($filters), $valid_filters);
        if (!empty($invalid_filters)) {
            throw new HttpInvalidParameterException($request);
        }

        $args = [];
        $sql = " SELECT * FROM layouts WHERE 1 ";
        return $this->paginate($sql, $args);
    }

    /**
     * Queries the database to find a single layout with the provided ID
     * @param int $layout_id The ID to search fo
     * @return mixed The single layout found
     */
    public function findLayoutById(int $layout_id): mixed
    {
        $sql = " SELECT * FROM layouts WHERE layout_id = :layout_id ";
        $layout = $this->fetchSingle($sql, ['layout_id' => $layout_id]);

        return $layout;
    }
}
