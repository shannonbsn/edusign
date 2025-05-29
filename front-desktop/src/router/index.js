import { createRouter, createWebHistory } from 'vue-router';
import AuthLayout from '../views/AuthLayout.vue';
import Login from '../components/Auth/Login.vue';
import Register from '../components/Auth/Register.vue';
import Dashboard from '../views/Dashboard.vue';
import Unauthorized from '../views/Unauthorized.vue';

const routes = [
    {
        path: '/',
        redirect: '/auth/register'
    },
    {
        path: '/auth',
        component: AuthLayout,
        children: [
            { path: 'login', component: Login },
            { path: 'register', component: Register },
        ]
    },
    { path: '/dashboard', component: Dashboard, meta: { requiresIntervenant: true } },
    { path: '/unauthorized', component: Unauthorized },
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

router.beforeEach((to, from, next) => {
    const userRole = localStorage.getItem('userRole');

    if (to.meta.requiresIntervenant && userRole !== 'intervenant') {
        next('/unauthorized');
    } else {
        next();
    }
});

export default router;
