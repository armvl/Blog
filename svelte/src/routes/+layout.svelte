<script>
  import '$lib/css/common.less'

  import {onMount} from 'svelte'
  import {pageMetaStore, loaderPostsStore} from '$lib/js/store'
  import {max350, max400, max550, max900} from '$lib/js/media'
  import Toaster from '$lib/toast/Toaster.svelte'
  import Nav from '$lib/Nav.svelte'

  let
    root

  onMount( () => {
    root = document.documentElement
  })

  $: root && root.classList.toggle('max900', $max900)
  $: root && root.classList.toggle('max550', $max550)
  $: root && root.classList.toggle('max400', $max400)
  $: root && root.classList.toggle('max350', $max350)

</script>

<svelte:head>
  <title>{$pageMetaStore.title}</title>
</svelte:head>

{#if $loaderPostsStore}
  <div class="loader process"></div>
{/if}

<div class="main">
  <Nav />
  <slot />
</div>

<Toaster />

<style lang="less">
  @import '$lib/css/vars.less';

  :root {
    --loaderBackground: @loaderBackground;
    --paddingBody: @paddingBody;
    &.max550 {
      --paddingBody: 0;
    }
  }

  .main {
    margin: 0 auto;
    max-width: 1050px;
    min-width: 320px;
    background: #ffffff;
    padding: 12px 20px 20px;
    border-radius: 10px;
    box-shadow: 0 5px 20px 0 rgba(0, 0, 0, .08);
    position: relative;
  }
  .loader {
    content: '';
    display: block;
    height: 4px;
    background: var(--loaderBackground);
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
  }
  :global(.max550) {
    .main {
      border-radius: 0;
      box-sizing: border-box;
    }
  }
</style>