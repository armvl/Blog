import {writable} from 'svelte/store'

const
  max900 = set('(max-width: 899px)', writable(false)),
  max550 = set('(max-width: 549px)', writable(false)),
  max400 = set('(max-width: 399px)', writable(false)),
  max350 = set('(max-width: 349px)', writable(false)),
  portrait = set('screen and (orientation:landscape)', writable(false))

function set (value, store) {
  let
    mediaQuery = window.matchMedia(value),
    change = (e) => store.set(e.matches)

  mediaQuery.addEventListener('change', change)
  change(mediaQuery)

  return store
}

export {
  max900,
  max550,
  max400,
  max350,
  portrait
}