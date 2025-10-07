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

class KeycapsModel extends BaseModel
{
    public function __construct(private PDOService $pdo)
    {
        parent::__construct($pdo);
    }

    /**
     * Queries database (table keycap_sets) for the keycaps of a layout
     * and applies the provided filters using WHERE and other clauses
     * @param mixed $layout_id The ID of the mouse whose buttons need querying
     * @param array $filters The filters to apply to the query from the query string
     * @param \Psr\Http\Message\ServerRequestInterface $request The server-side http request
     * @throws \App\Exceptions\HttpInvalidParameterException If a provided query string parameter is not supported
     * @throws \App\Exceptions\HttpInvalidParameterValueException If the provided parameter value is unsupported (i.e., switch_type=pink)
     * @return array The paginated data
     */
    public function findKeycapSetByLayoutId($layout_id, array $filters, Request $request): array
    {
        $valid_filters = ['material', 'profile', 'manufacturer', 'price_maximum', 'page', 'limit'];
        $invalid_filters = array_diff(array_keys($filters), $valid_filters);
        if (!empty($invalid_filters)) {
            throw new HttpInvalidParameterException($request);
        }

        $args = [];
        $sql = " SELECT keycap_sets.* FROM keycap_sets
                JOIN keycap_compatibility ON keycap_sets.keycap_id = keycap_compatibility.keycap_id
                JOIN layouts ON keycap_compatibility.layout_id = layouts.layout_id
                WHERE keycap_compatibility.layout_id = :layout_id ";
        $args['layout_id'] = $layout_id;


        if (!empty($filters['material'])) {
            $sql .= " AND keycap_sets.material LIKE CONCAT('%', :keycap_material, '%') ";
            $args['keycap_material'] = $filters['material'];
        }
        if (!empty($filters['profile'])) {
            $sql .= " AND keycap_sets.profile LIKE CONCAT('%', :keycap_profile, '%') ";
            $args['keycap_profile'] = $filters['profile'];
        }
        if (!empty($filters['manufacturer'])) {
            $sql .= " AND keycap_sets.manufacturer LIKE CONCAT('%', :kaycap_manufacturer, '%') ";
            $args['keycap_manufacturer'] = $filters['manufacturer'];
        }
        if (!empty($filters['price_maximum'])) {
            if (is_numeric($filters['price_maximum'])) {
                $sql .= " AND keycap_sets.price <= :keycap_price ";
                $args['keycap_price'] = $filters['price_maximum'];
            } else {
                throw new HttpInvalidParameterValueException($request);
            }
        }

        return $this->paginate($sql, $args);
    }
}
