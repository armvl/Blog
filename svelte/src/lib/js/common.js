import {toaster} from '$lib/toast/store'
import {goto} from '$app/navigation'

function plural (n) {
  return ((n==1) || (n%10==1 && n%100!=11)) ? 0 : ((n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20)) ? 1 : 2);
}

async function request (link, payload = {}, abortController = null) {
  let
    response = null,
    json = null

  try {
    payload.token = localStorage.getItem('token')

    const response = await fetch('/api/' + link, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      credentials: 'same-origin',
      body: JSON.stringify(payload),
      signal: abortController ? abortController.signal : null
    })

    if ( ! response.ok) // если HTTP-статус не в диапазоне 200-299
      throw new Error('Request HTTP error')

    json = await response.json()

    if (typeof json.success === 'undefined')
      throw new Error('Response success error')

    return json
  }
  catch (err) {
    console.log('{request error}', err, response, json)
    return { status: false, msg: err.message }
  }
}

function warning (msg) {
  toaster.push(msg, 'warning', 10000)
}

function success (msg) {
  toaster.push(msg, 'success')
}

function pluralCommon (words, n, ucfirst = false) {
  const txt = words[plural(n)]
  return ucfirst ? txt.charAt(0).toUpperCase() + txt.slice(1) : txt
}

function pluralPosts (n, ucfirst = false) {
  return pluralCommon(['пост', 'поста', 'постов'], n, ucfirst)
}

async function deletePost (uid) {
  const json = await request('deletePost', {uid})

  if ( ! json.success)
    return warning(json.msg)

  await goto('/blog')

  success(`Пост удален!`)
}

function prepareDataPost (title, tags, editor) {
  if ( ! editor || ! editor.getContent)
    return 'Редактор загружается.'

  const
    html = editor.getContent({format: 'html'}).split('<p>more</p>'),
    entry = html.length > 1 ? html[0] : '',
    content = html.length > 1 ? html.slice(1).join('') : html[0]

  if ( ! title)
    return 'Напишите название поста.'

  if ( ! entry)
    return 'Напишите краткое вступление для поста. (Вставьте разделитель "more")'

  if ( ! content)
    return 'Напишите содержимое поста.'

  return {
    title,
    entry,
    tags: tags.filter((tag) => tag.selected).map((tag) => tag.id),
    content
  }
}

function dateFormat (timestamp) {
  const
    dt = new Date(timestamp * 1000),
    month = ['янв', 'фев', 'мар', 'апр', 'май', 'июн', 'июл', 'авг', 'сен', 'окт', 'ноя', 'дек']

  return dt.getDate() + ' ' + month[dt.getMonth()] + '. ' + dt.getFullYear()
}

export {
  request,
  warning,
  success,
  plural,
  pluralPosts,
  prepareDataPost,
  deletePost,
  dateFormat
}