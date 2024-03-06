<script>
  import {goto} from '$app/navigation'

  export let
    url = '',
    text,
    state = '',
    size = '',
    click = () => {},
    loading = false,
    left = false,
    right = false,
    disabled = false

  let
    className = [state, size].join(' ').trim()

  async function onClick(e) {
    if (url)
      return await goto(url)

    click(e)
  }
</script>

<button class={className}
        on:click={onClick}
        class:process={loading}
        class:disabled
        class:left
        class:right>

  <slot />

  {#if text}
    {text}
  {/if}
</button>

<style lang="less">
  @import '$lib/css/vars.less';

  :root {
    --btnColor: @btnColor;
    --btnColorDisabled: @btnColorDisabled;
    --btnBackground: @btnBackground;
    --btnBackgroundHover: darken(@btnBackground, 10%);
    --btnBackgroundActive: darken(@btnBackground, 18%);
    --btnBackgroundDisabled: lighten(@btnBackground, 25%);

    --btnActionsColor: @btnActionsColor;
    --btnActionsColorDisabled: @btnActionsColorDisabled;

    --btnPrimaryBackground: @btnPrimaryBackground;
    --btnPrimaryBackgroundHover: darken(@btnPrimaryBackground, 12%);
    --btnPrimaryBackgroundActive: darken(@btnPrimaryBackground, 18%);
    --btnPrimaryBackgroundDisabled: lighten(@btnPrimaryBackground, 25%);

    --btnDangerBackground: @btnDangerBackground;
    --btnDangerBackgroundHover: darken(@btnDangerBackground, 9%);
    --btnDangerBackgroundActive: darken(@btnDangerBackground, 16%);
    --btnDangerBackgroundDisabled: lighten(@btnDangerBackground, 26%);
  }

  button {
    padding: 8px 12px;
    cursor: pointer;
    font-size: 16px;
    display: inline-flex;
    align-items: center;
    white-space: nowrap;
    position: relative;
    border-radius: 10px;
    //transition: background .15s ease;
    border: 0;
    min-width: 30px;
    text-decoration: none;
    background-color: var(--btnBackground);
    color: var(--btnColor);
    &.large {
      padding: 11px 15px;
      font-size: 16px;
    }
    &:hover {
      background-color: var(--btnBackgroundHover);
    }
    &:active {
      background-color: var(--btnBackgroundActive);
    }
    &:disabled, &.disabled {
      background-color: var(--btnBackgroundDisabled);
      box-shadow: none;
      color: var(--btnColorDisabled);
      cursor: not-allowed;
      outline: none;
      border-color: transparent;
    }
    &.primary, &.success, &.danger {
      color: var(--btnActionsColor);
      fill: var(--btnActionsColor);
    }
    &.primary {
      background-color: var(--btnPrimaryBackground);
      &:hover {
        background-color: var(--btnPrimaryBackgroundHover);
      }
      &:active {
        background-color: var(--btnPrimaryBackgroundActive);
      }
      &:disabled, &.disabled {
        background-color: var(--btnPrimaryBackgroundDisabled);
        color: var(--btnActionsColorDisabled);
      }
    }
    &.danger {
      background-color: var(--btnDangerBackground);
      &:hover {
        background-color: var(--btnDangerBackgroundHover);
      }
      &:active {
        background-color: var(--btnDangerBackgroundActive);
      }
      &:disabled, &.disabled {
        background-color: var(--btnDangerBackgroundDisabled);
        color: var(--btnActionsColorDisabled);
      }
    }
  }
  .left {
    margin-left: 20px;
  }
  .right {
    margin-right: 20px;
  }
  :global(.max400) {
    button {
      font-size: 14px;
    }
  }
</style>