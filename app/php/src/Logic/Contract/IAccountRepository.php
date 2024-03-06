<?php

namespace App\Logic\Contract;

interface IAccountRepository {
  public function getAccountByUid ($uid): array;
  public function insertAccount (string $nameAccount): array;
}