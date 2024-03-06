
/** @type {import('@sveltejs/kit').ParamMatcher} */
export function match (param) {
  return /^\d{1,10}$/.test(param);
}