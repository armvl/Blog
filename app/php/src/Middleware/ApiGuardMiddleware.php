<?php

namespace App\Middleware;

use App\Logic\Exception\ValidationDomainException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;

class ApiGuardMiddleware implements MiddlewareInterface {

  public function process (Request $request, Handler $handler): Response {
    $method = $request->getMethod();
    $contentType = $request->getHeaderLine('Content-Type');
    $XRequestedWith = $request->getHeaderLine('X-Requested-With');

    if ( $method != 'POST'
      || $contentType != 'application/json'
      || $XRequestedWith != 'XMLHttpRequest'
    ) {
      throw new ValidationDomainException('Access denied #100');
    }

    return $handler->handle($request);
  }
}