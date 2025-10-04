<?php

namespace App\Controllers;

use App\Domain\Models\VendorsModel;
use App\Exceptions\HttpInvalidIdException;
use App\Exceptions\HttpPaginationException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;

class VendorsController extends BaseController
{
    public function __construct(private VendorsModel $vendors_model) {}

    public function handleGetVendors(Request $request, Response $response): Response
    {
        $filters = $request->getQueryParams();
        // dd($filters); NOTE: output the received filters

        $current_page = !empty($filters['page']) ? $filters['page'] : 1;
        $records_per_page = !empty($filters['limit']) ? $filters['limit'] : 10;

        if (!is_numeric($current_page) || !is_numeric($records_per_page)) {
            throw new HttpPaginationException($request);
        }

        $this->vendors_model->setPaginationOptions($current_page, $records_per_page);

        $vendors = $this->vendors_model->getVendors($filters, $request);
        return $this->renderJson($response, $vendors);
    }

    /**
     * Route to get vendors by their id (GET /vendors/{vendor_id})
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return void
     */
    public function handleGetVendorsById(Request $request, Response $response, array $uri_args): Response
    {
        $vendor_id = $uri_args["vendors_id"];
        $vendor = $this->vendors_model->findVendorById($vendor_id);

        // If the ID is invalid, return error 404
        if ($vendor === false) {
            throw new HttpInvalidIdException($request);
        }

        return $this->renderJson($response, $vendor);
    }
}
