<script setup>
import { ref } from 'vue'
import { router, usePage ,Link } from '@inertiajs/vue3'
// Icons
import VideoImage from 'vue-material-design-icons/VideoImage.vue'
import Image from 'vue-material-design-icons/Image.vue'
import EmoticonOutline from 'vue-material-design-icons/EmoticonOutline.vue'

const page = usePage()


// Child components
import CropperModal from './CropperModal.vue'
import CreatePostOverlay from './CreatePostOverlay.vue'

const isModalOpen = ref(false)
const isPostOverlay = ref(false)

const openModal = () => {
  isModalOpen.value = true
}

const closeModal = () => {
  isModalOpen.value = false
}


//when guest user click show register 
const handleProfileClick = () => {
  const user = page.props.auth.user
  if (user && user.image) {
    // If user has image, maybe open a menu or do nothing
    console.log('Profile clicked') 
  } else {
    // If no image, redirect to register page
    router.visit(route('login'))
  }
}

</script>

<template>
  <!-- CreatePostOverlay shows only if isPostOverlay is true -->
  <CreatePostOverlay
    v-if="isPostOverlay"
    @showModal="isPostOverlay = false"
  />

  <!-- CropperModal shows only if isModalOpen is true -->
  <CropperModal v-if="isModalOpen" @showModal="closeModal" />

  <div id="CreatePostBox" class="w-full bg-white rounded-lg px-3 mt-4 shadow-md ">
    <!-- Top Section -->
    <div class="flex items-center py-3 border-b">
        <button @click="handleProfileClick" class="mr-2">
            <img
              class="rounded-full ml-1 min-w-[36px] max-h-[36px]"
              :src="$page.props.auth.user?.image 
                ? `/storage/${$page.props.auth.user?.image}` 
                : 'https://i.pinimg.com/736x/15/0f/a8/150fa8800b0a0d5633abc1d1c4db3d87.jpg'"
              alt="Profile"
            />
          </button>
      <div
        
        class="flex items-center justify-start bg-[#EFF2F5] p-2 rounded-full w-full cursor-pointer"
      >
        <div @click="isPostOverlay = true" class="text-left pl-2">create post ...</div>
      </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex items-center py-3 border-b">
      <button
        class="flex items-center justify-center p-1 hover:bg-[#F2F2F2] w-full rounded-lg mx-1 cursor-pointer"
      >
        <VideoImage :size="35" fillColor="#F12848" />
        <div class="text-[#6F7275] font-bold">Live</div>
      </button>
      <button
       @click="isPostOverlay = true"
        class="flex items-center justify-center p-1 hover:bg-[#F2F2F2] w-full rounded-lg mx-1 cursor-pointer"
      >
        <Image :size="28" fillColor="#43BE62" />
        <div class="text-[#6F7275] font-bold">Photo/video</div>
      </button>
      <button
        class="flex items-center justify-center p-1 hover:bg-[#F2F2F2] w-full rounded-lg mx-1 cursor-pointer"
      >
        <EmoticonOutline :size="30" fillColor="#F8B927" />
        <div class="text-[#6F7275] font-bold">activity</div>
      </button>
    </div>
  </div>
</template>
