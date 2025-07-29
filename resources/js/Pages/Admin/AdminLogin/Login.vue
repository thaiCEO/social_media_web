<script setup>
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const form = useForm({
  email: '',
  password: ''
})

const submit = () => {
  form.post(route('admin.login.submit'))
}
</script>

<template>
  <div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-white shadow-md rounded-lg p-6 w-full max-w-sm">
      <h2 class="text-xl font-bold mb-4 text-center">Admin Login</h2>
      
      <form @submit.prevent="submit">
        <div class="mb-3">
          <label class="block mb-1">Email</label>
          <input type="email" v-model="form.email" class="border rounded w-full px-3 py-2" />
          <div v-if="form.errors.email" class="text-red-500 text-sm">{{ form.errors.email }}</div>
        </div>

        <div class="mb-3">
          <label class="block mb-1">Password</label>
          <input type="password" v-model="form.password" class="border rounded w-full px-3 py-2" />
          <div v-if="form.errors.password" class="text-red-500 text-sm">{{ form.errors.password }}</div>
        </div>

        <button :disabled="form.processing" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded">
          Login
        </button>
      </form>
    </div>
  </div>
</template>
