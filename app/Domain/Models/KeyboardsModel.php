<?php

namespace App\Domain\Models;

use App\Exceptions\HttpInvalidDateException;
use App\Exceptions\HttpInvalidParameterException;
use App\Exceptions\HttpInvalidParameterValueException;
use App\Exceptions\HttpRangeFilterException;
use App\Helpers\Core\PDOService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class KeyboardsModel extends BaseModel
{
    public function __construct(private PDOService $pdo)
    {
        parent::__construct($pdo);
    }

    public function getKeyboards(array $filters, Request $request): array
    {
        // Check for invalid filters (https://www.php.net/manual/en/function.array-diff.php)
        $valid_filters = ['name', 'connectivity', 'switch_type', 'hotswappable', 'weight_maximum', 'released_before', 'released_after', 'firmware_type'];
        $invalid_filters = array_diff(array_keys($filters), $valid_filters);
        if (!empty($invalid_filters)) {
            throw new HttpInvalidParameterException($request);
        }

        $args = [];
        $sql = " SELECT * FROM keyboards JOIN switches ON keyboards.switch_id = switches.switch_id JOIN pcbs ON keyboards.keyboard_id = pcbs.keyboard_id WHERE 1 ";

        if (!empty($filters['name'])) {
            $sql .= " AND keyboards.name LIKE CONCAT('%', :keyboards_name, '%') ";
            $args['keyboards_name'] = $filters['name'];
        }
        if (!empty($filters['connectivity'])) {
            // make sure that the connectivity type is either wired, wireless, or both
            $allowedConnectivity = ['wired', 'wireless', 'both'];
            if (!in_array($filters['connectivity'], $allowedConnectivity, true)) {
                throw new HttpInvalidParameterValueException($request);
            }

            $sql .= " AND keyboards.connectivity LIKE CONCAT('%', :keyboards_connectivity, '%') ";
            $args['keyboards_connectivity'] = $filters['connectivity'];
        }
        if (!empty($filters['switch_type'])) {
            // make sure that the type is only linear, tactile, or clicky
            $allowedTypes = ['linear', 'tactile', 'clicky'];
            if (!in_array($filters['switch_type'], $allowedTypes, true)) {
                throw new HttpInvalidParameterValueException($request);
            }

            $sql .= " AND switches.name LIKE CONCAT('%', :switch_type, '%') ";
            $args['switch_type'] = $filters['switch_type'];
        }
        if (!empty($filters['hotswappable'])) {
            // Input either true or false, and it will search using boolean integers (0 or 1)
            // https://www.php.net/manual/en/filter.constants
            $hotSwappable = filter_var($filters['hotswappable'], FILTER_VALIDATE_BOOLEAN);

            $sql .= " AND keyboards.hot_swappable = :hot_swappable ";
            $args['hot_swappable'] = $hotSwappable ? 1 : 0;
        }
        if (!empty($filters['weight_maximum'])) {
            if (is_numeric($filters['weight_maximum'])) {
                $sql .= " AND keyboards.weight <= :keyboards_weight_maximum ";
                $args['keyboards_weight_maximum'] = $filters['weight_maximum'];
            } else {
                throw new HttpInvalidParameterValueException($request);
            }
        }
        if (!empty($filters['released_after'])) {
            if (empty($filters['released_before'])) {
                throw new HttpRangeFilterException($request);
            }

            if ($this->validateDate($filters['released_after']) && $this->validateDate($filters['released_before'])) {
                $sql .= " AND keyboards.release_date BETWEEN :keyboards_released_after AND :keyboards_released_before ";
                $args['keyboards_released_before'] = $filters['released_before'];
                $args['keyboards_released_after'] = $filters['released_after'];
            } else {
                throw new HttpInvalidDateException($request);
            }
        } else if (!empty($filters['released_before'])) {
            throw new HttpRangeFilterException($request);
        }
        if (!empty($filters['firmware_type'])) {
            // make sure that the type is only QMK or proprietary
            $allowedTypes = ['QMK', 'proprietary'];
            if (!in_array($filters['firmware_type'], $allowedTypes, true)) {
                throw new HttpInvalidParameterValueException($request);
            }

            $sql .= " AND pcbs.firmware LIKE CONCAT('%', :keyboards_firmware_type, '%') ";
            $args['keyboards_firmware_type'] = $filters['firmware_type'];
        }

        return $this->paginate($sql, $args);
    }

    public function findKeyboardById(int $keyboard_id): mixed
    {
        $sql = " SELECT * FROM keyboards WHERE keyboard_id = :keyboard_id ";
        $keyboard = $this->fetchSingle($sql, ["keyboard_id" => $keyboard_id]);

        return $keyboard;
    }

    public function findKeyboardByLayoutId(int $layout_id, array $filters, Request $request): array
    {
        // Check for invalid filters (https://www.php.net/manual/en/function.array-diff.php)
        $valid_filters = ['switch_type', 'lower_price_limit', 'upper_price_limit', 'connectivity'];
        $invalid_filters = array_diff(array_keys($filters), $valid_filters);
        if (!empty($invalid_filters)) {
            throw new HttpInvalidParameterException($request);
        }

        $args = [];
        $sql = " SELECT * FROM keyboards JOIN switches ON keyboards.switch_id = switches.switch_id WHERE layout_id = :layout_id ";
        $args['layout_id'] = $layout_id;

        if (!empty($filters['switch_type'])) {
            $sql .= " AND switches.name LIKE CONCAT('%', :switch_type, '%') ";
            $args['switch_type'] = $filters['switch_type'];
        }
        if (!empty($filters['lower_price_limit'])) {
            if (empty($filters['upper_price_limit'])) {
                // Using a lower price limit requires an upper price limit
                throw new HttpRangeFilterException($request);
            }

            if (is_numeric($filters['lower_price_limit']) && is_numeric($filters['upper_price_limit'])) {
                $sql .= " AND keyboards.price BETWEEN :lower_limit AND :upper_limit ";
                $args['lower_limit'] = $filters['lower_price_limit'];
                $args['upper_limit'] = $filters['upper_price_limit'];
            } else {
                throw new HttpInvalidParameterValueException($request);
            }
        } else if (!empty($filters['upper_price_limit'])) {
            // Using an upper price limit requires a lower price limit
            throw new HttpRangeFilterException($request);
        }
        if (!empty($filters['connectivity'])) {
            // make sure that the connectivity type is either wired, wireless, or both
            $allowedConnectivity = ['wired', 'wireless', 'both'];
            if (!in_array($filters['connectivity'], $allowedConnectivity, true)) {
                throw new HttpInvalidParameterValueException($request);
            }

            $sql .= " AND keyboards.connectivity LIKE CONCAT('%', :keyboards_connectivity, '%') ";
            $args['keyboards_connectivity'] = $filters['connectivity'];
        }

        return $this->paginate($sql, $args);
    }
}
