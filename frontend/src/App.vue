<template>
  <div id="app">
    <div id="nav" v-once>
      <div class="nav-container">
        <!-- <router-link v-if="!authenticated" :to="{ name: 'login' }">Login</router-link> -->
        <router-link v-if="authenticated" :to="{ name: 'home' }" class="home">Home</router-link>
        <div v-if="authenticated">
          <button  @click="showModal('user')" class="nav-btn">Create User </button> |
          <button  @click="showModal('role')" class="nav-btn">Create Role </button> |
          <button @click="logout" class="nav-btn">Logout</button>
        </div>
      </div>
      <br />
      <p v-if="loading">loading...</p>
      <p v-if="error" style="color: red">{{ error }}</p>
    </div>
    <router-view />
    <div style="display: flex; justify-content: space-evenly; ">
      <div v-show="isUserModalOpen" class="modal">
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

      <div v-show="isRoleModalOpen" class="modal">
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
import { defineComponent, ref, onMounted, reactive } from 'vue'
import { useAuthStore } from '@/store/index'
import repository from '@/api/repository'

export default defineComponent({
  name: 'App',
  setup() {
    const authStore = useAuthStore()
    const isUserModalOpen = ref(false)
    const isRoleModalOpen = ref(false)
    const userForm = reactive({
      id: null,
      email: '',
      name: '',
      password: '',
      passwordConfirmation: '',
      role_id: null,
    })
    const roleForm = reactive({
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
        const { id, email, name, password, passwordConfirmation, role_id } = userForm

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
        const { id, name, description } = roleForm
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


<style scoped>
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
  /* outline: 1px solid red */
}

#nav {
  display: flex;
  justify-content: center;
  width: 100%;
  padding: 15px;
  border-bottom: 1px solid #dee2e6;
}

.nav-container {
  display: flex;
  justify-content: space-between;
  width: 80%;
  align-items: center;
}

.nav-container > div {
  display: flex;
  justify-content: center;
  align-items: center;
}

.home {
  text-decoration: none;
  color: #1b1b1b;
  font-size: 2rem;
}

.nav-btn {
  font-family: Arial, Helvetica, sans-serif;
  background-color: transparent;
  color: #1b1b1b;
  border: none;
  font-size: 1.25rem;
  cursor: pointer;
  margin: 0 10px;
}

.nav-btn:hover {
  font-size: 1.3rem;
  color: #0f0f0f;
  transition: all 0.1s ease-in-out;
}

.modal {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background-color: white;
  border-radius: 5px;
  box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
  min-width: 450px;
}

.modal > form {
  padding: 20px;
  width: 100%;
}
.modal > form > input {
  width: 100%;
  padding: 10px;
  margin: 10px 0;
  border: 1px solid #ccc;
  border-radius: 5px;
}

.modal >form > select {
  width: 100%;
  padding: 10px;
  margin: 10px 0;
  border: 1px solid #ccc;
  border-radius: 5px;
}

.modal > form > button {
  width: 100%;
  padding: 10px;
  background-color: #292929;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}
.modal > form > button:hover {
  background-color: #1b1b1b;
  transition: all 0.1s ease-in-out;
}
.modal > form > label {
  font-family: Arial, Helvetica, sans-serif;
  font-size: .9rem;
  color: #292929;
}
.close {
  color: #aaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
}
.close:hover,
.close:focus {
  color: black;
  text-decoration: none;
  cursor: pointer;
}
</style>