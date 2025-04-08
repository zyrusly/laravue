import type { RouteRecordRaw } from 'vue-router'
import Login from '@/views/auth/Login.vue'
import Index from '@/views/home/Index.vue'

import middleware from './middleware'

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    redirect: '/login',
    beforeEnter: middleware.guest,
  },
  {
    path: '/login',
    name: 'login',
    component: Login,
    beforeEnter: middleware.guest,
  },
  {
    path: '/home',
    name: 'home',
    component: Index,
    beforeEnter: middleware.user,
  },
]

export default routes
