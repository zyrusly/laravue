import { defineStore } from 'pinia'
import { ref, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import repository from '@/api/repository' // adjust path if needed

interface User {
  id: number
  name: string
  email: string
}
export const useAuthStore = defineStore('auth', () => {
  const router = useRouter()

  const storedUser = sessionStorage.getItem('user')
  const user = ref<User | null>(storedUser ? JSON.parse(storedUser) : null)
  const loading = ref(false)
  const error = ref<string | null>(null)

  const authenticated = computed(() => user.value !== null)

  async function login(credentials: any) {
    loading.value = true
    error.value = null
    try {
      await repository.createSession()
      const { data } = await repository.login(credentials)
      if (data.error) {
        error.value = data.error
      } else {
        setUser(data)
        sessionStorage.setItem('user', JSON.stringify(data))
      }
    } catch (err: any) {
      console.error(err)
      error.value = err?.message || 'An error occurred'
    } finally {
      loading.value = false
    }
  }

  async function logout() {
    loading.value = true
    error.value = null
    try {
      await repository.logout()
      setUser(null)
      sessionStorage.removeItem('user')
      window.location.reload() // reload the page to update the UI
      router.push({ name: 'Login' }) // redirect to login page
    } catch (err: any) {
      error.value = err?.message || 'An error occurred'
    } finally {
      loading.value = false
    }
  }

  function setUser(u: User | null) {
    user.value = u
  }


  return {
    user,
    loading,
    error,
    authenticated,
    login,
    logout,
    setUser,
  }
})
