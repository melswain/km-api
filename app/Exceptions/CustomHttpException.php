
<?php

use Slim\Exception\HttpSpecializedException;
use Psr\Http\Message\ServerRequestInterface;

class CustomHttpException extends HttpSpecializedException
{
    protected $code;
    protected $title;
    protected $description;

    public function __construct(ServerRequestInterface $request, int $code = 400, string $title = 'Bad Request', string $description = 'This request could not be understood.')
    {
        $this->request = $request;
        $this->code = $code;
        $this->title = $title;
        $this->description = $description;
    }
}
