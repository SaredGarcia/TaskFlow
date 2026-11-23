import { configure } from 'quasar/wrappers'

export default configure(() => ({
  boot: ['axios'],
  css: ['app.scss'],
  extras: ['material-icons', 'roboto-font'],
  build: {
    target: { browser: ['es2022', 'firefox115', 'chrome115', 'safari14'] },
    vueRouterMode: 'history'
  },
  devServer: {
    open: true,
    port: 9000
  },
  framework: {
    config: { brand: { primary: '#5367E8', secondary: '#16A6A1', accent: '#F4A340' } },
    plugins: ['Dialog', 'Notify']
  }
}))
