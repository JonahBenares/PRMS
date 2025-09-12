import { createRouter, createWebHistory } from "vue-router";


import notFound from '../pages/notFound.vue'
import login from '../pages/login.vue'
import home from '../pages/user/dashboard.vue'
import pr_list from '../pages/user/pr_list.vue'
import print_pr from '../pages/printables/print_pr.vue'
import item_list from '../pages/user/masterfile/item_list.vue'
import item_add from '../pages/user/masterfile/item_add.vue'
import item_edit from '../pages/user/masterfile/item_edit.vue'
import department_list from '../pages/user/masterfile/department.vue'
import purpose_list from '../pages/user/masterfile/purpose.vue'
import enduse_list from '../pages/user/masterfile/enduse.vue'
import employee_list from '../pages/user/masterfile/employee.vue'
import users_list from '../pages/user/masterfile/users.vue'
import location_list from '../pages/user/masterfile/location.vue'
import company_list from '../pages/user/masterfile/company.vue'


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
        path:'/masterfile/items/edit',
        component: item_edit,
    },
    {
        path:'/masterfile/department',
        component: department_list,
    },
    {
        path:'/masterfile/purpose',
        component: purpose_list,
    },
    {
        path:'/masterfile/enduse',
        component: enduse_list,
    },
    {
        path:'/masterfile/employee',
        component: employee_list,
    },
    {
        path:'/masterfile/users',
        component: users_list,
    },
    {
        path:'/masterfile/location',
        component: location_list,
    },
    {
        path:'/masterfile/company',
        component: company_list,
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
