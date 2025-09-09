import './bootstrap';
import '../css/app.css';

import $ from 'jquery';
window.$ = window.jQuery = $;

import 'datatables.net'; // core only, no CSS


import { createApp } from 'vue';
import app from './pages/app.vue';
import router from './router/index.js';

createApp(app).use(router).mount("#app");