import {handler} from './build/handler.js'
import express from 'express'

const app = express()
const port = process.env.PORT || 3000
const host = '127.0.0.1'

app.get('/health', (req, res) => {
  console.log((new Date).toUTCString() + ' healthcheck')
  res.end('ok');
})

app.use(handler)

app.listen(port, () => {
  console.log(`listening on port ${host}:${port}`);
})