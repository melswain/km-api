<?php

namespace App\Exceptions;

use Psr\Http\Message\ServerRequestInterface;

class HttpRangeFilterException extends CustomHttpException
{
    // https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Status/416

    public function __construct(ServerRequestInterface $request)
    {
        parent::__construct(
            $request,
            416,
            'Range Not Satisfiable',
            'One or more filter parameters are invalid. Specifying one upper/lower range limit requires a matching opposite limit.'
        );
    }

}
