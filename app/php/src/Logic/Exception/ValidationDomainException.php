<?php

namespace App\Logic\Exception;

class ValidationDomainException extends \DomainException {

  public function __construct (
    string $message = 'Invalid input.',
    int $code = 0,
    ?\Throwable $previous = null
  ) {
    parent::__construct($message, $code, $previous);
  }
}