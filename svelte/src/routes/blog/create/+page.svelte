<script>
  import {prepareDataPost, request, success, warning} from '$lib/js/common'
  import {pageMetaStore} from '$lib/js/store'
  import {onMount} from 'svelte'
  import Title from '$lib/blog/Title.svelte'
  import Page from '$lib/blog/Page.svelte'
  import Tags from '$lib/blog/Tags.svelte'
  import Editor from '$lib/editor/Editor.svelte'
  import fish from '$lib/js/fish'
  import Button from '$lib/controls/Button.svelte'

  let
    editor,
    title = '',
    tpl = '<p>Краткое вступление к посту</p><p>more</p><p>Содержимое поста</p>',
    value = tpl,
    tags = [],
    loading

  $pageMetaStore.title = 'Создание поста'

  onMount(async () => {
    const json = await request('getTags')

    if ( ! json.success)
      return warning(json.msg)

    tags = json.data
  })

  function fillByFish (e) {
    if ( ! editor || ! editor.getContent)
      return

    editor.setContent(fish.entry +'<p>more</p>'+ fish.content)
    title = fish.title
  }

  async function save (e) {
    const result = prepareDataPost(title, tags, editor)

    if (typeof result === 'string')
      return warning(result)

    if ( ! result || ! result.content)
      return

    loading = true

    const json = await request('createPost', result)

    loading = false

    if ( ! json.success)
      return warning(json.msg)

    const token = localStorage.getItem('token')

    if ( ! token || token !== json.data.account)
      localStorage.setItem('token', json.data.account)

    if (json.data.tags)
      tags = json.data.tags

    success(`Пост "<b>${title}</b>" создан! &nbsp; <a href="/blog/${json.data.uid}">Перейти</a>`)

    title = ''
    editor.setContent(tpl)
  }

</script>

<Page sidebarTitle="Выберите теги поста">
  <svelte:fragment slot="sidebar">
    <Tags bind:tags changePost />
  </svelte:fragment>

  <div class="top">
    <Button text="Сохранить"
            state="primary"
            click={save}
            bind:loading={loading} />

    <Button text="Заполнить рыбой"
            click={fillByFish}
            left />
  </div>

  <div>
    <Title placeholder="Введите заголовок" bind:value={title} />
    <Editor bind:value bind:editor />
  </div>
</Page>

<style lang="less">
  .top {
    display: flex;
    justify-content: flex-end;
    align-items: center;
  }
</style>