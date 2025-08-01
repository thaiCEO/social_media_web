<script setup>
import { router } from '@inertiajs/vue3'
import ThumbUp from 'vue-material-design-icons/ThumbUp.vue'
import AccountMultiple from 'vue-material-design-icons/AccountMultiple.vue'
import Earth from 'vue-material-design-icons/Earth.vue'
import Delete from 'vue-material-design-icons/Delete.vue'
import ArrowLeft from 'vue-material-design-icons/ArrowLeft.vue'

const props = defineProps({
  post: Object,
})

const authUser = props.post.auth_user ?? null


const goBack = () => {
  // Get previous URL from document.referrer
  const previousUrl = document.referrer || route('posts.index')

  router.visit(previousUrl, {
    preserveScroll: true,
    preserveState: true,
  })
}




const toggleLike = () => {
  if (!authUser) {
    router.get('/register')
    return
  }
  if (props.post.user_liked) {
    router.delete(`/posts/${props.post.id}/like`)
  } else {
    router.post(`/posts/${props.post.id}/like`)
  }
}
</script>

<template>
  <div class="bg-gray-100 min-h-screen">
    <div class="max-w-[900px] mx-auto pt-4 pb-10">
      <!-- Post Card -->
      <div class="bg-white rounded-lg shadow-md">
        <!-- Header -->
        <div class="flex items-center p-4 border-b gap-3">
          <!-- Back button -->
          <button
           @click="goBack"
            class="p-2 rounded-full hover:bg-gray-100"
            title="Back"
          >
            <ArrowLeft fillColor="#333" />
          </button>

          <img
            class="rounded-full w-12 h-12 object-cover"
            :src="`/storage/${post.user.image}`"
          />
          <div class="flex-1">
            <div class="font-bold">{{ post.user.name }}</div>
            <div class="text-xs text-gray-500 flex items-center gap-1">
              {{ post.created_at }}
              <Earth
                v-if="post.visibility === 'public'"
                :size="14"
                fillColor="#666"
              />
              <AccountMultiple
                v-else
                :size="14"
                fillColor="#666"
              />
            </div>
          </div>
          <button
            v-if="authUser && authUser.id === post.user.id"
            @click="router.delete(`/post/${post.id}`)"
            class="p-2 rounded-full hover:bg-gray-100"
          >
            <Delete fillColor="#666" />
          </button>
        </div>

        <!-- Post text -->
        <div v-if="post.text" class="px-4 py-3 text-gray-800 text-lg">
          {{ post.text }}
        </div>

        <!-- All Images stacked -->
        <div class="flex flex-col">
          <div
            v-for="img in post.images"
            :key="img.id"
            class="flex justify-center bg-black"
          >
            <img
              class="max-h-[85vh] w-auto object-contain"
              :src="`/storage/${img.image}`"
            />
          </div>
        </div>

        <!-- Actions -->
        <div class="p-4 border-t">
          <button
            @click="toggleLike"
            class="flex items-center gap-2 text-gray-600 hover:text-blue-600 font-semibold"
          >
            <ThumbUp
              :fillColor="post.user_liked ? '#1D72E2' : '#64676B'"
              :size="18"
            />
            <span :class="post.user_liked ? 'text-blue-600 font-bold' : ''">
              {{ post.likes_count }} Like
            </span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
