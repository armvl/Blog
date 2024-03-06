<script>
  import Button from '$lib/controls/Button.svelte'
  import {dateFormat} from '$lib/js/common'

  export let
    post

  let
    accountUid = localStorage.getItem('token'),
    isEdit = post.account === accountUid,
    date = dateFormat(post.upd)

</script>

<div class="entry">
  <h2><a href="/blog/{post.uid}">{post.title}</a></h2>

  <div class="text formatted">{@html post.entry || ''}</div>

  <div class="actions">
    <div class="left">
      <Button text="Читать далее" url="/blog/{post.uid}" />

      {#if isEdit}
        <Button text="Редактировать" url="/blog/edit/{post.uid}" left />
      {/if}
    </div>

    <div class="right">
      Обновлен:
      <span>{date}</span>
    </div>
  </div>
</div>

<style lang="less">
  @import '$lib/css/vars.less';

  :root {
    --entryTitleHoverColor: @entryTitleHoverColor;
    --entryBorderColor: @entryBorderColor;
  }

  h2 {
    font-weight: 700;
    font-size: 24px;
    padding: 10px 0;
    a, a:link, a:hover, a:visited, a:focus {
      color: inherit;
      text-decoration: none;
    }
    a:hover {
      color: var(--entryTitleHoverColor);
    }
  }
  .entry {
    border-bottom: 1px solid var(--entryBorderColor);
    padding: 10px 0 20px 0;
    position: relative;
  }
  .text {
    line-height: 1.4em;
    padding: 5px 0 18px 0;
    font-size: 16px;
  }
  .actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  .left {
    flex: 1 1 auto;
  }
  .right {
    color: var(--labelDateColor);
    span {
      font-style: italic;
      color: var(--labelDateStripeColor);
      background: var(--labelDateStripeBackground);
      padding: 3px 8px 3px 6px;
      border-radius: 16px;
    }
  }
  :global(.max550) {
    .actions {
      align-items: flex-start;
      flex-direction: column;
      .right {
        margin-top: 20px;
      }
    }
  }
  :global(.max400) {
    h2 {
      font-size: 22px;
    }
    .actions {
      .right {
        font-size: 12px;
      }
    }
  }
</style>