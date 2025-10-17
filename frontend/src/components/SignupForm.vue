<template>
  <div class="flex h-[700px] w-full">
    <div class="w-full flex flex-col items-center justify-center">
      <form @submit.prevent="handleRegister" class="md:w-96 w-80 flex flex-col items-center justify-center">
        <h2 class="text-4xl text-gray-900 font-medium">Sign up</h2>
        <p class="text-sm text-gray-500/90 mt-3 pb-6">Create your account</p>

        <div v-if="error" class="w-full mb-4 p-3 text-red-700">
          {{ error }}
        </div>

        <div v-if="success" class="w-full mb-4 p-3 text-green-700">
          {{ success }}
        </div>

        <div
          class="flex items-center w-full bg-transparent border border-gray-300/60 h-12 rounded-full overflow-hidden pl-6 gap-2 mb-4"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="25"
            height="16"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            class="lucide lucide-user-icon lucide-user"
          >
            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
            <circle cx="12" cy="7" r="4" />
          </svg>
          <input
            v-model="formData.name"
            type="text"
            placeholder="Name"
            class="bg-transparent text-gray-500/80 placeholder-gray-500/80 outline-none text-sm w-full h-full"
            required
          />
        </div>

        <div
          class="flex items-center w-full bg-transparent border border-gray-300/60 h-12 rounded-full overflow-hidden pl-6 gap-2"
        >
          <svg
            width="16"
            height="11"
            viewBox="0 0 16 11"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              fill-rule="evenodd"
              clip-rule="evenodd"
              d="M0 .55.571 0H15.43l.57.55v9.9l-.571.55H.57L0 10.45zm1.143 1.138V9.9h13.714V1.69l-6.503 4.8h-.697zM13.749 1.1H2.25L8 5.356z"
              fill="#6B7280"
            />
          </svg>
          <input
            v-model="formData.email"
            type="email"
            placeholder="Email"
            class="bg-transparent text-gray-500/80 placeholder-gray-500/80 outline-none text-sm w-full h-full"
            required
          />
        </div>

        <div
          class="flex items-center mt-6 w-full bg-transparent border border-gray-300/60 h-12 rounded-full overflow-hidden pl-6 gap-2"
        >
          <svg
            width="13"
            height="17"
            viewBox="0 0 13 17"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              d="M13 8.5c0-.938-.729-1.7-1.625-1.7h-.812V4.25C10.563 1.907 8.74 0 6.5 0S2.438 1.907 2.438 4.25V6.8h-.813C.729 6.8 0 7.562 0 8.5v6.8c0 .938.729 1.7 1.625 1.7h9.75c.896 0 1.625-.762 1.625-1.7zM4.063 4.25c0-1.406 1.093-2.55 2.437-2.55s2.438 1.144 2.438 2.55V6.8H4.061z"
              fill="#6B7280"
            />
          </svg>
          <input
            v-model="formData.password"
            type="password"
            placeholder="Password"
            class="bg-transparent text-gray-500/80 placeholder-gray-500/80 outline-none text-sm w-full h-full"
            required
          />
        </div>

        <button
          type="submit"
          :disabled="isLoading"
          class="mt-8 w-full h-11 rounded-full text-white bg-indigo-500 hover:opacity-90 transition-opacity disabled:opacity-50"
        >
          {{ isLoading ? 'Inscription...' : 'Register' }}
        </button>
        <p class="text-gray-500/90 text-sm mt-4">
          Already have an account?
          <router-link to="/"
            ><span class="hover:underline text-blue-600">Sign in</span></router-link
          >
        </p>
      </form>
    </div>
  </div>
</template>

<script>
import { useAuthStore } from '../stores/authStore.js'
import { mapState } from 'pinia'

export default {
  name: 'SignupForm',
  data() {
    return {
      formData: {
        name: '',
        email: '',
        password: ''
      },
      success: ''
    }
  },
  computed: {
    ...mapState(useAuthStore, ['isLoading', 'error'])
  },
  methods: {
    async handleRegister() {
      const authStore = useAuthStore()
      this.success = ''

      const result = await authStore.register(
        this.formData.name,
        this.formData.email,
        this.formData.password
      )

      if (result.success) {
        this.success = 'Inscription réussie !'
        setTimeout(() => {
          this.$router.push('/dashboard')
        }, 2000)
      }
    }
  }
}
</script>
