import { createRouter, createWebHistory } from 'vue-router'
import type { RouteRecordRaw } from 'vue-router'
import routes from './routes'

const router = createRouter({
  history: createWebHistory(),
  routes: routes as RouteRecordRaw[], // TypeScript type assertion
  scrollBehavior: () => ({ top: 0 }),
})

export default router
