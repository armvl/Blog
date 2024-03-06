<script>
  export let
    sidebarTitle = 'Выбрать по тегам'
</script>

<div class="page">
  <div class="content">
    <slot />
  </div>

  <div class="sidebar">
    <div class="sticky">
      <div class="title">{sidebarTitle}</div>

      {#if $$slots.sidebar}
        <slot name="sidebar"></slot>
      {/if}
    </div>
  </div>
</div>

<style lang="less">
  @import '$lib/css/vars.less';

  :root {
    --sidebarWidth: @sidebarWidth;
    --sidebarBackground: @sidebarBackground;
    --sidebarTitleColor: @sidebarTitleColor;
  }

  .page {
    display: flex;
    position: relative;
  }
  .content {
    flex: 1 1 auto;
    margin-right: 20px;
  }
  .sidebar {
    width: var(--sidebarWidth);
    min-width: var(--sidebarWidth);
    max-width: var(--sidebarWidth);
    background: var(--sidebarBackground);
    border-radius: 10px;
    padding: 20px;
    box-sizing: border-box;
    .title {
      font-size: 18px;
      text-transform: uppercase;
      letter-spacing: .5px;
      color: var(--sidebarTitleColor);
      padding-bottom: 15px;
      border-bottom: 1px solid rgba(0, 0, 0, .15);
    }
  }
  .sticky {
    position: sticky;
    top: 15px;
  }
  :global(.max900) {
    .page {
      --sidebarWidth: 100%;
      flex-direction: column;
      .content {
        margin-right: 0;
        margin-bottom: 20px;
      }
      .sticky {
        position: static;
      }
    }
  }
</style>