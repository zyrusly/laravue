import { createRouter, createWebHistory } from 'vue-router'
import type { RouteRecordRaw } from 'vue-router'
import routes from './routes'

const router = createRouter({
  history: createWebHistory(),
  routes: routes as RouteRecordRaw[], 
  scrollBehavior: () => ({ top: 0 }),
})

export default router
