<script>
  import Editor from '@tinymce/tinymce-svelte'
  import Loader from '$lib/blog/Loader.svelte'

  export let
    value,
    editor

  let
    // apiKey = '',
    scriptSrc = '/js/tinymce/tinymce.min.js',
    loadHide = false,
    conf = {
      setup: (tinyEditor) => {
        loadHide = true
        tinyEditor.on('init', (e) => editor = tinyEditor);
      },
      height: 700,
      min_height: 200,
      content_css: '/css/formatted.css',
      body_class: 'formatted',
      language: 'ru',
      menubar: false,
      plugins: ['codesample','lists', 'link','image','autolink','anchor','media','table'],
      toolbar: 'undo redo blocks removeformat bold italic forecolor backcolor alignleft aligncenter alignright alignjustify codesample bullist numlist link image table',
    }

</script>

{#if ! loadHide}
  <Loader block />
{/if}

<!--<Editor {apiKey} {scriptSrc} {conf} bind:value />-->
<Editor {scriptSrc} {conf} bind:value />