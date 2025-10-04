<?php

namespace App\Exceptions;

use Psr\Http\Message\ServerRequestInterface;

class HttpPaginationException extends CustomHttpException
{
    // https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Status/422
    public function __construct(ServerRequestInterface $request)
    {
        parent::__construct(
            $request,
            422,
            'Unprocessable Content - Invalid Pagination',
            'Pagination parameters must be numeric and greater than zero.'
        );
    }
}
