<?php

namespace App\Infrastructure\Library;

use PHPUnit\Logging\Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Psr7\Factory\ResponseFactory;

class JsonResponse {

  private ?Response $response = null;

  public function success ($data = [], ?Response $response = null): Response {
    if ($response)
      $this->setResponse($response);

    return $this->out(['success' => true, 'data' => $data]);
  }

  public function fail ($msg = '', ?Response $response = null): Response {
    if ($response)
      $this->setResponse($response);

    return $this->out(['success' => false, 'msg' => $msg]);
  }

  private function setResponse (?Response $response = null): static {
    $this->response = $response;
    return $this;
  }

  private function out ($out = []): Response {
    if ( ! $this->response)
      $this->response = (new ResponseFactory())->createResponse(200);

    $this->response->getBody()->write(json_encode($out, JSON_UNESCAPED_UNICODE));
    return $this->response->withHeader('Content-Type', 'application/json');
  }
}