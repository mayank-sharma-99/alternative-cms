import { createRouter, createWebHistory } from 'vue-router';
import PageView from '../views/PageView.vue';
import AdminView from '../views/AdminView.vue';

const routes = [
    {
        path: '/',
        name: 'admin',
        component: AdminView,
    },
    {
        path: '/:pathMatch(.*)*',
        name: 'page',
        component: PageView,
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
