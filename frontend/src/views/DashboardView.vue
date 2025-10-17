<template>
  <div class="min-h-screen bg-gray-100 flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg w-full max-w-md">
      <h1 class="text-2xl font-bold text-gray-900 mb-4">Dashboard</h1>
      <p class="text-gray-600 mb-6">hello !</p>

      <div class="space-y-4">
        <div class="p-4 text-green-700">
          <p class="font-semibold">Connexion réussie !</p>
        </div>

        <button
          @click="handleLogout"
          class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded transition-colors"
        >
          Se déconnecter
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { useAuthStore } from '../stores/auth.js'

export default {
  name: 'DashboardView',
  methods: {
    handleLogout() {
      const authStore = useAuthStore()
      authStore.logout()
      this.$router.push('/')
    }
  }
}
</script>

