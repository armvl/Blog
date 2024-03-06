<script>
  import {createEventDispatcher} from 'svelte'
  import {pluralPosts} from '$lib/js/common'

  export let
    page,
    loading,
    currentPage,
    totalPages,
    totalPosts

  const
    dispatch = createEventDispatcher()

  function onClick (i) {
    dispatch('click', {page: i})
  }
</script>

<div class="pagination">
  <div class="total">
    <span>Всего</span>
    {totalPosts} {pluralPosts(totalPosts)}
  </div>

  {#if totalPages && totalPages !== 1}
    {#each Array(totalPages) as _, i (i)}
      <span class="item"
         on:click={() => onClick(i + 1)}
         class:process={loading && page === (i + 1)}
         class:active={currentPage === (i + 1)}>
        {i + 1}
      </span>
    {/each}
  {/if}
</div>

<style lang="less">
  @import '$lib/css/vars.less';

  :root {
    --pgnItemBackground: @pgnItemBackground;
    --pgnItemBackgroundHover: darken(@pgnItemBackground, 5%);
    --pgnItemBackgroundActive: darken(@pgnItemBackground, 10%);
    --pgnTotalColor: @pgnTotalColor;
  }

  .pagination {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    flex-wrap: wrap;
    //font-size: 16px;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    margin-top: -3px;
  }
  .total {
    margin-right: 10px;
    color: var(--pgnTotalColor);
  }
  .item {
    text-decoration: none;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 3px 5px 0;
    cursor: pointer;
    width: 34px;
    height: 34px;
    background: var(--pgnItemBackground);
    border-radius: 50%;
    &.active {
      background: var(--pgnItemBackgroundActive);
    }
    &:hover {
      background: var(--pgnItemBackgroundHover);
    }
  }
  :global(.max400) {
    .pagination {
      font-size: 12px;
    }
  }
  :global(.max350) {
    .total {
      span {
        display: none;
      }
    }
  }
</style>