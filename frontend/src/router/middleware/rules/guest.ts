import { useAuthStore } from '@/store/index'
import type { NavigationGuardNext, RouteLocationNormalized } from 'vue-router'
export default (
  to: RouteLocationNormalized,
  from: RouteLocationNormalized,
  next: NavigationGuardNext,
) => {
  const authStore = useAuthStore()
  if (authStore.authenticated) {
    next({ name: 'home' })
  } else {
    next()
  }
}
