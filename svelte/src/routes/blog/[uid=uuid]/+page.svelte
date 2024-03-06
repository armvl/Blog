<script>
  import '$lib/editor/formatted.css'

  import {onMount} from 'svelte'
  import {request, warning, deletePost, dateFormat} from '$lib/js/common'
  import {page} from '$app/stores'
  import {pageMetaStore} from '$lib/js/store'
  import Loader from '$lib/blog/Loader.svelte'
  import Button from '$lib/controls/Button.svelte'
  import Tags from '$lib/blog/Tags.svelte'

  let
    post,
    uid = $page.params.uid,
    accountUid = localStorage.getItem('token'),
    tags = []

  async function clickDelete (e) {
    await deletePost(uid)
  }

  onMount(async () => {
    const json = await request('getPost', {uid})

    if ( ! json.success)
      return warning(json.msg)

    tags = json.data.tags
    post = json.data.post
    $pageMetaStore.title = post.title
  })
</script>

{#if post}
  {#if post.uid}
    {#if post.account === accountUid}
      <div class="top">
        <Button text="Редактировать" state="primary" url="/blog/edit/{post.uid}" />
        <Button text="Удалить" state="danger" click={clickDelete} left />
      </div>
    {/if}

    <div>
      <h1>
        <span>{post.title}</span>
      </h1>

  <!--    <div class="content formatted ck-content">-->
      <div class="content formatted">
        {@html post.entry}
        {@html post.content}
      </div>

      <div class="update">
        Обновлен:
        <span>{dateFormat(post.upd)}</span>
      </div>

      {#if tags && tags.length}
        <div class="tags">Теги:</div>
      {/if}

      <Tags bind:tags alignLeft />
    </div>
  {:else}
    <span class="notFound">Пост не найден</span>
  {/if}
{:else}
  <Loader block />
{/if}

<style lang="less">
  h1 {
    font-weight: 700;
    font-size: 26px;
    line-height: 1.2em;
    margin-bottom: .4em;
    padding-top: .8em;
    padding-bottom: .2em;
    border-bottom: 1px solid #e9e9e9;
    span {
      //background: #f1f1f6;
      //border-radius: 6px;
      padding: 6px 0;
    }
  }
  .top {
    display: flex;
    justify-content: flex-end;
    align-items: center;
  }
  .content {
    //line-height: 1.4em;
    padding: 5px 0 18px 0;
    font-size: 16px;
    &::after {
      content: '';
      display: block;
      clear: both;
    }
  }
  .notFound {
    font-size: 16px;
  }
  .tags {
    border-top: 1px solid #cbcbcb;
    padding-top: 15px;
    font-weight: 500;
  }
  :global(.max400) {
    h1 {
      font-size: 24px;
    }
  }
  .update {
    color: var(--labelDateColor);
    margin: 15px 0;
    span {
      font-style: italic;
      color: var(--labelDateStripeColor);
      background: var(--labelDateStripeBackground);
      padding: 3px 8px 3px 6px;
      border-radius: 16px;
    }
  }
</style>