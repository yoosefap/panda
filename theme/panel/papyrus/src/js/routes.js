import Dashboard from '../vue/pages/dashboard.vue';

export const routes = [

    {
        path: PINOOX.URL.BASE + '/dashboard',
        name: 'dashboard',
        component: Dashboard,
        meta: {
            showToolbar: true,
        }
    },
    {
        path: PINOOX.URL.BASE + '/error',
        name: 'error',
        component: Error,
    },
];