<?php

namespace App\Middleware;

use App\Infrastructure\Library\{Logger, JsonResponse};
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Middleware\ErrorMiddleware;

class CatchErrorMiddleware extends ErrorMiddleware {

  private JsonResponse $jsonResponse;
  private Logger $log;

  public function __construct (App $app, JsonResponse $jsonResponse, array $config, Logger $logger) {
    parent::__construct(
      $app->getCallableResolver(),
      $app->getResponseFactory(),
      $config['displayDetails'] ?? false,
      $config['logErrors'] ?? true,
      $config['logErrorDetails'] ?? true
    );

    $this->jsonResponse = $jsonResponse;
    $this->log = $logger;
    $this->setDefaultErrorHandler([$this, 'errorHandler']);
  }

  public function errorHandler (Request $request, \Throwable $exception, bool $displayErrorDetails, bool $logErrors, bool $logErrorDetails): Response {
    $msg = $exception->getMessage();
    $details = ' in '.$exception->getFile().':'. $exception->getLine();

    if ($logErrors)
      $this->log::err($logErrorDetails ? $msg.$details : $msg);

    return $this->jsonResponse->fail($displayErrorDetails ? $msg.$details : $msg);
  }
}