<?php

namespace App\Exceptions;

use Psr\Http\Message\ServerRequestInterface;

class HttpTooManyParametersException extends CustomHttpException
{
    // https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Status/400
    public function __construct(ServerRequestInterface $request)
    {
        parent::__construct(
            $request,
            400,
            'Bad Request - Too Many Parameters',
            'Some provided parameters cannot exist together because they are contradictory. This includes "name" and "name_contains".'
        );
    }
}
