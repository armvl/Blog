<script>
  import {request, success, warning, pluralPosts} from '$lib/js/common'
  import {goto} from '$app/navigation'
  import IconTick from '$lib/icons/Tick.svelte'
  import IconPlus from '$lib/icons/Plus.svelte'
  import IconCross from '$lib/icons/Cross.svelte'
  import Loader from '$lib/blog/Loader.svelte'

  export let
    tags,
    changePost = false,
    alignLeft = false

  let
    label = '',
    loading,
    focusedTag,
    totalPosts,
    timeout,
    abortController = null

  function clickSelectTag (i) {
    if (loading && abortController)
      abortController.abort()

    tags[i].selected = ! tags[i].selected

    if ( ! changePost) {
      focusedTag = i
      totalPosts = false
      clearTimeout(timeout)
      timeout = setTimeout(getTotalPosts, 700)
    }
  }

  async function getTotalPosts () {
    const requestTags = tags.filter(tag => tag.selected).map(tag => tag.id)

    if (loading) return
    loading = true
    totalPosts = false

    abortController = new AbortController()

    const json = await request('getPostsTotal', {tags: requestTags}, abortController)

    if (json.success)
      totalPosts = json.data

    loading = false
  }

  async function clickTail () {
    if (loading && abortController)
      abortController.abort()

    if (totalPosts === 0)
      return

    const requestTags = tags.filter(tag => tag.selected).map(tag => tag.id).join(',')
    await goto('/blog/1' + (requestTags ? '/?tags=' + requestTags : ''), {invalidateAll: true})
    focusedTag = false
  }

  async function clickTailClose () {
    if (loading && abortController)
      abortController.abort()

    focusedTag = false
  }

  async function clickAddTag (e) {
    if (loading) return

    if ( ! /^[a-zа-я0-9., -]+$/iu.test(label))
      return warning('Можно использовать только буквенно-цифровые символы! Теги можно разделять запятой.')

    let labels = label
      .replace(/(-{2,})|(\s{2,})|(,{2,})/g, (m, a, b, c) => a ? '-' : (b ? ' ' : (c ? ',' : '')))
      .split(',')
      .map(label => label.trim().toLowerCase())
      .filter(label => label && ! /^[-\s]+$/.test(label))

    if (labels.length > 10)
      return warning('Можно создать до 10 тегов за один раз!')

    loading = true
    const json = await request('createTags', {labels})
    loading = false

    if ( ! json.success)
      return warning(json.msg)

    tags = [...tags, ...json.data].sort((a, b) => a.label < b.label ? -1 : 1)

    success(`Теги "${json.data.map(tag => tag.label).join('", "')}" созданы!`)
  }

</script>


{#if tags && tags.length}
  {#if changePost}
    <div class="new">
      <input bind:value={label} placeholder="Введите новый тег">

      {#if label !== ''}
        <span class:process={loading} on:click={clickAddTag}><IconPlus /></span>
      {/if}
    </div>
  {/if}

  <div class="tags" class:alignLeft>
    {#each tags as tag, i (i)}
      <span class="tag" on:click={() => clickSelectTag(i)}>
        {#if tag.selected}
          <span class="badge"><IconTick /></span>
        {/if}

        {#if ! changePost && focusedTag === i}
          <div class="tail" on:click|stopPropagation={clickTail}>
            <div class="close" on:click|stopPropagation={clickTailClose}><IconCross /></div>

            {#if loading}
              Показать (<span class="load"><Loader white inline /></span>) постов
            {:else if totalPosts && totalPosts !== 0}
              Показать {totalPosts} {pluralPosts(totalPosts)}
            {:else if totalPosts === 0}
              Постов не найдено
            {:else}
              Показать посты
            {/if}

          </div>
        {/if}

        {tag.label}
      </span>
    {/each}
  </div>
{/if}

<style lang="less">
  @import '$lib/css/vars.less';

  :root {
    --tagsTagBackground: @tagsTagBackground;
    --tagsTagBackgroundActive: @tagsTagBackgroundActive;
    --tagsTagColor: @tagsTagColor;
    --tagsBgBadge: @tagsBgBadge;
    --tagsBgBadgeActive: darken(@tagsBgBadge, 5%);
    --tagsInputBorderColor: @tagsInputBorderColor;
    --tagsInputBorderColorActive: darken(@tagsInputBorderColor, 5%);
    --tagsBgTail: @tagsBgTail;
  }

  .tags {
    display: flex;
    margin: 10px -5px 0;
    flex-wrap: wrap;
    //justify-content: space-between;
    &.alignLeft {
      margin: 10px 0 0;
      justify-content: flex-start;
      .tag {
        margin: 10px 10px 0 0 !important;
      }
    }
  }
  .tag {
    text-decoration: none;
    color: var(--tagsTagColor);
    padding: 3px 9px;
    border-radius: 10px;
    background: var(--tagsTagBackground);
    margin: 10px 5px 0 5px;
    position: relative;
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    &:hover {
      background: var(--tagsTagBackgroundActive);
    }
  }
  .tail {
    white-space: nowrap;
    position: absolute;
    top: -7px;
    right: calc(100% + 13px);
    background: var(--tagsBgTail);
    padding: 10px 15px 10px 37px;
    border-radius: 10px;
    z-index: 1;
    color: #fff;
    //box-shadow: 0 0 10px 2px rgba(0, 0, 0, 0.5);
    font-weight: 500;
    &:after {
      content: '';
      display: block;
      width: 14px;
      height: 14px;
      position: absolute;
      top: calc(50% - 7px);
      right: -6px;
      transform: rotate(45deg);
      z-index: -1;
      background: var(--tagsBgTail);
    }
    .close {
      position: absolute;
      top: calc(50% - 11px);
      left: 7px;
      background: rgba(255,255,255,.25);
      border-radius: 50%;
      width: 22px;
      height: 22px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      &:hover {
        background: rgba(255,255,255,.15);
      }
      :global(svg) {
        fill: #fff;
      }
    }
    .load {
      display: inline-flex;
      position: relative;
      top: 2px;
    }
  }
  .badge {
    position: absolute;
    top: -4px;
    right: -8px;
    background: var(--tagsBgBadge);
    fill: #fff;
    border-radius: 50%;
    width: 16px;
    height: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    :global(svg) {
      width: 10px;
      height: 10px;
    }
  }
  .new {
    position: relative;
    margin-top: 20px;
    input {
      background: #fff;
      border: 2px solid var(--tagsInputBorderColor);
      border-radius: 20px;
      padding: 6px 27px 6px 11px;
      width: 100%;
      &:focus {
        border-color: var(--tagsInputBorderColorActive);
      }
    }
    span {
      position: absolute;
      top: 1px;
      right: 0;
      background: var(--tagsBgBadge);
      border-radius: 50%;
      width: 30px;
      height: 30px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      &:hover {
        background: var(--tagsBgBadgeActive);
      }
      :global(svg) {
        fill: #fff;
      }
    }
  }
  :global(.max900) {
    .tags {
      justify-content: flex-start;
    }
  }
  :global(.max550) {
    .tags {
      justify-content: space-between;
    }
  }
</style>