<script>
  import {pageMetaStore} from '$lib/js/store'
  import Toaster from '$lib/toast/Toaster.svelte'
  import Button from '$lib/controls/Button.svelte'

  const
    title = 'Блог'

  $pageMetaStore.title = title

</script>

<h1>{title}</h1>

<p>Проект представляет собой пример простого блога. Имеется возможность создавать, редактировать, удалять посты. Постам можно назначить метки что сортирует их по разделам. Регистрация не нужна, новым пользователем является любой веб-браузер открывший сайт и посредством которого создается новый пост.</p>

<p>Под капотом SvelteKit для фронтенда и Slim Framework для серверного API.</p>

<p>Фронтенд это SPA что не мешает добавить ему и серверной обработки с нулевыми изменениями приложения. То есть нужно просто начать писать серверные обработчики.</p>

<p>Бекенд это API сервис с которым общается фронтенд. Он реализует чистую архитектуру.</p>

<p>
  <Button text="Перейти к постам" state="primary" url="/blog/1" />
</p>

<style lang="less">
  h1 {
    margin-bottom: 20px;
    font-size: 22px;
    font-weight: 500;
  }
  p {
    font-size: 16px;
    margin: 15px 0;
    line-height: 1.5em;
  }
</style>