<?php

namespace App\Infrastructure;

use App\Logic\Contract\ITagRepository;
use dibi;
use Dibi\Connection;

class TagRepository implements ITagRepository {

  public function __construct (
    private readonly Connection $db
  ) {}

  public function getTags (): array {
    $rows = $this->db->fetchAll('select * from tag order by label');
    return array_map(fn ($item) => (array) $item, $rows);
  }

  public function getTagIdsByPostId ($postId): array {
    $rows = $this->db->fetchAll('select tag_id from post_tags where post_id = %i', $postId);
    return array_map(fn ($item) => intval($item['tag_id']), $rows);
  }

  public function getTagsByPostId ($postId): array {
    $sql = 'select * from tag 
              where id in (
                select tag_id from post_tags 
                where post_id = %i
              ) order by label';

    $rows = $this->db->fetchAll($sql, $postId);

    return array_map(fn ($item) => (array) $item, $rows);
  }

  public function getTagsByIds (array $ids): array {
    $rows = $this->db->fetchAll('select * from tag where id in (%i)', $ids);
    return array_map(fn ($item) => (array) $item, $rows);
  }

  public function getTagsByLabels (array $labels): array {
    $rows = $this->db->fetchAll('select * from tag where label in (%s)', $labels);
    return array_map(fn ($item) => (array) $item, $rows);
  }

  public function deleteTagsByPostId ($postId): void {
    $this->db->query('delete from post_tags where post_id = %i', $postId);
  }

  public function deleteTags ($postId, array $ids): void {
    $this->db->query('delete from post_tags where post_id = %i and tag_id in (%i)', $postId, $ids);
  }

  public function insertTags ($postId, array $ids): void {
    $values = array_map(fn ($id) => ['tag_id' => $id, 'post_id' => $postId], $ids);
    $this->db->query('insert into post_tags', ...$values);
  }

  public function createTags (array $labels): array {
    $values = "('".join("'),('", $labels)."')";
    $rows = $this->db->fetchAll('insert into tag(label) values %sql returning *', $values);

    return array_map(fn ($item) => (array) $item, $rows);
  }
}