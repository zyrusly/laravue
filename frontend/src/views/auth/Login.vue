<template>
  <div>
    <h3>Login</h3>
    <p v-if="loading">loading...</p>
    <p v-if="error" style="color: red">{{ error }}</p>
    <input type="text" placeholder="Email" v-model="user.email" /><br />
    <input type="password" placeholder="Password" v-model="user.password" /><br />
    <button @click="login">Login</button>
  </div>
</template>

<script lang="ts">
import { defineComponent, reactive, ref } from 'vue'
import { useAuthStore } from '@/store/index'
import { useRouter } from 'vue-router'

export default defineComponent({
  name: 'Login',
  setup() {
    const authStore = useAuthStore()
    const router = useRouter()

    const user = reactive({
      email: '',
      password: '',
    })

    const loading = ref(false)
    const error = ref<string | null>(null)

    const login = async () => {
      error.value = null

      try {
        loading.value = true
        await authStore.login(user)
        await router.push({ name: 'home' })
      } catch (err) {
        error.value = (err as any).message || 'An error occurred'
      } finally {
        loading.value = false
      }
    }

    return {
      user,
      loading,
      error,
      login,
    }
  },
})
</script>
