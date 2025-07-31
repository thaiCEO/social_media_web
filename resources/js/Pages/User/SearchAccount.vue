<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

const searchTerm = ref('')
const results = ref([])

const searchUsers = async () => {
  if (searchTerm.value.length < 2) {
    results.value = []
    return
  }
  const res = await fetch(`/friends/search?q=${encodeURIComponent(searchTerm.value)}`)
  results.value = await res.json()
}

const viewProfile = (id) => {
  router.visit(route('user.show', id))
}

// When pressing Enter
const handleEnter = () => {
  if (!searchTerm.value) return
  // Redirect to friend requests page with search term (or you can use a dedicated result page)
  router.visit(route('friendship.incomingRequests', { q: searchTerm.value }))
}
</script>

<template>
  <div class="w-full h-full flex flex-col bg-gray-50">
    <!-- Header bar -->
    <div class="flex items-center p-2 bg-white shadow">
      <!-- Back button -->
      <button
        class="mr-2 p-2 rounded-full hover:bg-gray-100"
        @click="router.visit(route('posts.index'))"
      >
        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
      </button>

      <!-- Search box -->
      <input
        v-model="searchTerm"
        @input="searchUsers"
        @keyup.enter="handleEnter"
        type="text"
        placeholder="Search accounts"
        class="flex-1 border-none outline-none bg-gray-100 rounded-full px-4 py-2 text-sm"
      />
    </div>

    <!-- Results -->
    <div class="flex-1 overflow-y-auto p-2">
      <div v-if="results.length" class="bg-white rounded-lg shadow">
        <div
          v-for="user in results"
          :key="user.id"
          @click="viewProfile(user.id)"
          class="flex items-center p-2 hover:bg-gray-100 cursor-pointer"
        >
          <img
            :src="user.image ? '/storage/'+user.image : 'https://i.pinimg.com/736x/15/0f/a8/150fa8800b0a0d5633abc1d1c4db3d87.jpg'"
            class="w-10 h-10 rounded-full mr-3"
          >
          <span class="font-medium">{{ user.name }}</span>
        </div>
      </div>
      <div v-else-if="searchTerm.length >= 2" class="text-gray-500 text-center mt-4">
        No accounts found
      </div>
    </div>
  </div>
</template>
