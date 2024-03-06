<?php

namespace Test\Functional;

use App\Adapter\Controller\PostController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Depends;
use Psr\Http\Message\ResponseInterface as Response;

#[CoversClass(PostController::class)]
class PostControllerTest extends WebTestCase {

  private string $accountUid = 'd2e17339-8d22-4948-9d76-10df95e42b84';

  public function testGuard () {
    $response = $this->app()->handle($this->request('GET', '/'));
    $json = $this->checkHttpAndGetJson($response);
    $this->assertFalse($json['success']);
    $this->assertEquals('Access denied #100', $json['msg']);
  }

  public function testNotFound () {
    $response = $this->app()->handle($this->request('POST', '/api/somePath'));
    $json = $this->checkHttpAndGetJson($response);
    $this->assertFalse($json['success']);
    $this->assertEquals('Not found.', $json['msg']);
  }

  public function testGetPosts () {
    $this->checkPosts($this->app()->handle($this->requestPost('/api/getPosts')));
  }

  public function testGetPostsByTags () {
    $this->checkPosts($this->app()->handle($this->requestPost('/api/getPosts', ['tags' => [95, 104, 90]])));
  }

  public function testGetPostsTotal () {
    $response = $this->app()->handle($this->requestPost('/api/getPostsTotal'));
    $json = $this->checkHttpAndGetJson($response);
    $this->assertGreaterThan(0, $json['data']);
  }

  public function testGetPostsTotalByTags () {
    $response = $this->app()->handle($this->requestPost('/api/getPostsTotal', ['tags' => [95, 104, 90]]));
    $json = $this->checkHttpAndGetJson($response);
    $this->assertGreaterThan(0, $json['data']);
  }

  public function testGetPost () {
    $this->checkPost($this->app()->handle($this->requestPost('/api/getPost', [
      'uid' => '87d7543f-bd11-4e42-82de-3a90360e2ccd'
    ])));
  }

  public function testGetPostByAccount () {
    $this->checkPost($this->app()->handle($this->requestPost('/api/getPostByAccount', [
      'uid' => '87d7543f-bd11-4e42-82de-3a90360e2ccd',
      'token' => $this->accountUid,
    ])));
  }

  public function testGetTags () {
    $response = $this->app()->handle($this->requestPost('/api/getTags'));
    $json = $this->checkHttpAndGetJson($response);

    $this->assertTrue($json['success']);
    $this->assertIsArray($json['data']);
    $this->assertGreaterThan(0, count($json['data']));

    $tags = $json['data'][0];

    $this->assertIsString($tags['label']);
    $this->assertGreaterThan(0, $tags['id']);
  }

  public function testCreatePost () {
    $response = $this->app()->handle($this->requestPost('/api/createPost', [
      'title' => 'test title',
      'entry' => 'test entry',
      'content' => 'test content',
      'token' => $this->accountUid,
    ]));

    $json = $this->checkHttpAndGetJson($response);

    $this->assertTrue($json['success']);
    $this->assertIsArray($json['data']);

    $data = $json['data'];

    $this->assertMatchesRegularExpression('/^[a-f0-9-]{36}$/i', $data['account']);
    $this->assertMatchesRegularExpression('/^[a-f0-9-]{36}$/i', $data['uid']);

    $this->checkTags($data);

    return $data['uid'];
  }

  #[Depends('testCreatePost')]
  public function testEditPost (string $postUid) {
    $response = $this->app()->handle($this->requestPost('/api/editPost', [
      'title' => 'edit test title',
      'entry' => 'edit test entry',
      'content' => 'edit test content',
      'token' => $this->accountUid,
      'tags' => [95, 104, 90],
      'uid' => $postUid,
    ]));

    $json = $this->checkHttpAndGetJson($response);

    $this->assertTrue($json['success']);
    $this->assertEquals('', $json['data']);

    return $postUid;
  }

  #[Depends('testEditPost')]
  public function testDeletePost (string $postUid) {
    $response = $this->app()->handle($this->requestPost('/api/deletePost', [
      'token' => $this->accountUid,
      'uid' => $postUid,
    ]));

    $json = $this->checkHttpAndGetJson($response);

    $this->assertTrue($json['success']);
    $this->assertEquals('', $json['data']);
  }

  private function checkHttpAndGetJson (Response $response): array {
    $this->assertEquals(200, $response->getStatusCode());
    $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));

    $json = json_decode(strval($response->getBody()), true);
    $this->assertIsArray($json);

    return $json;
  }

  private function checkPosts (Response $response): void {
    $json = $this->checkHttpAndGetJson($response);

    $this->assertTrue($json['success']);
    $this->assertIsArray($json['data']);

    $data = $json['data'];

    $this->assertEquals(1, $data['currentPage']);

    $this->assertGreaterThan(0, $data['totalPages']);
    $this->assertGreaterThan(0, $data['totalPosts']);

    $this->checkTags($data);

    $this->assertIsArray($data['posts']);
    $this->assertGreaterThan(0, count($data['posts']));

    $post = $data['posts'][0];

    $this->assertMatchesRegularExpression('/^[a-f0-9-]{36}$/i', $post['account']);
    $this->assertMatchesRegularExpression('/^[a-f0-9-]{36}$/i', $post['uid']);
    $this->assertIsString($post['entry']);
    $this->assertIsString($post['title']);
    $this->assertGreaterThan(0, $post['upd']);
  }

  private function checkPost (Response $response): void {
    $json = $this->checkHttpAndGetJson($response);

    $this->assertTrue($json['success']);
    $this->assertIsArray($json['data']);

    $data = $json['data'];

    $this->checkTags($data);

    $this->assertIsArray($data['post']);

    $post = $data['post'];

    $this->assertMatchesRegularExpression('/^[a-f0-9-]{36}$/i', $post['account']);
    $this->assertMatchesRegularExpression('/^[a-f0-9-]{36}$/i', $post['uid']);
    $this->assertIsString($post['content']);
    $this->assertIsString($post['entry']);
    $this->assertIsString($post['title']);
    $this->assertGreaterThan(0, $post['upd']);
  }

  private function checkTags (array $data): void {
    $this->assertIsArray($data['tags']);
    $this->assertGreaterThan(0, count($data['tags']));

    $tag = $data['tags'][0];

    $this->assertIsString($tag['label']);
    $this->assertGreaterThan(0, $tag['id']);
  }
}