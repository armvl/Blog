<script>
  import {goto} from '$app/navigation'
  import {page} from '$app/stores'
  import {fly} from 'svelte/transition'
  import {max550} from '$lib/js/media'
  import IconMenu from '$lib/icons/Menu.svelte'
  import Button from '$lib/controls/Button.svelte'

  let
    menu = [
      {text: 'Блог', url: '/blog/1'},
      {text: 'Postgres', url: '/blog/1?tags=31,61,67,65,66,24'},
      {text: 'Nginx', url: '/blog/1?tags=155,156,157,108'},
      {text: 'JavaScript', url: '/blog/1?tags=168,169'},
      {text: 'PHP', url: '/blog/1?tags=95,104,90'},
    ],
    currentUrl,
    openMenu = false,
    animateDuration = 200

  $: currentUrl = $page.url.pathname + $page.url.search

  async function clickNav (url) {
    if (openMenu)
      toggleMenu()

    if (url !== currentUrl)
      await goto(url, {invalidateAll: true})
  }

  function toggleMenu (e) {
    openMenu = ! openMenu
  }

</script>

{#if $max550}
  <div class="top">
    <div class="btn" on:click={toggleMenu}>
      <IconMenu size={20}/>
    </div>

    <div>
      <Button text="Создать пост" state="primary" url="/blog/create" />
    </div>
  </div>

  {#if openMenu}
    <div class="wrap"
         on:click={toggleMenu}
         transition:fly={{duration: animateDuration }}>

      <nav on:click|stopPropagation
           transition:fly={{ x: -250, duration: animateDuration }}>

        {#each menu as item, i (i)}
          <span on:click={() => clickNav(item.url)}
                class:active={currentUrl.replace(/^\/blog\/[0-9]+/, '/blog/1') === item.url}>
            {item.text}
          </span>
        {/each}
      </nav>
    </div>
  {/if}
{:else}
  <nav>
    {#each menu as item, i (i)}
      <span on:click={() => clickNav(item.url)}
            class:active={currentUrl.replace(/^\/blog\/[0-9]+/, '/blog/1') === item.url}>
        {item.text}
      </span>
    {/each}
  </nav>
{/if}

<style lang="less">
  @import '$lib/css/vars.less';

  :root {
    --navItemColor: @navItemColor;
    --navItemColorHover: @navItemColorHover;
    --navItemBackground: @navItemBackground;
    --navItemBackgroundHover: @navItemBackgroundHover;
  }

  .wrap {
    display: contents;
  }
  nav {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    margin-bottom: 20px;
    padding-bottom: 20px;
    //border-bottom: 1px solid #e1e1e1;
  }
  span {
    color: var(--navColor);
    //background: var(--navItemBackground);
    //border-radius: 10px;
    margin-right: 15px;
    font-size: 16px;
    padding: 6px 12px;
    cursor: pointer;
    font-weight: 500;
    border-bottom: 3px solid var(--navItemBackground);
    &.active,
    &:hover {
      //background: var(--navItemBackgroundHover);
      border-color: var(--navItemBackgroundHover);
      color: var(--navItemColorHover);
    }
  }
  .top {
    display: flex;
    margin-bottom: 20px;align-items: center;
    justify-content: space-between;
    .btn {
      display: none;
      padding: 0 10px 0 0;
    }
  }
  :global(.max550) {
    .wrap {
      display: block;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, .2);
      z-index: 10;
    }
    nav {
      display: block;
      background: #fff;
      width: 60%;
      max-width: 250px;
      height: 100vh;
      padding: 20px;
      box-shadow: 0 0 12px 0 rgba(0, 0, 0, .3);
      span {
        display: block;
        margin-right: 0;
        padding: 10px 0;
      }
    }
    .btn {
      display: flex;
    }
  }
</style>