<template>
  <div style="display: flex; justify-content: center">
    <!-- Existing code -->
    <h3>Users:</h3>
    <p v-if="error" style="color: red">{{ error }}</p>
    <div v-if="users" class="users-wrapper" style="width: 50%">
      <div v-for="(user, index) in users" :key="index" class="user">
        <br />
        <h3>{{ user.name }}</h3>
        <p><strong>Email:</strong> {{ user.email }}</p>
        <p><strong>Role:</strong> {{ user.role ? user.role.name : 'No role assigned' }}</p>
        <button @click="editUser(user)">Edit</button>&nbsp;&nbsp;
        <button @click="deleteUser(user.id)">Delete</button> <br />
        <hr />
      </div>
    </div>
    <p v-else>loading...</p>
    <h3>Roles:</h3>
    <p v-if="roleError" style="color: red">{{ roleError }}</p>
    <div v-if="roles" class="roles-wrapper" style="width: 50%">
      <div v-for="(role, index) in roles" :key="index" class="role">
        <br />
        <h3>{{ role.name }}</h3>
        <p>{{ role.description }}</p>
        <button @click="editRole(role)">Edit</button>&nbsp;&nbsp;
        <button @click="deleteRole(role.id)">Delete</button> <br />
        <hr />
      </div>
    </div>
    <p v-else>loading...</p>
    <!-- Modal Component -->
    <div v-if="showModal" class="modal">
      <div class="modal-content">
        <span class="close" @click="closeModal">&times;</span>
        <h3>Edit Role</h3>
        <br />
        <label for="roleName">Name:</label>
        <br />
        <input type="text" id="roleName" v-model="selectedRole.name" />
        <br />
        <br />
        <label for="roleDescription">Description:</label>
        <br />
        <input type="text" id="roleDescription" v-model="selectedRole.description" />
        <br />
        <br />
        <button @click="saveRole">Save</button>&nbsp;&nbsp;
        <button @click="closeModal">Close</button>
        <br />
      </div>
    </div>
    <div v-if="userModalOpen" class="modal">
      <br />
      <div class="modal-content">
        <label for="email">Email:</label>
        <br />
        <input type="email" v-model="selectedUser.email" required />
        <br />
        <label for="name">Name:</label>
        <br />
        <input type="text" v-model="selectedUser.name" required />
        <br />
        <label for="roleId">Role ID:</label> <br />
        <select v-model="selectedUser.role_id">
          <option value="null"><i>No Role Selected</i></option>
          <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}</option>
        </select>
        <br />
        <br />
        <button @click="saveUser">Update</button> &nbsp; &nbsp; &nbsp;
        <button type="button" @click="hideModal">Cancel</button>
        <br />
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue'
import repository from '@/api/repository'

interface User {
  id: number
  name: string
  email: string
  role: null | {
    id: number
    name: string
    description: string
  }
  password: null | string
  role_id: number
}

interface Role {
  id: number
  name: string
  description: string | null
}

export default defineComponent({
  name: 'UsersIndex',
  setup() {
    const users = ref<User[] | null>(null)
    const roles = ref<Role[] | null>(null)
    const error = ref<string | null>(null)
    const roleError = ref<string | null>(null)
    const userError = ref<string | null>(null)
    const showModal = ref(false)
    const userModalOpen = ref(false)
    const selectedRole = ref<Role | null>(null)
    const selectedUser = ref<User | null>(null)

    const fetchUsers = async () => {
      try {
        const { data } = await repository.getUsers()
        users.value = data
      } catch (err) {
        error.value = (err as any).message || 'An error occurred'
      }
    }

    const fetchRoles = async () => {
      try {
        const { data } = await repository.getRoles()
        roles.value = data
      } catch (err) {
        roleError.value = (err as any).message || 'An error occurred'
      }
    }

    const editUser = (user: User) => {
      selectedUser.value = { ...user }
      userModalOpen.value = true
    }

    const deleteUser = async (userId: number) => {
      try {
        await repository.destroyUser(userId)
        users.value = users.value?.filter((user) => user.id !== userId) || null
        console.log('User deleted:', userId)
        window.location.reload()
      } catch (err) {
        error.value = (err as any).message || 'An error occurred while deleting the user'
      }
    }

    const editRole = (role: Role) => {
      selectedRole.value = { ...role }
      showModal.value = true
    }

    const saveRole = async () => {
      if (selectedRole.value) {
        try {
          await repository.updateRole({
            id: selectedRole.value.id,
            name: selectedRole.value.name,
            description: selectedRole.value.description,
          })
          roles.value =
            roles.value?.map((role) =>
              role.id === selectedRole.value?.id ? selectedRole.value : role,
            ) || null
          closeModal()
        } catch (err) {
          roleError.value = (err as any).message || 'An error occurred while saving the role'
        }
      }
    }

    const saveUser = async () => {
      if (selectedUser.value) {
        try {
          await repository.updateUser(
            {
              name: selectedUser.value.name,
              email: selectedUser.value.email,
              role_id: selectedUser.value.role_id,
              password: null,
            },
            selectedUser.value.id,
          )
          users.value =
            users.value?.map((user) =>
              user.id === selectedUser.value?.id ? selectedUser.value : user,
            ) || null
          hideModal()
          window.location.reload()
        } catch (err) {
          userError.value = (err as any).message || 'An error occurred while saving the user'
        }
      }
    }

    const closeModal = () => {
      showModal.value = false
      selectedRole.value = null
    }

    const hideModal = () => {
      userModalOpen.value = false
      selectedUser.value = null
    }

    const deleteRole = async (role_id: number) => {
      try {
        await repository.destroyRole(role_id)
        roles.value = roles.value?.filter((role) => role.id !== role_id) || null
        console.log('Role deleted:', role_id)
        window.location.reload()
      } catch (err) {
        roleError.value = (err as any).message || 'An error occurred while deleting the role'
      }
    }

    fetchUsers()
    fetchRoles()
    return {
      users,
      roles,
      error,
      roleError,
      showModal,
      userModalOpen,
      selectedRole,
      selectedUser,
      saveUser,
      hideModal,
      editUser,
      deleteUser,
      editRole,
      saveRole,
      closeModal,
      deleteRole,
      userError,
    }
  },
})
</script>

<style scoped>
.modal {
  display: block;
  position: fixed;
  z-index: 1;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: rgb(0, 0, 0);
  background-color: rgba(0, 0, 0, 0.4);
  border-radius: 8px;
}
.modal-content {
  background-color: #fefefe;
  margin: 15% auto; /* 15% from the top and centered */
  padding: 20px;
  border: 1px solid #888;
  width: 30%; /* Could be more or less, depending on screen size */
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
