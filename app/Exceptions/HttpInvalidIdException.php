<?php

namespace App\Exceptions;

use Psr\Http\Message\ServerRequestInterface;

class HttpInvalidIdException extends CustomHttpException
{
    // https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Status/404

    public function __construct(ServerRequestInterface $request)
    {
        parent::__construct(
            $request,
            404,
            'ID Not Found',
            'The provided resource id was not found and does not exist.'
        );
    }
}
