<?php

namespace App\Domain\Models;

use App\Exceptions\HttpInvalidDateException;
use App\Exceptions\HttpInvalidParameterException;
use App\Exceptions\HttpInvalidParameterValueException;
use App\Exceptions\HttpRangeFilterException;
use App\Helpers\Core\PDOService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class MiceModel extends BaseModel
{
    public function __construct(private PDOService $pdo)
    {
        parent::__construct($pdo);
    }

    public function getMice(array $filters, Request $request): array
    {
        // Check for invalid filters (https://www.php.net/manual/en/function.array-diff.php)
        $valid_filters = ['name', 'polling_rate', 'connection', 'weight_minimum', 'weight_maximum', 'lower_price_limit', 'upper_price_limit', 'button_count', 'rating'];
        $invalid_filters = array_diff(array_keys($filters), $valid_filters);
        if (!empty($invalid_filters)) {
            throw new HttpInvalidParameterException($request);
        }

        $args = [];
        $sql = " SELECT DISTINCT mice.* FROM mice JOIN mouse_buttons ON mice.mouse_id = mouse_buttons.mouse_id JOIN mouse_reviews ON mice.mouse_id = mouse_reviews.mouse_id WHERE 1 ";

        if (!empty($filters['name'])) {
            $sql .= " AND mice.name LIKE CONCAT('%', :mice_name, '%') ";
            $args['mice_name'] = $filters['name'];
        }
        if (!empty($filters['polling_rate'])) {
            // make sure that the polling rate is either 125, 500, or 1000 Hz
            $allowedRate = ['125', '500', '100'];
            if (!in_array($filters['polling_rate'], $allowedRate, true)) {
                throw new HttpInvalidParameterValueException($request);
            }

            $sql .= " AND mice.polling_rate LIKE CONCAT('%', :mice_polling_rate, '%') ";
            $args['mice_polling_rate'] = $filters['polling_rate'];
        }
        if (!empty($filters['connection'])) {
            // make sure that the connectivity type is either wired, wireless, or both
            $allowedConnectivity = ['wired', 'wireless', 'both'];
            if (!in_array($filters['connection'], $allowedConnectivity, true)) {
                throw new HttpInvalidParameterValueException($request);
            }

            $sql .= " AND mice.connection LIKE CONCAT('%', :mice_connection, '%') ";
            $args['mice_connection'] = $filters['connection'];
        }
        if (!empty($filters['weight_minimum'])) {
            if (empty($filters['weight_maximum'])) {
                throw new HttpRangeFilterException($request);
            }

            if (is_numeric($filters['weight_minimum']) && is_numeric($filters['weight_maximum'])) {
                $sql .= " AND mice.weight BETWEEN :mice_weight_minimum AND :mice_weight_maximum ";
                $args['mice_weight_minimum'] = $filters['weight_minimum'];
                $args['mice_weight_maximum'] = $filters['weight_maximum'];
            } else {
                throw new HttpInvalidParameterValueException($request);
            }
        } else if (!empty($filters['weight_maximum'])) {
            throw new HttpRangeFilterException($request);
        }
        if (!empty($filters['lower_price_limit'])) {
            if (empty($filters['upper_price_limit'])) {
                // Using a lower price limit requires an upper price limit
                throw new HttpRangeFilterException($request);
            }

            if (is_numeric($filters['lower_price_limit']) && is_numeric($filters['upper_price_limit'])) {
                $sql .= " AND mice.price BETWEEN :lower_limit AND :upper_limit ";
                $args['lower_limit'] = $filters['lower_price_limit'];
                $args['upper_limit'] = $filters['upper_price_limit'];
            } else {
                throw new HttpInvalidParameterValueException($request);
            }
        } else if (!empty($filters['upper_price_limit'])) {
            // Using an upper price limit requires a lower price limit
            throw new HttpRangeFilterException($request);
        }
        if (!empty($filter['button_count'])) {
            // the button count value must be numeric
            if (is_numeric($filters['button_count'])) {
                $sql .= " GROUP BY mice.mouse_id HAVING COUNT(mouse_buttons.mouse_id) ?>= :button_count ";
                $args['button_count'] = $filters['button_count'];
            } else {
                throw new HttpInvalidParameterValueException($request);
            }
        }
        if (!empty($filters['rating'])) {
            // the rating value must be numeric
            if (is_numeric($filters['rating'])) {
                $sql .= " GROUP BY mice.mouse_id HAVING AVG(mouse_reviews.rating) >= :mouse_rating ";
                $args['mouse_rating'] = $filters['rating'];
            } else {
                throw new HttpInvalidParameterValueException($request);
            }
        }

        return $this->paginate($sql, $args);
    }

    public function findMouseById(int $mouse_id): mixed
    {
        $sql = " SELECT * FROM mice WHERE mouse_id = :mouse_id ";
        $mouse = $this->fetchSingle($sql, ["mouse_id" => $mouse_id]);

        return $mouse;
    }
}
