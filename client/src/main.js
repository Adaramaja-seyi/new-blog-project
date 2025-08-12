import { createApp } from 'vue'
import { createRouter, createWebHistory } from 'vue-router'
import App from './App.vue'

// Import Bootstrap CSS and JS
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap/dist/js/bootstrap.bundle.min.js'

// Import global SCSS
import './assets/scss/main.scss'

// Import router configuration
import router from './router'

// Create and mount the app
const app = createApp(App)
app.use(router)
app.mount('#app')
