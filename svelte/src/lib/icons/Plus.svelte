<script>
  export let size = 16
</script>

{#if size === 16}
  <svg width="16" height="16" viewBox="0 0 16 16">
    <path fill-rule="evenodd" clip-rule="evenodd" d="M13,7H9V3c0-0.55-0.45-1-1-1S7,2.45,7,3v4H3C2.45,7,2,7.45,2,8
			c0,0.55,0.45,1,1,1h4v4c0,0.55,0.45,1,1,1s1-0.45,1-1V9h4c0.55,0,1-0.45,1-1C14,7.45,13.55,7,13,7z"/>
  </svg>
{:else if size === 20}
  <svg width="20" height="20" viewBox="0 0 20 20">
    <path fill-rule="evenodd" clip-rule="evenodd" d="M16,9h-5V4c0-0.55-0.45-1-1-1S9,3.45,9,4v5H4c-0.55,0-1,0.45-1,1
			c0,0.55,0.45,1,1,1h5v5c0,0.55,0.45,1,1,1s1-0.45,1-1v-5h5c0.55,0,1-0.45,1-1C17,9.45,16.55,9,16,9z"/>
  </svg>
{/if}