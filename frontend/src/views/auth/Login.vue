<template>
  <div>
    <h3>Login</h3>
    <form @submit.prevent="login">
      <p v-if="loading">Loading...</p>
      <p v-if="error" style="color: red">{{ error }}</p>
      <template v-else>
        <input type="email" placeholder="Email" v-model="user.email" required /><br />
        <input type="password" placeholder="Password" v-model="user.password" required /><br />
        <button type="submit" :disabled="loading">Login</button>
      </template>
    </form>
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
        if (
          (err as any).response &&
          (err as any).response.data &&
          (err as any).response.data.message
        ) {
          error.value = (err as any).response.data.message
        } else {
          error.value = (err as any).message || 'An error occurred'
        }
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
