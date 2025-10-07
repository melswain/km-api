<?php

namespace App\Controllers;

use App\Domain\Models\LayoutsModel;
use App\Domain\Models\KeyboardsModel;
use App\Domain\Models\KeycapsModel;
use App\Exceptions\HttpInvalidIdException;
use App\Exceptions\HttpPaginationException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class LayoutsController extends BaseController
{
    public function __construct(private LayoutsModel $layouts_model, private KeyboardsModel $keyboards_model, private KeycapsModel $keycaps_model) {}

    /**
     * Handles the get layouts request and (theoretically) processes the filters
     * (including pagination filters) (layouts does not currently support any filters) (GET /layouts)
     * @param \Psr\Http\Message\ServerRequestInterface $request The server-side http request
     * @param \Psr\Http\Message\ResponseInterface $response The incoming server-side http response
     * @throws \App\Exceptions\HttpPaginationException If the provided pagination values are not numeric, an error is thrown
     * @return Response The encoded JSON response to be sent to the user
     */
    public function handleGetLayouts(Request $request, Response $response): Response
    {
        $filters = $request->getQueryParams();

        // Deal with pagination parameters
        $current_page = !empty($filters['page']) ? $filters['page'] : 1;
        $records_per_page = !empty($filters['limit']) ? $filters['limit'] : 10;

        if (!is_numeric($current_page) || !is_numeric($records_per_page)) {
            throw new HttpPaginationException($request);
        }

        $this->layouts_model->setPaginationOptions($current_page, $records_per_page);

        $layouts = $this->layouts_model->getLayouts($filters, $request);
        return $this->renderJson($response, $layouts);
    }

    /**
     * Route to get layouts by their id (GET /layouts/{layout_id})
     * @param \Psr\Http\Message\ServerRequestInterface $request The server-side http request
     * @param \Psr\Http\Message\ResponseInterface $response The incoming server-side http response
     * @param array $uri_args The URI arguments, in this case, the layout's ID
     * @throws \App\Exceptions\HttpInvalidIdException If the provided layout id is invalid
     * @return Response The encoded response to be sent to the user
     */
    public function handleGetLayoutsById(Request $request, Response $response, array $uri_args): Response
    {
        $layout_id = $uri_args['layout_id'];
        $layout = $this->layouts_model->findLayoutById($layout_id);

        // If the ID is invalid, return error 404
        if ($layout === false) {
            throw new HttpInvalidIdException($request);
        }

        return $this->renderJson($response, $layout);
    }

    /**
     * Handles the get keyboards of a layout and its filters,
     * including pagination (GET /layouts/{layout_id}/keyboards)
     * @param \Psr\Http\Message\ServerRequestInterface $request The server-side http request
     * @param \Psr\Http\Message\ResponseInterface $response The incoming server-side http response
     * @param array $uri_args The URI arguments; in this case, the layout's ID
     * @throws \App\Exceptions\HttpInvalidIdException If the provided layout id is invalid
     * @throws \App\Exceptions\HttpPaginationException If the provided pagination values or not numeric, an error is thrown
     * @return Response The encoded response to be sent to the user
     */
    public function handleGetKeyboardLayoutById(Request $request, Response $response, array $uri_args): Response
    {
        $layout_id = $uri_args['layout_id'];
        $filters = $request->getQueryParams();

        // Validate layout exists
        $layout = $this->layouts_model->findLayoutById($layout_id);
        if ($layout === false) {
            throw new HttpInvalidIdException($request);
        }

        $current_page = !empty($filters['page']) ? $filters['page'] : 1;
        $records_per_page = !empty($filters['limit']) ? $filters['limit'] : 10;

        if (!is_numeric($current_page) || !is_numeric($records_per_page)) {
            throw new HttpPaginationException($request);
        }

        $this->keyboards_model->setPaginationOptions($current_page, $records_per_page);

        // Fetch switches for this vendor
        $keyboards = $this->keyboards_model->findKeyboardByLayoutId($layout_id, $filters, $request);

        return $this->renderJson($response, $keyboards);
    }

    /**
     * Handles the get keycap sets of a layout and handles its filters,
     * including pagination (GET /layouts/{layout_id}/keycap-sets)
     * @param \Psr\Http\Message\ServerRequestInterface $request The server-side http request
     * @param \Psr\Http\Message\ResponseInterface $response The incoming server-side http response
     * @param array $uri_args The URI arguments; in this case, the layout's ID
     * @throws \App\Exceptions\HttpInvalidIdException If the provided layout id is invalid
     * @throws \App\Exceptions\HttpPaginationException If the provided pagination values are not numeric, an error is thrown
     * @return Response The encoded response to be sent to the user
     */
    public function handleGetKeycapSetLayoutById(Request $request, Response $response, array $uri_args): Response
    {
        $layout_id = $uri_args['layout_id'];
        $filters = $request->getQueryParams();

        // Validate layout existence
        $layout = $this->layouts_model->findLayoutById($layout_id);
        if ($layout === false) {
            throw new HttpInvalidIdException($request);
        }

        // Deal with pagination parameters
        $current_page = !empty($filters['page']) ? $filters['page'] : 1;
        $records_per_page = !empty($filters['limit']) ? $filters['limit'] : 10;

        if (!is_numeric($current_page) || !is_numeric($records_per_page)) {
            throw new HttpPaginationException($request);
        }

        $this->keycaps_model->setPaginationOptions($current_page, $records_per_page);

        // Fetch keycaps for this layout
        $keycaps = $this->keycaps_model->findKeycapSetByLayoutId($layout_id, $filters, $request);

        return $this->renderJson($response, $keycaps);
    }
}
