import './bootstrap';
import '../css/app.css';
import "@fontsource/poppins/400.css";
import "@fontsource/poppins/500.css";
import "@fontsource/poppins/600.css";
import "@fontsource/poppins/700.css";

import $ from 'jquery';
window.$ = window.jQuery = $;

import 'datatables.net'; // core only, no CSS

// import Vue3EasyDataTable from 'vue3-easy-data-table';
// import 'vue3-easy-data-table/dist/style.css';

import { createApp } from 'vue';
import app from './pages/app.vue';
import router from './router/index.js';

createApp(app).use(router).mount("#app");


