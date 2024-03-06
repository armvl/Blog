<script>

  export let
    block = false,
    white = false,
    inline = false

</script>

{#if block}
  <div class="block">
    <span class="loader" class:white class:inline></span>
  </div>
{:else}
  <span class="loader" class:white class:inline></span>
{/if}

<style lang="less">
  .block {
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .loader {
    width: 48px;
    height: 48px;
    border: 5px solid #7c7c7c;
    border-bottom-color: transparent;
    border-radius: 50%;
    display: inline-block;
    box-sizing: border-box;
    animation: rotation 1s linear infinite;
    &.white {
      border-color: #fff;
      border-bottom-color: transparent;
    }
    &.inline {
      border-width: 2px;
      width: 13px;
      height: 13px;
    }
  }

  @keyframes rotation {
    0% {
      transform: rotate(0deg);
    }
    100% {
      transform: rotate(360deg);
    }
  }
</style>