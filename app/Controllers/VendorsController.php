<?php

namespace App\Controllers;

use App\Domain\Models\VendorsModel;
use App\Domain\Models\SwitchesModel;
use App\Exceptions\HttpInvalidIdException;
use App\Exceptions\HttpPaginationException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class VendorsController extends BaseController
{
    public function __construct(private VendorsModel $vendors_model, private SwitchesModel $switches_model) {}

    /**
     * Handles the get layouts request and processes the filters,
     * including pagination (GET /vendors)
     * @param \Psr\Http\Message\ServerRequestInterface $request The server-side http request
     * @param \Psr\Http\Message\ResponseInterface $response the incoming server-side http response
     * @throws \App\Exceptions\HttpPaginationException If the provided pagination values are not numeric
     * @return Response The encoded JSON response to be sent to the user
     */
    public function handleGetVendors(Request $request, Response $response): Response
    {
        $filters = $request->getQueryParams();
        // dd($filters); NOTE: output the received filters

        // handle pagination
        $current_page = !empty($filters['page']) ? $filters['page'] : 1;
        $records_per_page = !empty($filters['limit']) ? $filters['limit'] : 10;

        if (!is_numeric($current_page) || !is_numeric($records_per_page)) {
            throw new HttpPaginationException($request);
        }

        $this->vendors_model->setPaginationOptions($current_page, $records_per_page);

        // get vendors
        $vendors = $this->vendors_model->getVendors($filters, $request);
        return $this->renderJson($response, $vendors);
    }

    /**
     * Route to get vendors by their id (GET /vendors/{vendor_id})
     * @param \Psr\Http\Message\ServerRequestInterface $request The server-side http request
     * @param \Psr\Http\Message\ResponseInterface $response The incoming server-side http response
     * @param array $uri_args The URI arguments, in this case, the layout's ID
     * @throws \App\Exceptions\HttpInvalidIdException If the provided layout ID is invalid
     * @return Response the encoded JSON response to be sent to the user
     */
    public function handleGetVendorById(Request $request, Response $response, array $uri_args): Response
    {
        $vendor_id = $uri_args["vendor_id"];
        $vendor = $this->vendors_model->findVendorById($vendor_id);

        // If the ID is invalid, return error 404
        if ($vendor === false) {
            throw new HttpInvalidIdException($request);
        }

        return $this->renderJson($response, $vendor);
    }

    /**
     * Handles the get switches of a vendor and handles its filters,
     * including pagination (GET /vendors/{vendor_id}/switches)
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param array $uri_args
     * @throws \App\Exceptions\HttpInvalidIdException
     * @throws \App\Exceptions\HttpPaginationException
     * @return Response
     */
    public function handleGetSwitchVendorById(Request $request, Response $response, array $uri_args): Response
    {
        $vendor_id = $uri_args['vendor_id'];
        $filters = $request->getQueryParams();

        // Validate vendor exists
        $vendor = $this->vendors_model->findVendorById($vendor_id);
        if ($vendor === false) {
            throw new HttpInvalidIdException($request);
        }

        // handle pagination
        $current_page = !empty($filters['page']) ? $filters['page'] : 1;
        $records_per_page = !empty($filters['limit']) ? $filters['limit'] : 10;

        if (!is_numeric($current_page) || !is_numeric($records_per_page)) {
            throw new HttpPaginationException($request);
        }

        $this->switches_model->setPaginationOptions($current_page, $records_per_page);

        // Fetch switches for this vendor
        $switches = $this->switches_model->findSwitchesByVendorId($vendor_id, $filters, $request);

        return $this->renderJson($response, $switches);
    }
}
