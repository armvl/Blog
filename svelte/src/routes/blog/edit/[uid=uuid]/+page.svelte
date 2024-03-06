<script>
  import {onMount} from 'svelte'
  import {page} from '$app/stores'
  import {pageMetaStore} from '$lib/js/store'
  import {prepareDataPost, request, success, warning, deletePost} from '$lib/js/common'
  import Button from '$lib/controls/Button.svelte'
  import Title from '$lib/blog/Title.svelte'
  import Editor from '$lib/editor/Editor.svelte'
  import Loader from '$lib/blog/Loader.svelte'
  import Tags from '$lib/blog/Tags.svelte'
  import Page from '$lib/blog/Page.svelte'

  let
    post,
    editor,
    uid = $page.params.uid,
    title = '',
    accountUid = localStorage.getItem('token'),
    value = '',
    tags = [],
    loadingSave,
    loadingDelete

  onMount(async () => {
    const json = await request('getPostByAccount', {uid})

    if ( ! json.success)
      return warning(json.msg)

    post = json.data.post

    if (post && post.uid) {
      value = post.entry ? post.entry +'<p>more</p>'+ post.content : post.content
      title = post.title
      tags = json.data.tags
    }

    $pageMetaStore.title = `Редактирование поста "${title}"`
  })

  async function clickSave (e) {
    if (loadingSave || loadingDelete) return

    const result = prepareDataPost(title, tags, editor)

    if (typeof result === 'string')
      return warning(result)

    if ( ! result || ! result.content)
      return

    loadingSave = true

    const json = await request('editPost', {uid, ...result})

    loadingSave = false

    if ( ! json.success)
      return warning(json.msg)

    success(`Пост "<b>${post.title}</b>" обновлен! &nbsp; <a href="/blog/${uid}">Перейти</a>`)
  }

  async function clickDelete (e) {
    if (loadingSave || loadingDelete) return
    loadingDelete = true

    await deletePost(uid)

    loadingDelete = false
  }

</script>

{#if post}
  {#if post.uid}
    {#if post.account === accountUid}
      <Page sidebarTitle="Выберите теги поста">
        <svelte:fragment slot="sidebar">
          <Tags bind:tags changePost />
        </svelte:fragment>

        <div class="top">
          <Button text="Сохранить"
                  state="primary"
                  click={clickSave}
                  bind:disabled={loadingDelete}
                  bind:loading={loadingSave} />

          <Button text="Удалить"
                  state="danger"
                  click={clickDelete}
                  bind:disabled={loadingSave}
                  bind:loading={loadingDelete}
                  left />
        </div>

        <div>
          <Title bind:value={title} />
          <Editor bind:value bind:editor />
        </div>
      </Page>
    {:else}
      Access denied
    {/if}
  {:else}
    <span class="notFound">Пост не найден</span>
  {/if}
{:else}
  <Loader block />
{/if}

<style lang="less">
  .top {
    display: flex;
    justify-content: flex-end;
    align-items: center;
  }
  .notFound {
    font-size: 16px;
  }
</style>