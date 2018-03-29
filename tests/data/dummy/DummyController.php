<?php

declare(strict_types=1);

namespace Wtf\Core\Tests\Dummy;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DummyController extends \Wtf\Root
{
    /**
     * Invoke controller.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param array                  $args
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->request = $request;
        $this->response = $response;
        $name = \explode('-', $request->getAttribute('route')->getName());
        $action = \strtolower(\end($name)).'Action';

        if (\method_exists($this, $action)) {
            $response = \call_user_func([$this, $action]);
        } else {
            $response = $this->notFoundHandler($request, $response);
        }

        return $response;
    }
}
