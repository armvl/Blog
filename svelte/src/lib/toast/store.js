import {writable} from 'svelte/store'

const
  toastsStore = writable([]),
  toaster = {}

toaster.push = (message, state = 'primary', duration = 5000, vars) => {
  const toast = {
    id: 'id' + Math.random(),
    message,
    state,
    duration,
    ...vars,
  }

  toastsStore.update((stack) => [...stack, toast])
}

export {
  toastsStore,
  toaster
}