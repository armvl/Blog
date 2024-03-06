<script>
  import {toastsStore} from '$lib/toast/store'
  import {onMount}  from 'svelte'
  import {fly} from 'svelte/transition'
  import IconCross from '$lib/icons/Cross.svelte'
  import IconTick from '$lib/icons/Tick.svelte'
  import IconWarningSign from '$lib/icons/WarningSign.svelte'
  import IconHand from '$lib/icons/Hand.svelte'

  export let
    toast

  let
    durationIn = 250,
    durationOut = 150,
    timeout

  function removeToast () {
    toastsStore.update((toasts) => toasts.filter((t) => t !== toast))
  }

  function closeToast () {
    clearTimeout(timeout)
    removeToast()
  }

  onMount(async () => {
    if (toast.duration > Math.max(durationIn, durationOut))
      timeout = setTimeout(removeToast, toast.duration)
  })
</script>

<div class="toast {toast.state}" transition:fly={{ x: 200, duration: 300 }}>
  {#if toast.state === 'success'}
    <span class="icon"><IconTick /></span>
  {:else if toast.state === 'danger'}
    <span class="icon"><IconWarningSign /></span>
  {:else if toast.state === 'warning'}
    <span class="icon"><IconHand /></span>
  {/if}

  <span class="message">{@html toast.message}</span>

  <span class="close-wrap">
    <span class="close" on:click={closeToast}>
      <IconCross />
    </span>
  </span>
</div>

<style lang="less">
  @import '$lib/css/vars.less';

  :root {
    --toastSuccessColor: @toastSuccessColor;
    --toastSuccessColorIcon: @toastSuccessColorIcon;
    --toastSuccessBackground: @toastSuccessBackground;

    --toastDangerColor: @toastDangerColor;
    --toastDangerColorIcon: @toastDangerColorIcon;
    --toastDangerBackground: @toastDangerBackground;

    --toastWarningColor: @toastWarningColor;
    --toastWarningColorIcon: @toastWarningColorIcon;
    --toastWarningBackground: @toastWarningBackground;
  }

  .toast {
    display: flex;
    align-items: flex-start;
    background-color: #ffffff;
    border-radius: 5px;
    box-shadow: 0 2px 4px rgba(17, 20, 24, .2),
                0 8px 16px rgba(17, 20, 24, .2);
    margin: 20px 0 0;
    max-width: 500px;
    min-width: 300px;
    pointer-events: all;
    position: relative;
    &.success {
      background: var(--toastSuccessBackground);
      color: var(--toastSuccessColor);
      .close,
      .icon {
        fill: var(--toastSuccessColorIcon);
      }
    }
    &.danger {
      background: var(--toastDangerBackground);
      color: var(--toastDangerColor);
      .close,
      .icon {
        fill: var(--toastDangerColorIcon);
      }
    }
    &.warning {
      background: var(--toastWarningBackground);
      color: var(--toastWarningColor);
      .close,
      .icon {
        fill: var(--toastWarningColorIcon);
      }
    }
    :global(b) {
      font-weight: 700;
    }
    :global(a) {
      font-weight: 500;
      text-decoration: underline;
      color: #ffffff;
      &:hover {
        text-decoration: none;
      }
    }
  }
  .icon {
    display: inline-flex;
    flex: 0 0 auto;
    vertical-align: text-bottom;
    margin: 11px 0 11px 11px;
  }
  .message {
    flex: 1 1 auto;
    padding: 11px 11px 11px 13px;
    word-break: break-word;
    //line-height: 1.2;
  }
  .close-wrap {
    flex: 0 0 auto;
    padding: 5px 5px 5px 0;
  }
  .close {
    align-items: center;
    border-radius: 3px;
    cursor: pointer;
    display: inline-flex;
    justify-content: center;
    min-height: 28px;
    min-width: 28px;
    &:hover {
      background: rgba(0,0,0,.15);
    }
  }
</style>