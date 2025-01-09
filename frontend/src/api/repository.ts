import api from './api'
import axios from 'axios'

interface LoginParams {
  email: string
  password: string
}

interface UserParams {
  email: string
  password: string | null
  // password_confirmation: string
  role_id: null | number
  name: string
}

interface RoleParams {
  id: number | null
  name: string
  description: string | null
}

export default {
  createSession() {
    return api.get('http://localhost:8000/sanctum/csrf-cookie')
  },

  login(params: LoginParams) {
    return api.post('http://localhost:8000/api/login', params)
  },

  logout() {
    return api.delete('http://localhost:8000/api/logout')
  },

  getUsers() {
    return api.get('http://localhost:8000/api/user')
  },

  getRoles() {
    return api.get('http://localhost:8000/api/role')
  },

  createUser(params: UserParams) {
    return api.post('http://localhost:8000/api/user', params)
  },

  updateUser(params: UserParams, id: number) {
    return api.put(`http://localhost:8000/api/user/${id}`, params)
  },

  createRole(params: RoleParams) {
    return api.post('http://localhost:8000/api/role', params)
  },

  updateRole(params: RoleParams) {
    return api.post(`http://localhost:8000/api/role/`, params)
  },

  destroyUser(id: number) {
    return api.delete(`http://localhost:8000/api/user/${id}`)
  },

  destroyRole(id: number) {
    return api.delete(`http://localhost:8000/api/role/${id}`)
  },
}
