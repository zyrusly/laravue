<template>
  <div class="login-container">
    <h2>Login</h2>
    <form @submit.prevent="login">
      <p v-if="loading" class="loading"></p>
      <template v-else>
        <input type="email" placeholder="Email" v-model="user.email" required /><br />
        <input type="password" placeholder="Password" v-model="user.password" required /><br />
        <button type="submit" :disabled="loading">Login</button>
        <p v-if="error" style="color: red" class="error">{{ error }}</p>
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
        if (!authStore.error) {
          router.push({ name: 'home' })
          window.location.reload()
        } else {
          error.value = authStore.error
        }
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

<style scoped>
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
  /* outline: 1px solid red */
}


.login-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 80vh;
}

.login-container > form {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 50px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  border-radius: 5px;
  background-color: rgb(228, 228, 228);
  min-width: 300px;
  min-height: 300px;
}
.login-container > h2 {
  margin-bottom: 5px;
  color: #292929;
  font-family: Arial, Helvetica, sans-serif;
}

.login-container > form > input {
  width: 100%;
  padding: 10px;
  margin: 10px 0;
  border: 1px solid #ccc;
  border-radius: 5px;
}
.login-container > form > button {
  width: 100%;
  padding: 10px;
  background-color: #292929;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

.loading {
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  position: relative;
  }

.loading::after {
    content: '';
    width: 16px;
    height: 16px;
    border: 2px solid transparent;
    border-top: 2px solid #292929;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-left: 10px;
  }

@keyframes spin {
    from {
      transform: rotate(0deg);
    }
    to {
      transform: rotate(360deg);
    }
  }

.error {
  color: red;
  font-size: 14px;
  margin-top: 10px;
  text-align: center;
}


</style>