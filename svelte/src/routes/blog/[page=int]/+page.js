
/** @type {import('./$types').PageLoad} */
export function load ({params, url}) {
  const
    tagsParam = url.searchParams.get('tags')

  return {
    page: params.page ? +params.page : 1,
    tags: tagsParam ? tagsParam.split(',') : []
  }
}