// stores/auth.ts
import { defineStore } from 'pinia'
import repository from '@/api/repository'

interface User {
  id: number
  name: string
  email: string
}

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: sessionStorage.getItem('user')
      ? JSON.parse(sessionStorage.getItem('user') as string)
      : (null as User | null),
    loading: false as boolean,
    error: false as string | boolean,
  }),

  getters: {
    authenticated: (state) => state.user !== null,
  },

  actions: {
    async login(user: any) {
      try {
        this.loading = true
        await repository.createSession()
        const { data } = await repository.login(user)
        this.setUser(data)
        console.log(data)
        sessionStorage.setItem('user', JSON.stringify(data))
        window.location.reload()
      } catch (error) {
        console.log(error)
        this.error = (error as any).message || 'An error occurred'
      } finally {
        this.loading = false
      }
    },

    async logout() {
      try {
        this.loading = true
        await repository.logout()
        this.setUser(null)
        sessionStorage.removeItem('user')
        window.location.reload()
      } catch (error) {
        this.error = (error as any).message || 'An error occurred'
      } finally {
        this.loading = false
      }
    },

    setUser(user: User | null) {
      this.user = user
    },
  },
})
