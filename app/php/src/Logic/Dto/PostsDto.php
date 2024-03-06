<?php

namespace App\Logic\Dto;

class PostsDto {

  public function __construct (
    public int $totalPosts = 0,  // сколько всего постов запрошено
    public int $totalPages = 0,  // сколько страниц имеется по запросу
    public int $currentPage = 1, // текущая страница
    public array $posts = [],    // запрошенные посты
    public array $tags = []      // все теги
  ) {}
}