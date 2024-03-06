<?php

use App\Adapter\Controller\PostController;
use Slim\App;
use Slim\Exception\HttpNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;

return static function (App $app): void {
  $app->group('/api', function (RouteCollectorProxy $group) {
    $group->post('/getPosts', [PostController::class, 'getPosts']);
    $group->post('/getPost', [PostController::class, 'getPost']);
    $group->post('/getPostByAccount', [PostController::class, 'getPostByAccount']);
    $group->post('/editPost', [PostController::class, 'editPost']);
    $group->post('/createPost', [PostController::class, 'createPost']);
    $group->post('/deletePost', [PostController::class, 'deletePost']);
    $group->post('/getTags', [PostController::class, 'getTags']);
    $group->post('/createTags', [PostController::class, 'createTags']);
    $group->post('/getPostsTotal', [PostController::class, 'getPostsTotal']);
  });

  $app->map(['GET','POST','PUT','DELETE','PATCH'], '/{params:.*}', function (Request $request, Response $response) {
    throw new HttpNotFoundException($request);
  });
};
