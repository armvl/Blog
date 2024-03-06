<?php

namespace App\Logic\Contract;

interface ITagRepository {
  public function getTags (): array;
  public function getTagIdsByPostId ($postId): array;
  public function getTagsByPostId ($postId): array;
  public function getTagsByIds (array $ids): array;
  public function getTagsByLabels (array $labels): array;
  public function deleteTagsByPostId ($postId): void;
  public function deleteTags ($postId, array $ids): void;
  public function insertTags ($postId, array $ids): void;
  public function createTags (array $labels): array;
}