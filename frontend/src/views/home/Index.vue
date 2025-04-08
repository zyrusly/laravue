<template>
  <div class="home-container">
    <h3>Users:</h3>
    <div v-if="users" class="users-wrapper" style="width: 50%">
      <div v-for="(user, index) in users" :key="index" class="user">
        <br />
        <div class="card">
          <p><strong>Name:</strong> {{ user.name }}</p>
          <p><strong>Email:</strong> {{ user.email }}</p>
          <p><strong>Role:</strong> {{ user.role?.name  }} </p>
          <button @click="editUser(user)" class="edit-btn">Edit</button>&nbsp;&nbsp;
          <button @click="deleteUser(user.id)" class="delete-btn">Delete</button> <br />
        </div>
      </div>
    </div>
    <p v-else>loading...</p>
    <h3>Roles:</h3>
    <div v-if="roles" class="roles-wrapper" style="width: 50%">
      <div v-for="(role, index) in roles" :key="index" class="role">
        <br />
       <div class="card" style="background-color:aliceblue;">
          <h3>{{ role.name }}</h3>
          <p>{{ role.description }}</p>
          <button @click="editRole(role)" class="edit-btn">Edit</button>&nbsp;&nbsp;
          <button @click="deleteRole(role.id)" class="delete-btn">Delete</button> <br />
       </div>
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
        <p v-if="roleError" style="color: red">{{ roleError }}</p>
        <button @click="saveRole">Update</button>&nbsp;&nbsp;
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
          <!-- <option value="null">No Role Selected</option> -->
          <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}</option>
        </select>
        <br />
        <p v-if="error" style="color: red">{{ error }}</p>
        <br />
        <button @click="saveUser">Update</button> &nbsp; &nbsp; &nbsp;
        <button type="button" @click="hideModal">Cancel</button>
        <br />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { defineComponent, ref, reactive, onMounted } from 'vue'
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

// export default defineComponent({
//   name: 'UsersIndex',
//   setup() {
    const users = reactive<User[] >([])
    const roles = reactive<Role[]>([])
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
        Object.assign(users, data)
        console.log('Users:', users)
      } catch (err) {
        error.value = (err as any).message || 'An error occurred'
      }
    }

    const fetchRoles = async () => {
      try {
        const { data } = await repository.getRoles()
        Object.assign(roles, data)
      } catch (err) {
        roleError.value = (err as any).message || 'An error occurred'
      }
    }

    const editUser = (user: User) => {
      selectedUser.value = { ...user }
      userModalOpen.value = true
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
          fetchRoles()
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
          fetchUsers()
          hideModal()
          
        } catch (err) {
          userError.value = (err as any).message || 'An error occurred while saving the user'
        }
      }
    }

    const closeModal = () => {
      showModal.value = false
      selectedRole.value = null
      error.value = null
      roleError.value = null
      userError.value = null
    }

    const hideModal = () => {
      userModalOpen.value = false
      selectedUser.value = null
      error.value = null
      roleError.value = null
      userError.value = null
    }

    const deleteRole = async (role_id: number) => {
      const confirmDelete = window.confirm('Are you sure you want to delete this Role?')
      if (!confirmDelete) return
      try {
        await repository.destroyRole(role_id)
        // fetchRoles()
        // console.log('Role deleted:', role_id)
        window.location.reload()
        alert('Role deleted successfully.')
      } catch (err) {
        roleError.value = (err as any).message || 'An error occurred while deleting the role'
      }
    }

    const deleteUser = async (userId: number) => {

      const confirmDelete = window.confirm('Are you sure you want to delete this User?')
      
      if (!confirmDelete) return
      try {
        await repository.destroyUser(userId)
        console.log('User deleted:', userId)
        alert('User deleted successfully.')
        window.location.reload()
      } catch (err) {
        error.value = (err as any).message || 'An error occurred while deleting the user'
      }
    }

    onMounted(() => {
      fetchUsers()
      fetchRoles()
    })
//   },
// })
</script>

<style scoped>
.home-container {
  display: flex;
  justify-content: center;
  margin-top: 20px;
}

h2,h1,h3,p {
  font-family: 'Arial', sans-serif;
  margin: 15px 8px;
}

.card {
  background-color: #f9f9f9;
  /* border: 1px solid #ccc; */
  border-radius: 5px;
  padding: 20px;
  margin: 10px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.edit-btn {
  background-color: #4CAF50; /* Green */
  border: none;
  color: white;
  padding: 10px 20px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: .8rem;
  margin: 4px 2px;
  cursor: pointer;
}

.delete-btn {
  background-color: #f44336; /* Red */
  border: none;
  color: white;
  padding: 10px 20px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: .8rem;
  margin: 4px 2px;
  cursor: pointer;
}

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
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
}
.modal-content {
  background-color: #fefefe;
  margin: 15% auto;
  padding: 20px;
  border: 1px solid #888;
  width: 30%; 
  border-radius: 8px;
  min-width: 350px;
}

.modal-content > input {
  width: 96.2%;
  padding: 10px;
  margin: 10px 0;
  border: 1px solid #ccc;
  border-radius: 5px;
}
.modal-content > select {
  width: 100%;
  padding: 10px;
  margin: 10px 0;
  border: 1px solid #ccc;
  border-radius: 5px;
}
.modal-content > button {
  width: 100%;
  padding: 10px;
  background-color: #292929;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}
.modal-content > button:hover {
  background-color: #555;
  color: white;
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
