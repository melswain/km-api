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

    public function getVendors(array $filters, Request $request): array // using "array" as the datatype is called "hint typing"
    {
        // Check for invalid filters (https://www.php.net/manual/en/function.array-diff.php)
        $valid_filters = ['name', 'country', 'founded_after', 'founded_before', 'keyboards_count', 'lower_price_limit', 'upper_price_limit'];
        $invalid_filters = array_diff(array_keys($filters), $valid_filters);
        if (!empty($invalid_filters)) {
            throw new HttpInvalidParameterException($request);
        }

        $args = [];
        $sql = " SELECT DISTINCT vendors.* FROM vendors LEFT JOIN keyboards ON keyboards.vendor_id = vendors.vendor_id WHERE 1 ";

        if (!empty($filters['name'])) {
            $sql .= " AND name LIKE CONCAT('%', :vendors_name, '%') ";
            $args['vendors_name'] = $filters['name'];
        }
        if (!empty($filters['country'])) {
            $sql .= " AND country LIKE CONCAT('%', :vendors_country, '%') ";
            $args['vendors_country'] = $filters['country'];
        }
        if (!empty($filters['founded_after'])) {
            if ($this->validateYear($filters['founded_after'])) {
                $sql .= " AND founded_year > :vendors_founded_after ";
                $args["vendors_founded_after"] = $filters['founded_after'];
            } else {
                throw new HttpInvalidParameterValueException($request);
            }
        }
        if (!empty($filters['founded_before'])) {
            if ($this->validateYear($filters['founded_after'])) {
                $sql .= " AND founded_year < :vendors_founded_before ";
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

        return $this->paginate($sql, $args);
    }

    public function findVendorById(int $vendor_id): mixed
    {
        $sql = " SELECT * FROM vendors WHERE vendor_id = :vendor_id ";
        $vendor = $this->fetchSingle($sql, ["vendor_id" => $vendor_id]);

        return $vendor;
    }
}
