<?php

declare(strict_types=1);

use App\Controllers\AboutController;
use App\Controllers\KeyboardsController;
use App\Controllers\LayoutsController;
use App\Controllers\MiceController;
use App\Controllers\VendorsController;
use App\Helpers\DateTimeHelper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


return static function (Slim\App $app): void {

    // Routes without authentication check: /login, /token

    //* ROUTE: GET /
    $app->get('/', [AboutController::class, 'handleAboutWebService']);

    //* NOTE: callback naming pattern: handle<ActionName>, e.g. handleGetPlayers
    //* ROUTE: GET /players
    //$app->get('/players', [PlayersController::class, 'handleGetPlayers']);

    //* ROUTE: GET /ping
    $app->get('/ping', function (Request $request, Response $response, $args) {

        $payload = [
            "greetings" => "Reporting! Hello there!",
            "now" => DateTimeHelper::now(DateTimeHelper::Y_M_D_H_M),
        ];
        $response->getBody()->write(json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR));
        return $response;
    });
    // Example route to test error handling.
    $app->get('/error', function (Request $request, Response $response, $args) {
        throw new \Slim\Exception\HttpNotFoundException($request, "Something went wrong");
    });

    // ROUTE: GET /vendors get list of vendors
    $app->get('/vendors', [VendorsController::class, 'handleGetVendors']);
    // ROUTE: GET /vendors/{vendors_id} get vendor by id
    $app->get('/vendors/{vendor_id}', [VendorsController::class, 'handleGetVendorById']);
    $app->get('/vendors/{vendor_id}/switches', [VendorsController::class, 'handleGetSwitchVendorById']);

    // ROUTE: GET /keyboards get list of keyboards
    $app->get('/keyboards', [KeyboardsController::class, 'handleGetKeyboards']);
    // ROUTE: GET /keyboards/{keyboard_id} get keyboard by id
    $app->get('/keyboards/{keyboard_id}', [KeyboardsController::class, 'handleGetKeyboardById']);

    // ROUTE: GET /mice get list of mice
    $app->get('/mice', [MiceController::class, 'handleGetMice']);
    // ROUTE: GET /mice/{mouse_id} get mouse by id
    $app->get('/mice/{mouse_id}', [MiceController::class, 'handleGetMouseById']);
    // ROUTE: GET /mice/{mouse_id}/buttons get buttons from mouse
    $app->get('/mice/{mouse_id}/buttons', [MiceController::class, 'handleGetButtonMouseById']);

    // ROUTE: GET /layouts get list of layouts
    $app->get('/layouts', [LayoutsController::class, 'handleGetLayouts']);
    // ROUTE: GET /layouts{layout_id} get layout by id
    $app->get('/layouts/{layout_id}', [LayoutsController::class, 'handleGetLayoutsById']);
    // Route: GET /layouts/{layout_id}/keyboards get all keyboards belonging to layout id
    $app->get('/layouts/{layout_id}/keyboards', [LayoutsController::class, 'handleGetKeyboardLayoutById']);
    // Route: GET /layouts/{layout_id}/keycap-sets get all keycap sets belonging to layout id
    $app->get('/layouts/{layout_id}/keycap-sets', [LayoutsController::class, 'handleGetKeycapSetLayoutById']);
};
