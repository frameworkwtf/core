<?php

declare(strict_types=1);

namespace Wtf\Core\Tests\Dummy;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class ErrorHandler extends \Wtf\Root
{
    public function error500(ServerRequestInterface $request, ResponseInterface $response, Throwable $e): ResponseInterface
    {
        return $response->withStatus(503);
    }
}
