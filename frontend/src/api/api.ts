import axios from 'axios'
import type { InternalAxiosRequestConfig, AxiosResponse, AxiosError } from 'axios'

const instance = axios.create({
  withCredentials: true,
  withXSRFToken: true,
})

instance.interceptors.request.use((request: InternalAxiosRequestConfig) => {
  request.headers['Accept'] = 'application/json'
  request.headers['Content-Type'] = 'application/json'
  return request
})

instance.interceptors.response.use(
  (response: AxiosResponse) => {
    return response
  },
  (error: AxiosError) => {
    if (error.response && error.response.status === 401) {
      sessionStorage.removeItem('user')
      window.location.reload()
    }

    return Promise.reject(error)
  },
)

export default instance
