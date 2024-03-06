
/** @type {import('@sveltejs/kit').ParamMatcher} */
export function match (param) {
  return /^[0-9a-f-]{36}$/i.test(param);
}