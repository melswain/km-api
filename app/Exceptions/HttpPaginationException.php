<?php

class HttpPaginationException extends CustomHttpException
{
    protected $code = 422;
    protected $title = 'Unprocessable Content - Invalid Pagination';
    protected $description = 'Pagination parameters must be numeric and greater than zero.';
}
