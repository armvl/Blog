<script>

  export let
    value,
    placeholder

</script>

<div class="title">
  <input {placeholder} bind:value />
</div>

<style lang="less">
  @import '$lib/css/vars.less';

  :root {
    --titleInputBorderColor: @titleInputBorderColor;
    --titleInputBorderColorFocus: darken(@titleInputBorderColor, 5%);
  }

  @borderColor: #eee;

  .title {
    line-height: 1.68em;
    padding-top: 20px;
    padding-bottom: 18px;
    input {
      width: 100%;
      font-weight: 700;
      font-size: 26px;
      background: #fff;
      border: 2px solid var(--titleInputBorderColor);
      border-radius: 10px;
      padding: 6px 12px;
      &:focus {
        border-color: var(--titleInputBorderColorFocus);
      }
    }
  }
</style>