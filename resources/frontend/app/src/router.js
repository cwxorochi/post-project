import Vue from 'vue';
import Router from 'vue-router';
Vue.use(Router);

// Auth
// -------------------------------------
import LayoutApp from './views/layouts/LayoutApp'
import DashboardIndex from './views/dashboard/Index'

import NotFoundPage from './views/404'
const routes = [
  {
    path: '/',
    component: LayoutApp,
    meta: { redirectIfAuthenticated: true },
    children: [
      {
        path: '/',
        name: 'dashboard.index',
        component: DashboardIndex
      },
    ]
  },
  { path: '*', component: NotFoundPage }
]

const router = new Router({
  routes,
  mode: 'history',
  base: '/',
  linkExactActiveClass: 'active',
});

router.beforeEach( async (to, from, next) => {
  return next()
})

export default router
