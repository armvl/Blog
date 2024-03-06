<?php

namespace App\Logic\Contract;

interface IPostRepository {
  public function getPosts ($offset, $limit, $tags): array;
  public function getPostsTotal (): int;
  public function getPostByUid ($uid): array;
  public function getPostByUidAndAccountId ($postUid, $accountId): array;
  public function getPostByUidAndAccountUid ($postUid, $accountUid): array;
  public function getPostIdByUid ($uid): int;
  public function editPost ($id, $title, $entry, $content): void;
  public function insertPost ($accountId, $title, $entry, $content): array;
  public function deletePost ($uid): void;
}