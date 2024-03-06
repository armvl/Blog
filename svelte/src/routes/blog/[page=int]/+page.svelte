<script>
  import '$lib/editor/formatted.css'

  import {goto} from '$app/navigation'
  import {onDestroy} from 'svelte'
  import {request, warning} from '$lib/js/common'
  import {loaderPostsStore, pageMetaStore} from '$lib/js/store'
  import {max550} from '$lib/js/media'
  import Loader from '$lib/blog/Loader.svelte'
  import Entry from '$lib/blog/Entry.svelte'
  import Button from '$lib/controls/Button.svelte'
  import Pagination from '$lib/blog/Pagination.svelte'
  import Page from '$lib/blog/Page.svelte'
  import Tags from '$lib/blog/Tags.svelte'

  export let
    data

  let
    requestPage = 1,
    requestTags = [],
    posts,
    normalizedCurrentPage,
    totalPages,
    totalPosts,
    tags = [],
    loadingPaginate = false,
    loading,
    loaderPostsUnsubscribe = loaderPostsStore.subscribe(value => loading = value)

  $: data && getPosts()

  async function getPosts () {
    // disableScrollHandling()

    requestPage = data.page
    requestTags = data.tags

    loaderPostsStore.set(true)

    const json = await request('getPosts', {
      page: requestPage,
      tags: requestTags
    })

    loaderPostsStore.set(false)

    if ( ! json.success)
      return warning(json.msg)

    posts = json.data.posts
    normalizedCurrentPage = json.data.currentPage
    totalPages = json.data.totalPages
    totalPosts = json.data.totalPosts
    tags = json.data.tags
    $pageMetaStore.title = 'Блог. Страница ' + normalizedCurrentPage
    loadingPaginate = false
  }

  async function clickPagination(e) {
    if ( ! loading && requestPage !== e.detail.page) {
      loadingPaginate = true
      await goto('/blog/' + e.detail.page + (requestTags.length ? '/?tags=' + requestTags.join(',') : ''), {invalidateAll: true})
    }
  }

  onDestroy(loaderPostsUnsubscribe)
</script>

<div class="blog">
  {#if posts}
    <Page sidebarTitle="Посты по тегам">
      <svelte:fragment slot="sidebar">
        <Tags bind:tags={tags} />
      </svelte:fragment>

      {#if posts.length}
        <div class="top">
          <div>
            {#if ! $max550}
              <Button text="Создать пост" state="primary" url="/blog/create" />
            {/if}
          </div>

          <Pagination
            bind:loading={loadingPaginate}
            bind:page={requestPage}
            bind:totalPages
            bind:totalPosts
            bind:currentPage={normalizedCurrentPage}
            on:click={clickPagination} />
        </div>

        <div>
          {#each posts as post (post.uid)}
            <Entry {post}/>
          {/each}
        </div>

        <div class="bottom">
          <Pagination
            bind:loading={loadingPaginate}
            bind:page={requestPage}
            bind:totalPages
            bind:totalPosts
            bind:currentPage={normalizedCurrentPage}
            on:click={clickPagination} />
        </div>
      {:else}
        <span class="notFound">Постов не найдено</span>
      {/if}
    </Page>
  {:else}
    <div class="load">
      <Loader block/>
    </div>
  {/if}
</div>

<style lang="less">
  .blog {
    //border: 1px solid red;
    position: relative;
    min-height: 100px;
  }
  .load {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    //background: rgba(0,0,0,.1);
    background: rgba(255,255,255,.6);
    z-index: 1;
    padding: 27px 0;
  }
  .top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 0 20px;
    border-bottom: 1px solid #e1e1e1;
  }
  .bottom {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    margin: 20px 0 0;
  }
  .notFound {
    font-size: 16px;
  }
</style>