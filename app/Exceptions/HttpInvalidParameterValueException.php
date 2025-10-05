<?php

namespace App\Exceptions;

use Psr\Http\Message\ServerRequestInterface;

class HttpInvalidParameterValueException extends CustomHttpException
{
    // https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Status/400
    public function __construct(ServerRequestInterface $request)
    {
        parent::__construct(
            $request,
            400,
            'Bad Request - Invalid Parameter Value',
            'A provided filter parameter value is invalid.'
        );
    }
}
