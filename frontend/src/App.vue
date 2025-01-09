<template>
  <div id="app">
    <div id="nav">
      <div style="display: flex; justify-content: space-evenly; width: 100%">
        <router-link v-if="!authenticated" :to="{ name: 'login' }">Login</router-link>
        <router-link v-if="authenticated" :to="{ name: 'home' }">Home</router-link>
        <button v-if="authenticated" @click="logout">logout</button>
        <button v-if="authenticated" @click="showModal('user')">Create User</button>
        <button v-if="authenticated" @click="showModal('role')">Create Role</button>
        <br />
      </div>
      <br />
      <p v-if="loading">loading...</p>
      <p v-if="error" style="color: red">{{ error }}</p>
    </div>
    <router-view />
    <div style="display: flex; justify-content: space-evenly">
      <div v-if="isUserModalOpen" class="modal">
        <br />
        <form @submit.prevent="createUser">
          <label for="email">Email:</label>
          <br />
          <input type="email" v-model="userForm.email" required />
          <br />
          <label for="name">Name:</label>
          <br />
          <input type="text" v-model="userForm.name" required />
          <br />
          <label for="password">Password:</label>
          <br />
          <input type="password" v-model="userForm.password" required />
          <br />
          <label for="passwordConfirmation">Confirm Password:</label>
          <br />
          <input type="password" v-model="userForm.passwordConfirmation" required />
          <br />
          <label for="roleId">Role ID:</label> <br />
          <select v-model="userForm.role_id">
            <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}</option>
          </select>
          <br />
          <br />
          <button type="submit">Create</button> &nbsp; &nbsp; &nbsp;
          <button type="button" @click="hideModal">Cancel</button>
          <br />
          <p v-if="formError" style="color: red">{{ formError }}</p>
          <ul v-if="serverErrors && serverErrors.length">
            <li v-for="(error, index) in serverErrors" :key="index" style="color: red">
              {{ error }}
            </li>
          </ul>
        </form>
      </div>

      <div v-if="isRoleModalOpen" class="modal">
        <br />
        <form @submit.prevent="createRole">
          <label for="name">Name:</label>
          <br />
          <input type="text" v-model="roleForm.name" required />
          <br />
          <label for="description">Description:</label>
          <br />
          <input type="text" v-model="roleForm.description" required />
          <br />
          <br />
          <button type="submit">Create</button> &nbsp; &nbsp; &nbsp;
          <button type="button" @click="hideModalRole">Cancel</button>
          <br />
          <p v-if="formError" style="color: red">{{ formError }}</p>
          <ul v-if="serverErrors && serverErrors.length">
            <li v-for="(error, index) in serverErrors" :key="index" style="color: red">
              {{ error }}
            </li>
          </ul>
        </form>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted } from 'vue'
import { useAuthStore } from '@/store/index'
import repository from '@/api/repository'

export default defineComponent({
  name: 'App',
  setup() {
    const authStore = useAuthStore()
    const isUserModalOpen = ref(false)
    const isRoleModalOpen = ref(false)
    const userForm = ref({
      id: null,
      email: '',
      name: '',
      password: '',
      passwordConfirmation: '',
      role_id: null,
    })
    const roleForm = ref({
      id: null,
      name: '',
      description: '',
    })
    const error = ref<string | null>(null)
    const formError = ref<string | null>(null)
    const serverErrors = ref<string[]>([])
    const roles = ref<{ id: number; name: string }[]>([])

    const showModal = (type: string) => {
      if (type === 'user') {
        isUserModalOpen.value = true
      } else if (type === 'role') {
        isRoleModalOpen.value = true
      }
    }

    const hideModal = () => {
      isUserModalOpen.value = false
      formError.value = null
      serverErrors.value = []
    }

    const hideModalRole = () => {
      isRoleModalOpen.value = false
      formError.value = null
      serverErrors.value = []
    }

    const handleServerError = (err: unknown) => {
      if (
        err &&
        typeof err === 'object' &&
        'response' in err &&
        err.response &&
        typeof err.response === 'object' &&
        'data' in err.response &&
        err.response.data &&
        typeof err.response.data === 'object' &&
        'errors' in err.response.data
      ) {
        serverErrors.value = Object.values(err.response.data.errors).flat() as unknown as string[]
      } else {
        formError.value = (err as any).message || 'An error occurred'
      }
    }

    const fetchRoles = async () => {
      try {
        const { data } = await repository.getRoles()
        roles.value = data
      } catch (err) {
        console.log(err)
        handleServerError(err)
      }
    }

    const createUser = async () => {
      try {
        const { id, email, name, password, passwordConfirmation, role_id } = userForm.value

        if (password !== passwordConfirmation) {
          formError.value = 'Passwords do not match'
          return
        }
        await repository.createUser({ id, email, name, password, role_id })
        hideModal()
        window.location.reload()
      } catch (err) {
        console.log(err)
        handleServerError(err)
      }
    }

    const createRole = async () => {
      try {
        const { id, name, description } = roleForm.value
        await repository.createRole({ id, name, description })
        hideModal()
        window.location.reload()
      } catch (err) {
        console.log(err)
        handleServerError(err)
      }
    }

    const logout = async () => {
      await authStore.logout()
    }

    onMounted(() => {
      fetchRoles()
    })

    return {
      authenticated: authStore.authenticated,
      loading: authStore.loading,
      error: authStore.error,
      logout,
      isUserModalOpen,
      isRoleModalOpen,
      userForm,
      roleForm,
      roles,
      showModal,
      hideModal,
      hideModalRole,
      createUser,
      createRole,
      formError,
      serverErrors,
    }
  },
})
</script>
