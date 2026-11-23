import { createApp } from 'vue'
import { Quasar, Notify, Dialog } from 'quasar'
import '@quasar/extras/material-icons/material-icons.css'
import 'quasar/src/css/index.sass'
import './css/app.scss'
import App from './App.vue'

const app = createApp(App)
app.use(Quasar, {
  plugins: { Notify, Dialog },
  config: {
    brand: {
      primary: '#5367E8',
      secondary: '#16A6A1',
      accent: '#F4A340'
    }
  }
})
app.mount('#q-app')
