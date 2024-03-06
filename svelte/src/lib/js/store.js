import {writable} from 'svelte/store'

const
  pageMetaStore = writable({title: ''}),
  loaderPostsStore = writable(false)

export {
  pageMetaStore,
  loaderPostsStore
}