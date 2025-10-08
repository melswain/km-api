<?php

// declare(strict_types=1);

namespace App\Domain\Models;

use App\Exceptions\HttpInvalidParameterException;
use App\Exceptions\HttpInvalidParameterValueException;
use App\Exceptions\HttpRangeFilterException;
use App\Helpers\Core\PDOService;
use Psr\Http\Message\ServerRequestInterface as Request;

class VendorsModel extends BaseModel
{
    public function __construct(private PDOService $pdo)
    {
        parent::__construct($pdo);
    }

    /**
     * Queries database (table vendors) for all the vendors and applies the
     * provided filters using WHERE and other clauses
     * @param array $filters The filters to be applied to the query
     * @param \Psr\Http\Message\ServerRequestInterface $request The server-side http request
     * @throws \App\Exceptions\HttpInvalidParameterException If a provided query string parameter is not supported
     * @throws \App\Exceptions\HttpInvalidParameterValueException If the provided parameter value is unsupported (i.e., switch_type=pink)
     * @throws \App\Exceptions\HttpRangeFilterException If the user supplies an upper range limit, but not a lower, and vice-versa
     * @return array The paginated data
     */
    public function getVendors(array $filters, Request $request): array // using "array" as the datatype is called "hint typing"
    {
        // Check for invalid filters (https://www.php.net/manual/en/function.array-diff.php)
        $valid_filters = ['name', 'country', 'founded_after', 'founded_before', 'keyboards_count', 'lower_price_limit', 'upper_price_limit', 'page', 'limit', 'order_by'];
        $invalid_filters = array_diff(array_keys($filters), $valid_filters);
        if (!empty($invalid_filters)) {
            throw new HttpInvalidParameterException($request);
        }

        $args = [];
        $sql = " SELECT DISTINCT vendors.* FROM vendors LEFT JOIN keyboards ON keyboards.vendor_id = vendors.vendor_id WHERE 1 ";

        if (!empty($filters['name'])) {
            $sql .= " AND vendors.name LIKE CONCAT('%', :vendors_name, '%') ";
            $args['vendors_name'] = $filters['name'];
        }
        if (!empty($filters['country'])) {
            $sql .= " AND vendors.country LIKE CONCAT('%', :vendors_country, '%') ";
            $args['vendors_country'] = $filters['country'];
        }
        if (!empty($filters['founded_after'])) {
            if ($this->validateYear($filters['founded_after'])) {
                $sql .= " AND vendors.founded_year > :vendors_founded_after ";
                $args["vendors_founded_after"] = $filters['founded_after'];
            } else {
                throw new HttpInvalidParameterValueException($request);
            }
        }
        if (!empty($filters['founded_before'])) {
            if ($this->validateYear($filters['founded_after'])) {
                $sql .= " AND vendors.founded_year < :vendors_founded_before ";
                $args["vendors_founded_before"] = $filters['founded_before'];
            } else {
                throw new HttpInvalidParameterValueException($request);
            }
        }
        if (!empty($filters['keyboards_count'])) {
            if (is_numeric($filters['keyboards_count'])) {
                $sql .= " GROUP BY vendors.vendor_id HAVING COUNT(keyboards.keyboard_id) >= :count";
                $args['count'] = $filters['keyboards_count'];
            } else {
                throw new HttpInvalidParameterValueException($request);
            }
        }
        if (!empty($filters['lower_price_limit'])) {
            if (empty($filters['upper_price_limit'])) {
                // Using a lower price limit requires an upper price limit
                throw new HttpRangeFilterException($request);
            }

            if (is_numeric($filters['lower_price_limit']) && is_numeric($filters['upper_price_limit'])) {
                $sql .= " AND keyboards.price BETWEEN :lower_limit AND :upper_limit GROUP BY vendors.vendor_id ";
                $args['lower_limit'] = $filters['lower_price_limit'];
                $args['upper_limit'] = $filters['upper_price_limit'];
            } else {
                throw new HttpInvalidParameterValueException($request);
            }
        } else if (!empty($filters['upper_price_limit'])) {
            // Using an upper price limit requires a lower price limit
            throw new HttpRangeFilterException($request);
        }
        if (!empty($filters['order_by'])) {
            // the user can order by any column in vendors
            $allowedTypes = ['vendor_id', 'name', 'country', 'founded_year', 'website', 'headquarters'];
            if (!in_array($filters['order_by'], $allowedTypes, true)) {
                throw new HttpInvalidParameterValueException($request);
            }

            $sql .= " ORDER BY :order_by_type ASC ";
            $args['order_by_type'] = $filters['order_by'];
        }

        return $this->paginate($sql, $args);
    }

    /**
     * Queries the database to find a single vendor with the provided ID
     * @param int $vendor_id The ID to search for
     * @return mixed The single vendor found
     */
    public function findVendorById(int $vendor_id): mixed
    {
        $sql = " SELECT * FROM vendors WHERE vendor_id = :vendor_id ";
        $vendor = $this->fetchSingle($sql, ["vendor_id" => $vendor_id]);

        return $vendor;
    }
}
