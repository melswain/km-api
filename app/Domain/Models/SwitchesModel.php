<?php

namespace App\Domain\Models;

use App\Exceptions\HttpInvalidDateException;
use App\Exceptions\HttpInvalidParameterException;
use App\Exceptions\HttpInvalidParameterValueException;
use App\Exceptions\HttpRangeFilterException;
use App\Helpers\Core\PDOService;
use Psr\Http\Message\ServerRequestInterface as Request;

class SwitchesModel extends BaseModel
{
    public function __construct(private PDOService $pdo)
    {
        parent::__construct($pdo);
    }

    /**
     * Queries the database for all switches belonging to a vendor and applies the provided filters using WHERE and other clauses
     * @param int $vendor_id The ID of the vendor whose switches need querying
     * @param array $filters The filters to apply to the query from the query string
     * @param \Psr\Http\Message\ServerRequestInterface $request The server-side http request
     * @throws \App\Exceptions\HttpInvalidParameterException If a provided query string parameter is not supported (i.e., name)
     * @throws \App\Exceptions\HttpInvalidParameterValueException If the user supplies a date in an invalid format
     * @throws \App\Exceptions\HttpRangeFilterException If the user supplies an upper range limit, but not a lower, and vice-versa
     * @throws \App\Exceptions\HttpInvalidDateException If the user supplies a date in an invalid format (valid is YYYY-mm-dd)
     * @return array The paginated data
     */
    public function findSwitchesByVendorId(int $vendor_id, array $filters, Request $request): mixed
    {
        // Check for invalid filters (https://www.php.net/manual/en/function.array-diff.php)
        $valid_filters = ['type', 'lower_actuation_force_limit', 'upper_actuation_force_limit', 'lower_travel_distance_limit', 'upper_travel_distance_limit', 'lifespan_minimum', 'released_after', 'released_before', 'page', 'limit'];
        $invalid_filters = array_diff(array_keys($filters), $valid_filters);
        if (!empty($invalid_filters)) {
            throw new HttpInvalidParameterException($request);
        }

        $args = [];
        $sql = " SELECT * FROM switches WHERE vendor_id = :vendor_id ";
        $args['vendor_id'] = $vendor_id;

        if (!empty($filters['type'])) {
            // make sure that the type is only linear, tactile, or clicky
            $allowedTypes = ['linear', 'tactile', 'clicky'];
            if (!in_array($filters['type'], $allowedTypes, true)) {
                throw new HttpInvalidParameterValueException($request);
            }

            $sql .= " AND type LIKE CONCAT('%', :switch_type, '%') ";
            $args['switch_type'] = $filters['type'];
        }
        if (!empty($filters['lower_actuation_force_limit'])) {
            if (empty($filters['upper_actuation_force_limit'])) {
                // Using a lower force limit requires an upper force limit
                throw new HttpRangeFilterException($request);
            }
            $sql .= " AND actuation_force BETWEEN :lower_limit AND :upper_limit ";
            $args['lower_limit'] = $filters['lower_actuation_force_limit'];
            $args['upper_limit'] = $filters['upper_actuation_force_limit'];
        } else if (!empty($filters['upper_actuation_force_limit'])) {
            // Using an upper force limit requires a lower force limit
            throw new HttpRangeFilterException($request);
        }
        if (!empty($filters['lower_travel_distance_limit'])) {
            if (empty($filters['upper_travel_distance_limit'])) {
                // Using a lower travel distance limit requires an upper force limit
                throw new HttpRangeFilterException($request);
            }
            $sql .= " AND total_travel BETWEEN :lower_limit AND :upper_limit ";
            $args['lower_limit'] = $filters['lower_travel_distance_limit'];
            $args['upper_limit'] = $filters['upper_travel_distance_limit'];
        } else if (!empty($filters['upper_travel_distance_limit'])) {
            // Using an upper travel distance limit requires a lower force limit
            throw new HttpRangeFilterException($request);
        }
        if (!empty($filters['lifespan_minimum'])) {
            if (is_numeric($filters['lifespan_minimum'])) {
                $sql .= " AND lifespan_million >= :switch_lifespan ";
                $args['switch_lifespan'] = $filters['lifespan_minimum'];
            } else {
                throw new HttpInvalidParameterValueException($request);
            }
        }
        if (!empty($filters['released_after'])) {
            if (empty($filters['released_before'])) {
                throw new HttpRangeFilterException($request);
            }

            if ($this->validateDate($filters['released_after']) && $this->validateDate($filters['released_before'])) {
                $sql .= " AND release_date BETWEEN :switches_released_after AND :switches_released_before ";
                $args['switches_released_before'] = $filters['released_before'];
                $args['switches_released_after'] = $filters['released_after'];
            } else {
                throw new HttpInvalidDateException($request);
            }
        } else if (!empty($filters['released_before'])) {
            throw new HttpRangeFilterException($request);
        }

        return $this->paginate($sql, $args);
    }
}
