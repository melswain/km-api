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

    public function handleGetLayouts(Request $request, Response $response): Response
    {
        $filters = $request->getQueryParams();

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
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return void
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

    public function handleGetKeyboardLayoutById(Request $request, Response $response, array $uri_args): Response
    {
        $layout_id = $uri_args['layout_id'];
        $filters = $request->getQueryParams();

        // Validate layout exists
        $layout = $this->layouts_model->findLayoutById($layout_id);
        if ($layout === false) {
            throw new HttpInvalidIdException($request);
        }

        // Fetch switches for this vendor
        $keyboards = $this->keyboards_model->findKeyboardByLayoutId($layout_id, $filters, $request);

        return $this->renderJson($response, $keyboards);
    }
}
