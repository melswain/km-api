<?php

namespace App\Controllers;

use App\Domain\Models\KeyboardsModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class KeyboardsController extends BaseController
{
    public function __construct(private KeyboardsModel $keyboards_model) {}

    public function handleGetKeyboards(Request $request, Response $response): Response
    {
        $keyboards = $this->keyboards_model->getKeyboards();

        $payload = json_encode($keyboards);

        $response->getBody()->write($payload);

        return $response->withHeader(HEADERS_CONTENT_TYPE, APP_MEDIA_TYPE_JSON);
    }

    /**
     * Route to get a keyboard by its id (GET /keyboards/{keyboard_id})
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return void
     */
    public function handleGetKeyboardsById(Request $request, Response $response, array $uri_args): Response
    {
        $keyboard_id = $uri_args["keyboard_id"];
        $keyboard = $this->keyboards_model->findKeyboardById($keyboard_id);

        if ($keyboard === false) {
            $payload = [
                "status" => "error",
                "code" => 404,
                "message" => "There was no record matching the supplied keyboard ID..."
            ];

            return $this->renderJson(
                $response,
                $payload,
                404
            );
        }

        return $this->renderJson($response, $keyboard);
    }
}
