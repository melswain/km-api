<?php

namespace App\Exceptions;

use Psr\Http\Message\ServerRequestInterface;

class HttpInvalidDateException extends CustomHttpException
{
    // https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Status/400

    public function __construct(ServerRequestInterface $request)
    {
        parent::__construct(
            $request,
            400,
            'Bad Request - Invalid Date Format',
            'All dates are in the form yyyy-mm-dd. Please ensure that the inputted data types follow this format.'
        );
    }
}
