<script>
  import {toastsStore} from '$lib/toast/store'
  import Toast from '$lib/toast/Toast.svelte'
</script>

<div class="toaster">
  {#each $toastsStore as toast (toast.id)}
    <Toast bind:toast />
  {/each}
</div>

<style lang="less">
  .toaster {
    display: flex;
    align-items: flex-end;
    flex-direction: column;
    position: fixed;
    left: 0;
    right: 0;
    top: auto;
    bottom: 0;
    overflow: hidden;
    padding: 0 20px 20px;
    pointer-events: none;
    z-index: 350;
  }
</style>