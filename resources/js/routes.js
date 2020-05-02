import Vue from 'vue';
import VueRouter from 'vue-router';

import Login from './components/admin/views/auth/Login';
import Register from './components/admin/views/auth/Register';
import Panel from './components/admin/views/Panel';
import Category from './components/admin/views/Category';

Vue.use(VueRouter);

 const routes = [
    {
        path: '/panel',
        name: 'panel',
        component: Panel,
        meta:{
            requireAuth:true
        },
        children:[
            {
                path: '/category',
                name: 'category',
                component: Category
            },
        ]
    },
    {
        path: '*',
        component: Panel,
        meta:{
            requireAuth:true
        },
    },
    {
        path: '/login',
        name: 'login',
        component: Login
    },
    {
        path: '/register',
        name: 'register',
        component: Register
    },
    

];


const router = new VueRouter({
    routes, // short for `routes: routes`,
    mode: 'history'
});


export default router;
