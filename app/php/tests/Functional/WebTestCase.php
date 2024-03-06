<?php

namespace Test\Functional;

use App\Infrastructure\Library\Logger;
use DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use Slim\App;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\StreamFactory;

class WebTestCase extends TestCase {

  protected function requestPost (string $path, array $body = []): ServerRequestInterface {
    return $this->request('POST', $path, $body);
  }

  protected function request (string $method, string $path, array $body = []): ServerRequestInterface {
    $request = (new ServerRequestFactory())
      ->createServerRequest($method, $path)
      ->withHeader('Content-Type', 'application/json')
      ->withHeader('X-Requested-With', 'XMLHttpRequest')
    ;

    if ($body) {
      $stream = (new StreamFactory())->createStream(json_encode($body, JSON_UNESCAPED_UNICODE));
      $request = $request->withBody($stream);
    }

    return $request;
  }

  protected function app (): App {
    $builder = new ContainerBuilder();
    $configPath = __DIR__.'/../../config';

    $container = $builder
      ->useAutowiring(false)
      ->useAnnotations(false)
      ->addDefinitions(require $configPath.'/dependencies.php')
      ->build()
    ;

    $app = $container->get(App::class);

    (require $configPath.'/middlewares.php')($app);
    (require $configPath.'/routes.php')($app);

    return $app;
  }
}