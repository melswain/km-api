<?php

namespace App\Exceptions;

use Psr\Http\Message\ServerRequestInterface;

class HttpInvalidParameterException extends CustomHttpException
{
    // https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Status/400
    public function __construct(ServerRequestInterface $request)
    {
        parent::__construct(
            $request,
            400,
            'Bad Request - Unknown Parameters',
            'A provided filter parameter does not exist.'
        );
    }
}
