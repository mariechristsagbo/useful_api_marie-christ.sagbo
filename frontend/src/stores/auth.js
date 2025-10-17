import { defineStore } from 'pinia'
import apiService from '../services/api.js'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: null,
    isLoading: false,
    error: null,
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    userInfo: (state) => state.user,
  },

  actions: {
    async login(email, password) {
      this.isLoading = true
      this.error = null

      try {
        const response = await apiService.login(email, password)

        this.token = response.token
        this.user = { id: response.user_id }

        apiService.setToken(response.token)

        return { success: true, data: response }
      } catch (error) {
        this.error = error.message
        return { success: false, error: error.message }
      } finally {
        this.isLoading = false
      }
    },

    async register(name, email, password) {
      this.isLoading = true
      this.error = null

      try {
        const response = await apiService.register(name, email, password)

        this.token = response.token
        this.user = {
          id: response.id,
          name: response.name,
          email: response.email
        }

        apiService.setToken(response.token)

        return { success: true, data: response }
      } catch (error) {
        this.error = error.message
        return { success: false, error: error.message }
      } finally {
        this.isLoading = false
      }
    },

    logout() {
      this.user = null
      this.token = null
      this.error = null
      apiService.removeToken()
    },

    clearError() {
      this.error = null
    },

    initializeAuth() {
      const token = apiService.getToken()
      if (token) {
        this.token = token
      }
    }
  }
})

