<?php

namespace App\Infrastructure;

use Dibi\Connection;
use App\Logic\Contract\IPostRepository;

class PostRepository implements IPostRepository {

  public function __construct (
    private readonly Connection $db
  ) {}

  public function getPosts ($offset, $limit, $tags = []): array {
    $rows = $this->db->fetchAll('select 
                post.uid, 
                extract(epoch from post.update)::int upd,
                title, 
                entry, 
                account.uid as account 
              from post 
                join account on account.id = post.account_id 
              %if', ! empty($tags), 'where post.id in (select distinct post_id from post_tags where tag_id in (%i))', $tags, '%end
              order by post.id desc 
              offset %i limit %i', $offset, $limit);

    return array_map(fn ($item) => (array) $item, $rows);
  }

  public function getPostsTotal ($tags = []): int {
    return $this->db->fetchSingle('select count(id) from post 
             %if', ! empty($tags),
              'where id in (select distinct post_id from post_tags where tag_id in (%i))', $tags,
            '%end');
  }

  public function getPostByUid ($postUid): array {
    $sql = 'select post.id, 
                   post.uid, 
                   title, 
                   entry, 
                   content, 
                   extract(epoch from post.update)::int upd, 
                   account.uid as account 
              from post 
              join account on account.id = post.account_id 
              where post.uid = %s';

    return (array) $this->db->fetch($sql, $postUid);
  }

  public function getPostByUidAndAccountId ($postUid, $accountId): array {
    $sql = 'select id, uid, title, entry, content 
              from post 
              where uid = %s 
                and account_id = %i';

    return (array) $this->db->fetch($sql, $postUid, $accountId);
  }

  public function getPostByUidAndAccountUid ($postUid, $accountUid): array {
    $sql = 'select post.id, 
                   post.uid, 
                   title, 
                   entry, 
                   content, 
                   extract(epoch from post.update)::int upd, 
                   account.uid as account 
              from post, account
              where post.uid = %s 
                and post.account_id = account.id
                and account.uid = %s';

    return (array) $this->db->fetch($sql, $postUid, $accountUid);
  }

  public function getPostIdByUid ($uid): int {
    return $this->db->fetchSingle('select id from post where uid = %s', $uid);
  }

  public function editPost ($id, $title, $entry, $content): void {
    $fields = [
      'update' => 'now()',
      'entry' => $entry,
      'title' => $title,
      'content' => $content,
    ];

    $this->db->query('update post set', $fields, 'where id = %i', $id);
  }

  public function insertPost ($accountId, $title, $entry, $content): array {
    $fields = [
      'account_id' => $accountId,
      'title' => $title,
      'entry' => $entry,
      'content' => $content,
    ];

    return (array) $this->db->fetch('insert into post %v returning *', $fields);
  }

  public function deletePost ($uid): void {
    $this->db->query('delete from post where uid = %s', $uid);
  }
}