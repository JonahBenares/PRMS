import { createRouter, createWebHistory } from "vue-router";


import notFound from '../pages/notFound.vue'
import login from '../pages/login.vue'
import home from '../pages/user/dashboard.vue'
import dashboard from '../pages/admin/dashboard.vue'
import print_pr from '../pages/printables/print_pr.vue'


const routes = [
    {
        path:'/',
        component: login,
    },
    {
        path:'/user/dashboard',
        component: home,
    },
    {
        path:'/admin/dashboard',
        component: dashboard,
    },
    {
        path:'/user/print_pr',
        component: print_pr,
    },
    {
        path:'/:pathMatch(.*)*',
        name:'notFound',
        component: notFound,
        meta:{
            requiresAuth:false
        }
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router
