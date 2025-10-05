<?php

namespace App\Controllers;

use App\Domain\Models\MiceModel;
use App\Exceptions\HttpInvalidIdException;
use App\Exceptions\HttpPaginationException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class MiceController extends BaseController
{
    public function __construct(private MiceModel $mice_model) {}

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
     * Route to get a mouse by its id (GET /mice/{mouse_id})
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return void
     */
    public function handleGetMiceById(Request $request, Response $response, array $uri_args): Response
    {
        $mouse_id = $uri_args['mouse_id'];
        $mouse = $this->mice_model->findMouseById($mouse_id);

        if ($mouse === false) {
            throw new HttpInvalidIdException($request);
        }

        return $this->renderJson($response, $mouse);
    }
}
