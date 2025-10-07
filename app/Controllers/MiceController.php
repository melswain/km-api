<?php

namespace App\Controllers;

use App\Domain\Models\ButtonsModel;
use App\Domain\Models\MiceModel;
use App\Exceptions\HttpInvalidIdException;
use App\Exceptions\HttpPaginationException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class MiceController extends BaseController
{
    public function __construct(private MiceModel $mice_model, private ButtonsModel $buttons_model) {}

    /**
     * Handles the get mice request and processes the filters, including pagination
     * @param \Psr\Http\Message\ServerRequestInterface $request The server-side http request
     * @param \Psr\Http\Message\ResponseInterface $response The incoming server-side http response
     * @throws \App\Exceptions\HttpPaginationException If the provided pagination values are not numeric
     * @return Response The encoded JSON response to be sent to the user
     */
    public function handleGetMice(Request $request, Response $response): Response
    {
        $filters = $request->getQueryParams();

        $current_page = !empty($filters['page']) ? $filters['page'] : 1;
        $records_per_page = !empty($filters['limit']) ? $filters['limit'] : 10;

        if (!is_numeric($current_page) || !is_numeric($records_per_page)) {
            throw new HttpPaginationException($request);
        }

        $this->mice_model->setPaginationOptions($current_page, $records_per_page);

        $keyboards = $this->mice_model->getMice($filters, $request);
        return $this->renderJson($response, $keyboards);
    }

    /**
     * Route to get a mouse by its ID
     * @param \Psr\Http\Message\ServerRequestInterface $request The server-side http request
     * @param \Psr\Http\Message\ResponseInterface $response The incoming server-side http response
     * @param array $uri_args The URI arguments, in this case, the layout's ID
     * @throws \App\Exceptions\HttpInvalidIdException If the provided layout ID is invalid (i.e., it does not exist)
     * @return Response The encoded response to be sent to the user
     */
    public function handleGetMouseById(Request $request, Response $response, array $uri_args): Response
    {
        $mouse_id = $uri_args['mouse_id'];
        $mouse = $this->mice_model->findMouseById($mouse_id);

        if ($mouse === false) {
            throw new HttpInvalidIdException($request);
        }

        return $this->renderJson($response, $mouse);
    }

    /**
     * Handles the get buttons of a mouse and handles its filters, including pagination
     * @param \Psr\Http\Message\ServerRequestInterface $request The server-side http request
     * @param \Psr\Http\Message\ResponseInterface $response The incoming server-side http response
     * @param array $uri_args The URI arguments; in this case, the layout's ID
     * @throws \App\Exceptions\HttpInvalidIdException If the provided layout ID is invalid
     * @throws \App\Exceptions\HttpPaginationException If the provided pagination values are not numeric
     * @return Response The encoded response to be sent to the user
     */
    public function handleGetButtonMouseById(Request $request, Response $response, array $uri_args): Response
    {
        $mouse_id = $uri_args['mouse_id'];
        $filters = $request->getQueryParams();

        // Validate vendor exists
        $vendor = $this->mice_model->findMouseById($mouse_id);
        if ($vendor === false) {
            throw new HttpInvalidIdException($request);
        }

        // Handle pagination
        $current_page = !empty($filters['page']) ? $filters['page'] : 1;
        $records_per_page = !empty($filters['limit']) ? $filters['limit'] : 10;

        if (!is_numeric($current_page) || !is_numeric($records_per_page)) {
            throw new HttpPaginationException($request);
        }

        $this->buttons_model->setPaginationOptions($current_page, $records_per_page);

        // Fetch buttons for this mouse
        $buttons = $this->buttons_model->findButtonsByMouseId($mouse_id, $filters, $request);

        return $this->renderJson($response, $buttons);
    }
}
