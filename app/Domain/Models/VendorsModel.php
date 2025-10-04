<?php

// declare(strict_types=1);

namespace App\Domain\Models;

use App\Helpers\Core\PDOService;

class VendorsModel extends BaseModel
{
    public function __construct(private PDOService $pdo)
    {
        parent::__construct($pdo);
    }

    public function getVendors(array $filters): array // using "array" as the datatype is called "hint typing"
    {
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
            $sql .= " AND founded_year > :vendors_founded_after ";
            $args["vendors_founded_after"] = $filters['founded_after'];
        }
        if (!empty($filters['founded_before'])) {
            $sql .= " AND founded_year < :vendors_founded_before ";
            $args["vendors_founded_before"] = $filters['founded_before'];
        }
        if (!empty($filters['keyboards_count'])) {
            $sql .= " GROUP BY vendors.vendor_id HAVING COUNT(keyboards.keyboard_id) >= :count";
            $args['count'] = $filters['keyboards_count'];
        }
        if (!empty($filters['lower_price_limit'])) {
            if (empty($filters['upper_price_limit'])) {
                // throw error
            }
            $sql .= " AND keyboards.price BETWEEN :lower_limit AND :upper_limit GROUP BY vendors.vendor_id ";
            $args['lower_limit'] = $filters['lower_price_limit'];
            $args['upper_limit'] = $filters['upper_price_limit'];
        }
        if (!empty($filters['upper_price_limit'])) {
            // throw error
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
