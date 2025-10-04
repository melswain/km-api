<?php

namespace App\Exceptions;

use Slim\Exception\HttpSpecializedException;
use Psr\Http\Message\ServerRequestInterface;

class CustomHttpException extends HttpSpecializedException
{
    public function __construct(
        ServerRequestInterface $request,
        int $code = 400,
        string $title = 'Bad Request',
        string $description = 'This request could not be understood.'
    ) {
        $this->request = $request;
        $this->code = $code;
        $this->title = $title;
        $this->message = $description;
    }
}
