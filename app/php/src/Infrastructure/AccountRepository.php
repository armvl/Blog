<?php

namespace App\Infrastructure;

use App\Logic\Contract\IAccountRepository;
use Dibi\Connection;

class AccountRepository implements IAccountRepository {

  public function __construct (
    private readonly Connection $db
  ) {}

  public function getAccountByUid ($uid): array {
    return (array) $this->db->fetch('select * from account where uid = %s', $uid);
  }

  public function insertAccount ($nameAccount = ''): array {
    return (array) $this->db->fetch('insert into account %v returning *', [
      'name' => $nameAccount,
    ]);
  }
}