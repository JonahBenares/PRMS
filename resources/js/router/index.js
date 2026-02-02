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
import location_item from '../pages/user/masterfile/location_item.vue'
import location_pr from '../pages/user/masterfile/location_pr.vue'
import warehouse_list from '../pages/user/masterfile/warehouse.vue'
import rack_list from '../pages/user/masterfile/rack.vue'
import group_list from '../pages/user/masterfile/group.vue'
import category_list from '../pages/user/masterfile/category.vue'
import qualifier_list from '../pages/user/masterfile/qualifier.vue'
import company_list from '../pages/user/masterfile/company.vue'
import signatories from '../pages/user/masterfile/signatories.vue'


const routes = [
    {
        path:'/',
        name: 'login',
        component: login,
    },
    {
        path:'/create_pr',
        name: 'home',
        component: home,
        meta:{
            requiresAuth:false
        }
    },
    {
        path:'/print_pr/:id',
        component: print_pr,
        props: true
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
        path:'/masterfile/items/edit/:id',
        component: item_edit,
        props: true
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
        path:'/masterfile/location_item',
        component: location_item,
    },

    {
        path:'/masterfile/location_pr',
        component: location_pr,
    },
    {
        path:'/masterfile/warehouse',
        component: warehouse_list,
    },
    {
        path:'/masterfile/rack',
        component: rack_list,
    },
    {
        path:'/masterfile/group',
        component: group_list,
    },
    {
        path:'/masterfile/qualifier',
        component: qualifier_list,
    },
    {
        path:'/masterfile/category',
        component: category_list,
    },
    {
        path:'/masterfile/company',
        component: company_list,
    },
    {
        path:'/masterfile/signatories',
        component: signatories,
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

router.beforeEach((to,from) => {
    if(to.meta.requiresAuth && !localStorage.getItem('token') ){
        return { name: 'login'}
    } 
})

export default router
