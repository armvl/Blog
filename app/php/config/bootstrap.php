<?php

use DI\ContainerBuilder;
use Slim\App;

error_reporting(E_ALL);
ini_set('display_errors', false);
date_default_timezone_set('Europe/Moscow');

http_response_code(500);

include __DIR__.'/../vendor/autoload.php';

$builder = new ContainerBuilder();

$container = $builder
  ->useAutowiring(false)
  ->useAnnotations(false)
  ->addDefinitions(require __DIR__.'/dependencies.php')
  ->build()
;

$app = $container->get(App::class);

(require __DIR__.'/middlewares.php')($app);
(require __DIR__.'/routes.php')($app);

$app->run();
