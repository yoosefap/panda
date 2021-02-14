import Dashboard from '../vue/pages/dashboard.vue';
import Users from '../vue/pages/users.vue';
import Groups from '../vue/pages/groups.vue';
import Products from '../vue/pages/products/main.vue';
import ProductsList from '../vue/pages/products/list.vue';
import ProductsForm from '../vue/pages/products/form.vue';

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
    {
        path: PINOOX.URL.BASE + '/users',
        name: 'users',
        component: Users,
    },
    {
        path: PINOOX.URL.BASE + '/groups',
        name: 'groups',
        component: Groups,
    },
    {
        path: PINOOX.URL.BASE + '/products',
        component: Products,
        children: [
            {
                path: '',
                name: 'products-list',
                component: ProductsList,
            },
            {
                path: 'edit',
                name: 'products-form',
                component: ProductsForm,
                props: true,
            },
        ],
    },
];