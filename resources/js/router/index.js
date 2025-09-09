import { createRouter, createWebHistory } from "vue-router";


import notFound from '../pages/notFound.vue'
import login from '../pages/login.vue'
import home from '../pages/user/dashboard.vue'
import pr_list from '../pages/user/pr_list.vue'
import print_pr from '../pages/printables/print_pr.vue'
import item_list from '../pages/user/masterfile/item_list.vue'
import item_add from '../pages/user/masterfile/item_add.vue'


const routes = [
    {
        path:'/',
        component: login,
    },
    {
        path:'/create_pr',
        component: home,
    },
    {
        path:'/print_pr',
        component: print_pr,
    },
    {
        path:'/pr_list',
        component: pr_list,
    },
    {
        path:'/masterfile/items',
        component: item_list,
    },
    {
        path:'/masterfile/items/add',
        component: item_add,
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
