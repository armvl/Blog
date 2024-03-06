<?php

namespace App\Adapter\Controller;

use App\Logic\UseCase\PostUseCase;
use App\Infrastructure\Library\JsonResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class PostController {

  public function __construct (
    private readonly PostUseCase $postUseCase,
    private readonly JsonResponse $jsonResponse
  ) {}

  public function getPosts (Request $request, Response $response): Response {
    $body = $request->getParsedBody();
    $currentPage = $body['page'] ?? 1;
    $tags = $body['tags'] ?? [];

    $posts = $this->postUseCase->getPosts($currentPage, $tags);
    return $this->jsonResponse->success($posts, $response);
  }

  public function getPostsTotal (Request $request, Response $response): Response {
    $body = $request->getParsedBody();
    $tags = $body['tags'] ?? [];

    $postsTotal = $this->postUseCase->getPostsTotal($tags);

    return $this->jsonResponse->success($postsTotal, $response);
  }

  public function getPost (Request $request, Response $response): Response {
    $body = $request->getParsedBody();
    $postUid = $body['uid'] ?? '';

    $post = $this->postUseCase->getPost($postUid);

    return $this->jsonResponse->success($post, $response);
  }

  public function getPostByAccount (Request $request, Response $response): Response {
    $body = $request->getParsedBody();
    $postUid = $body['uid'] ?? '';
    $accountUid = $body['token'] ?? '';
    $post = $this->postUseCase->getPostByAccount($postUid, $accountUid);

    return $this->jsonResponse->success($post, $response);
  }

  public function editPost (Request $request, Response $response): Response {
    $body = $request->getParsedBody();
    $postUid = $body['uid'] ?? '';
    $entry = $body['entry'] ?? '';
    $title = $body['title'] ?? '';
    $tags = $body['tags'] ?? '';
    $content = $body['content'] ?? '';
    $accountUid = $body['token'] ?? '';

    $this->postUseCase->editPost($accountUid, $postUid, $title, $entry, $content, $tags);

    return $this->jsonResponse->success('', $response);
  }

  public function createPost (Request $request, Response $response): Response {
    $body = $request->getParsedBody();
    $server = $request->getServerParams();
    $title = $body['title'] ?? '';
    $entry = $body['entry'] ?? '';
    $content = $body['content'] ?? '';
    $tags = $body['tags'] ?? '';
    $nameAccount = $server['HTTP_USER_AGENT'] ?? '';
    $accountUid = $body['token'] ?? '';

    $out = $this->postUseCase->createPost($accountUid, $nameAccount, $title, $entry, $content, $tags);

    return $this->jsonResponse->success($out, $response);
  }

  public function deletePost (Request $request, Response $response): Response {
    $body = $request->getParsedBody();
    $uid = $body['uid'] ?? '';
    $accountUid = $body['token'] ?? '';

    $this->postUseCase->deletePost($uid, $accountUid);

    return $this->jsonResponse->success('', $response);
  }

  public function getTags (Request $request, Response $response): Response {
    $tags = $this->postUseCase->getTags();
    return $this->jsonResponse->success($tags, $response);
  }

  public function createTags (Request $request, Response $response): Response {
    $body = $request->getParsedBody();
    $labels = $body['labels'] ?? '';

    $newTags = $this->postUseCase->createTags($labels);
    return $this->jsonResponse->success($newTags, $response);
  }
}