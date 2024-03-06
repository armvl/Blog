<?php

namespace App\Logic;

class Validator {
  const UUID = '/^[a-f0-9-]{36}$/i';

  public function isUuid ($value): bool {
    return is_string($value) && preg_match(self::UUID, $value);
  }

  public function isAccountUid ($value): bool {
    return $this->isUuid($value);
  }
}