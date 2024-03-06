<?php

use App\Adapter\Controller\PostController;
use App\Infrastructure\{AccountRepository, PostRepository, TagRepository};
use App\Infrastructure\Library\{Logger, JsonResponse};
use App\Logic\{Cleaner, Validator};
use App\Logic\Contract\{IAccountRepository, IPostRepository, ITagRepository};
use App\Logic\UseCase\PostUseCase;
use App\Middleware\{ApiGuardMiddleware, CatchErrorMiddleware};
use Dibi\Connection as DBConnection;
use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Factory\AppFactory;

ini_set('xdebug.mode', 'coverage');

return [
  App::class => fn (ContainerInterface $dic): App => AppFactory::createFromContainer($dic),
  Validator::class => fn (ContainerInterface $dic) => new Validator(),
  Cleaner::class => fn (ContainerInterface $dic) => new Cleaner(),
  JsonResponse::class => fn (ContainerInterface $dic) => new JsonResponse(),
  DBConnection::class => fn (ContainerInterface $dic) => new DBConnection($dic->get('config')['db']),
  Logger::class => fn (ContainerInterface $dic) => new Logger($dic->get('config')['logDir']),

  ApiGuardMiddleware::class => fn (ContainerInterface $dic) => new ApiGuardMiddleware(),

  CatchErrorMiddleware::class => function (ContainerInterface $dic): CatchErrorMiddleware {
    return new CatchErrorMiddleware(
      $dic->get(App::class),
      $dic->get(JsonResponse::class),
      $dic->get('config')['errors'],
      $dic->get(Logger::class)
    );
  },

  IPostRepository::class => function (ContainerInterface $dic): IPostRepository {
    return new PostRepository($dic->get(DBConnection::class));
  },

  ITagRepository::class => function (ContainerInterface $dic): ITagRepository {
    return new TagRepository($dic->get(DBConnection::class));
  },

  IAccountRepository::class => function (ContainerInterface $dic): IAccountRepository {
    return new AccountRepository($dic->get(DBConnection::class));
  },

  PostUseCase::class => function (ContainerInterface $dic): PostUseCase {
    return new PostUseCase(
      $dic->get(IPostRepository::class),
      $dic->get(ITagRepository::class),
      $dic->get(IAccountRepository::class),
      $dic->get(Cleaner::class),
      $dic->get(Validator::class),
    );
  },

  PostController::class => function (ContainerInterface $dic): PostController {
    return new PostController(
      $dic->get(PostUseCase::class),
      $dic->get(JsonResponse::class),
    );
  },

  'config' => [
//    'env' => getenv('APP_ENV') ?: 'prod',
    'errors' => [
      'displayDetails' => false,
      'logErrors' => true,
      'logErrorDetails' => true,
    ],
    'logDir' => __DIR__.'/../../log',
    'db' => [
      'driver'   => 'postgre',
      'host'     => 'blog-postgres',
      'port'     => 5432,
      'username' => 'blog',
      'password' => '1111',
      'database' => 'blog',
//      'profiler' => [
//        'file' => __DIR__.'/../../db_log.log',
//      ],
    ]
  ],
];