<script>
  import {page} from '$app/stores'
</script>

<div class="title">Ошибка</div>
<div class="status">Status: {$page.status}</div>
<div class="msg">Msg: {$page.error.message}</div>

<style lang="less">
  .title {
    font-size: 16px;
    margin-bottom: 20px;
  }
  .status {
    margin-bottom: 10px;
  }
</style>