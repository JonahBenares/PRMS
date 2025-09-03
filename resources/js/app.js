import './bootstrap';
import '../css/app.css'

import { createApp } from 'vue'
import app from './pages/app.vue'
import router from './router/index.js'


// import { TailwindPagination } from 'laravel-vue-pagination';
createApp(app).use(router).mount("#app")
