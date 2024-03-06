<?php

namespace App\Logic\UseCase;

use App\Logic\Cleaner;
use App\Logic\Contract\IAccountRepository;
use App\Logic\Contract\ITagRepository;
use App\Logic\Contract\IPostRepository;
use App\Logic\Dto\PostsDto;
use App\Logic\Exception\ValidationDomainException;
use App\Logic\Validator;

class PostUseCase {

  public function __construct (
    private readonly IPostRepository $postRepository,
    private readonly ITagRepository $tagRepository,
    private readonly IAccountRepository $accountRepository,
    private readonly Cleaner $cleaner,
    private readonly Validator $validator
  ) {}

  /**
   * @param $currentPage
   * @param $tags [1,23,163]
   * @param $perPage
   * @return PostsDto
   */
  public function getPosts ($currentPage = 0, $tags = [], $perPage = 5): PostsDto {
    $allTags = $this->tagRepository->getTags();

    if (is_array($tags) && ! empty($tags)) {
      $tags = array_unique(
                array_map('intval',
                  array_filter($tags, fn ($tag) => is_numeric($tag))));

      if ($tags) {
        if ($tags = $this->tagRepository->getTagsByIds($tags))
          $preparedTags = array_map(fn($tag) => $tag['id'], $tags);
        else
          return new PostsDto(tags: $allTags);
      }
    }

    $tags = $preparedTags ?? [];

    $totalPosts = $this->postRepository->getPostsTotal($tags);
    $totalPages = ceil($totalPosts / $perPage);
    $currentPageNum = $currentPage ? max(1, abs(intval($currentPage))) : 0;
    $normalizeCurrentPage = max(min($totalPages, $currentPageNum), 1);
    $offset = ($normalizeCurrentPage - 1) * $perPage;
    $limit = $perPage;

    $posts = $this->postRepository->getPosts($offset, $limit, $tags);

    if ($tags) {
      foreach ($allTags as & $tag) {
        $tag['selected'] = in_array($tag['id'], $tags);
      }
    }

    return new PostsDto(
      totalPosts: $totalPosts,
      totalPages: $totalPages,
      currentPage: $normalizeCurrentPage,
      posts: $posts,
      tags: $allTags,
    );
  }

  public function getPostsTotal ($tags = []): int {
    return $this->postRepository->getPostsTotal($tags);
  }

  public function getPost ($postUid): array {
    if ( ! $this->validator->isUuid($postUid))
      throw new ValidationDomainException('Uid not valid');

    if ($post = $this->postRepository->getPostByUid($postUid)) {
      $tags = $this->tagRepository->getTagsByPostId($post['id']);
      unset($post['id']);
    }

    return [
      'post' => $post ?? [],
      'tags' => $tags ?? []
    ];
  }

  public function getPostByAccount ($postUid, $accountUid): array {
    if ( ! $this->validator->isUuid($postUid))
      throw new ValidationDomainException('Uid not valid');

    if ( ! $this->validator->isAccountUid($accountUid))
      throw new ValidationDomainException('Account token not valid');

    if ($post = $this->postRepository->getPostByUidAndAccountUid($postUid, $accountUid)) {
      $tags = $this->tagRepository->getTags();
      $postTags = $this->tagRepository->getTagIdsByPostId($post['id']);

      foreach ($tags as & $tag) {
        $tag['selected'] = in_array($tag['id'], $postTags);
      }

      unset($post['id']);
    }

    return [
      'post' => $post ?? [],
      'tags' => $tags ?? []
    ];
  }

  public function editPost ($accountUid, $postUid, $title, $entry, $content, $tags = []): void {
    if ( ! $this->validator->isUuid($postUid))
      throw new ValidationDomainException('Post uid not valid');

    if ( ! $this->validator->isAccountUid($accountUid))
      throw new ValidationDomainException('Account token not valid');

    $title = trim(strip_tags(strval($title)));

    if (strlen($title) > 300 || strlen($title) < 1)
      throw new ValidationDomainException('Title invalid');

    if ( ! $account = $this->accountRepository->getAccountByUid($accountUid))
      throw new ValidationDomainException('Account not found');

    if ( ! $post = $this->postRepository->getPostByUidAndAccountId($postUid, $account['id']))
      throw new ValidationDomainException('Post not found');

    $content = $this->cleaner->html($content);
    $entry = $this->cleaner->html($entry);

    $this->postRepository->editPost($post['id'], $title, $entry, $content);

    if (is_array($tags)) {
      $postTags = $this->tagRepository->getTagIdsByPostId($post['id']);
      $tags = ! empty($tags) ? array_unique(array_map('intval', $tags)) : [];

      if ($tags && ($tags = $this->tagRepository->getTagsByIds($tags))) {
        $tags = array_map(fn ($tag) => $tag['id'], $tags);

        if ($deleteIds = array_diff($postTags, $tags))
          $this->tagRepository->deleteTags($post['id'], $deleteIds);

        if ($insertIds = array_diff($tags, $postTags))
          $this->tagRepository->insertTags($post['id'], $insertIds);
      }
      else {
        $this->tagRepository->deleteTagsByPostId($post['id']);
      }
    }
  }

  public function createPost ($accountUid, $nameAccount, $title, $entry, $content, $tags = []): array {
    $title = trim(strip_tags(strval($title)));

    if (strlen($title) > 500 || strlen($title) < 1)
      throw new ValidationDomainException('Title invalid');

    if ($accountUid && ! $this->validator->isAccountUid($accountUid))
      throw new ValidationDomainException('Account token not valid');

    if ($accountUid)
      $account = $this->accountRepository->getAccountByUid($accountUid);

    if (empty($account))
      $account = $this->accountRepository->insertAccount($nameAccount);

    $content = $this->cleaner->html($content);
    $entry = $this->cleaner->html($entry);

    $newPost = $this->postRepository->insertPost($account['id'], $title, $entry, $content);

    if (is_array($tags) && ! empty($tags)) {
      $tags = array_unique(array_map('intval', $tags));

      if ($tags && ($tags = $this->tagRepository->getTagsByIds($tags))) {
        $tags = array_map(fn ($tag) => $tag['id'], $tags);
        $this->tagRepository->insertTags($newPost['id'], $tags);
      }
    }

    return [
      'uid' => $newPost['uid'],
      'account' => $account['uid'],
      'tags' => $this->tagRepository->getTags(),
    ];
  }

  public function deletePost ($uid, $accountUid): void {
    if ( ! $this->validator->isUuid($uid))
      throw new ValidationDomainException('Post uid not valid');

    if ( ! $post = $this->postRepository->getPostByUid($uid))
      throw new ValidationDomainException('Пост не найден');

    if ($post['account'] != $accountUid)
      throw new ValidationDomainException('У вас нет прав для удаления этого поста');

    $this->postRepository->deletePost($uid);
  }

  public function getTags (): array {
    return $this->tagRepository->getTags();
  }

  public function createTags ($labels): array {
    if ( ! is_array($labels) || count($labels) > 10)
      throw new ValidationDomainException('Tags invalid');

    $labels = array_filter($labels, fn ($label) => preg_match('#^[a-zа-я0-9. -]+$#u', $label));

    if ($labels && ($intersectTags = $this->tagRepository->getTagsByLabels($labels)))
      $labels = array_diff($labels, array_map(fn ($tag) => $tag['label'], $intersectTags));

    if ( ! $labels)
      throw new ValidationDomainException('Такие теги уже есть!');

    $newTags = $this->tagRepository->createTags($labels);

    return $newTags;
  }
}