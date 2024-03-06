<?php

use App\Middleware\ApiGuardMiddleware;
use App\Middleware\CatchErrorMiddleware;
use Slim\App;

return static function (App $app): void {
  $app->add(ApiGuardMiddleware::class);
  $app->addBodyParsingMiddleware();
  $app->addRoutingMiddleware();
  $app->add(CatchErrorMiddleware::class);
};