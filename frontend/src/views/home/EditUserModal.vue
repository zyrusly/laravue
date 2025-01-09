<template>
  <div v-if="showModal" class="modal">
    <div class="modal-content">
      <span class="close" @click="closeModal">&times;</span>
      <h3>Edit User</h3>
      <br />
      <label for="userName">Name:</label>
      <br />
      <input type="text" id="userName" v-model="user.name" />
      <br />
      <br />
      <label for="userEmail">Email:</label>
      <br />
      <input type="email" id="userEmail" v-model="user.email" />
      <br />
      <br />
      <label for="userRole">Role:</label>
      <br />
      <select id="userRole" v-model="user.role_id">
        <option :value="null">No role assigned</option>
        <option v-for="role in roles" :key="role.id" :value="role.id">
          {{ role.name }}
        </option>
      </select>
      <br />
      <br />
      <button @click="saveUser">Save</button>&nbsp;&nbsp;
      <button @click="closeModal">Close</button>
      <br />
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue'
import type { PropType } from 'vue'
import repository from '@/api/repository'

interface User {
  id: number
  name: string
  email: string
  role_id: number | null
  password: null | string
}

interface Role {
  id: number
  name: string
  description: string | null
}

export default defineComponent({
  name: 'EditUserModal',
  props: {
    showModal: {
      type: Boolean,
      required: true,
    },
    user: {
      type: Object as PropType<User>,
      required: true,
    },
    roles: {
      type: Array as PropType<Role[]>,
      required: true,
    },
  },
  setup(props, { emit }) {
    const saveUser = async () => {
      try {
        await repository.updateUser({
          id: props.user.id,
          name: props.user.name,
          email: props.user.email,
          role_id: props.user.role_id,
          password: null,
        })
        emit('close')
      } catch (err) {
        alert('An error occurred while saving the user')
      }
    }

    const closeModal = () => {
      emit('close')
    }

    return {
      saveUser,
      closeModal,
    }
  },
})
</script>
