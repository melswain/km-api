<?php

namespace App\Domain\Models;

use App\Exceptions\HttpInvalidDateException;
use App\Exceptions\HttpInvalidParameterException;
use App\Exceptions\HttpInvalidParameterValueException;
use App\Exceptions\HttpRangeFilterException;
use App\Exceptions\HttpTooManyParametersException;
use App\Helpers\Core\PDOService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class KeycapsModel extends BaseModel
{
    public function __construct(private PDOService $pdo)
    {
        parent::__construct($pdo);
    }
}
