<script setup>
import { ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';

import Magnify from 'vue-material-design-icons/Magnify.vue'
import Home from 'vue-material-design-icons/Home.vue'
import HomeOutline from 'vue-material-design-icons/HomeOutline.vue'
import TelevisionPlay from 'vue-material-design-icons/TelevisionPlay.vue'
import StorefrontOutline from 'vue-material-design-icons/StorefrontOutline.vue'
import AccountGroup from 'vue-material-design-icons/AccountGroup.vue'
import AccountGroupOutline from 'vue-material-design-icons/AccountGroupOutline.vue'
import ControllerClassicOutline from 'vue-material-design-icons/ControllerClassicOutline.vue'
import DotsGrid from 'vue-material-design-icons/DotsGrid.vue'
import FacebookMessenger from 'vue-material-design-icons/FacebookMessenger.vue'
import Bell from 'vue-material-design-icons/Bell.vue'
import Logout from 'vue-material-design-icons/Logout.vue'
import CropperModal from '../Components/CropperModal.vue';
import { route } from 'ziggy-js';

const user = usePage().props.auth.user
let showMenu = ref(false)

const searchTerm = ref('')
const results = ref([])


const handleAvatarClick = () => {
  if (user && user.image) {
    // Toggle menu
    showMenu.value = !showMenu.value
  } else {
    // Redirect to register
    router.visit(route('login'))
  }
}

const searchUsers = async () => {
  if (searchTerm.value.length < 2) {
    results.value = []
    return
  }

  const res = await fetch(`/friends/search?q=${encodeURIComponent(searchTerm.value)}`)
  results.value = await res.json()
}

const viewProfile = (userId) => {
  router.visit(route('user.show', userId))
}

const goToFriendRequests = () => {
  router.visit(route('friendship.incomingRequests', { q: searchTerm.value }))
}

const logout = () => {
  router.post(route('logout'));
};

const handleSearchClick = () => {
  if (window.innerWidth < 1024) {
    // On mobile: open the full search page
    router.visit(route('search'))
  }
  // On desktop: do nothing (the input is already there)
}

</script>

<template>
  <div
    id="MainNav"
    class="fixed top-0 z-50 w-full flex items-center justify-between bg-white shadow-xl border-b "
  >
    <!-- Left Section -->
    <div id="NavLeft" class="flex items-center justify-start px-3">
     <Link href="" class="min-w-[55px]">
       <h2 class="font-extrabold text-blue-600 text-2xl hover:text-blue-800 transition-colors duration-300">
          Camboshare
        </h2>
      </Link>

      <div class="flex items-center justify-center bg-[#EFF2F5] p-1 rounded-full h-[40px] ml-2 relative">
       <Magnify
          class="p-1 cursor-pointer"
          :size="22"
          fillColor="#64676B"
          @click="handleSearchClick"
        />

        <input
          v-model="searchTerm"
          @input="searchUsers"
          @keyup.enter="goToFriendRequests"
          class="lg:block hidden border-none p-0 bg-[#EFF2F5]
            placeholder-[#64676B] ring-0 focus:ring-0"
          placeholder="Search accounts"
          type="text"
        />
        <!-- Dropdown results -->
        <div v-if="results.length && searchTerm"
          class="absolute top-11 left-0 bg-white shadow-lg rounded-lg w-72 max-h-80 overflow-auto z-50">
          <div v-for="user in results" :key="user.id"
            @click.stop="viewProfile(user.id)"
            class="flex items-center justify-between p-2 hover:bg-gray-100 cursor-pointer">
            <div class="flex items-center">
              <img :src="user.image ? '/storage/'+user.image : 'https://i.pinimg.com/736x/15/0f/a8/150fa8800b0a0d5633abc1d1c4db3d87.jpg'"
                class="w-8 h-8 rounded-full mr-2">
              <span>{{ user.name }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Center Buttons -->
    <div id="NavCenter" class="flex items-center justify-center space-x-1">
      <Link class="w-full" :href="route('posts.index')">
        <div
          :class="$page.url === '/post' ? 'mt-1.5' : ''"
          class="flex items-center justify-center h-[48px] p-1 hover:bg-[#F2F2F2] w-full rounded-lg cursor-pointer"
        >
          <div>
            <Home v-if="$page.url === '/post'" class="mx-auto" :size="27" fillColor="#1A73E3" />
            <HomeOutline v-else class="mx-auto" :size="32" fillColor="#64676B" />
          </div>
        </div>
        <div
          v-if="$page.url === '/post'"
          class="border-b-4 border-b-blue-400 rounded-md"
        ></div>
      </Link>

      <Link class="w-full" :href="route('friendship.incomingRequests')">
        <div
          :class="$page.url === '/friend-requests' ? 'mt-1.5' : ''"
          class="relative flex items-center justify-center h-[48px] p-1 hover:bg-[#F2F2F2] w-full rounded-lg cursor-pointer"
        >
          <AccountGroup
            v-if="$page.url === '/friend-requests'"
            class="mx-auto"
            :size="27"
            fillColor="#1A73E3"
          />
          <AccountGroupOutline
            v-else
            class="mx-auto"
            :size="32"
            fillColor="#64676B"
          />
          <!-- Notification Badge -->
          <span
            v-if="$page.props.friendRequestsCount > 0"
            class="absolute top-1 right-4 bg-red-500 text-white text-xs font-bold rounded-full px-1.5 py-0.5"
          >
            {{ $page.props.friendRequestsCount }}
          </span>
        </div>
        <div
          v-if="$page.url === '/friend-requests'"
          class="border-b-4 border-b-blue-400 rounded-md"
        ></div>
      </Link>

      <button class="flex items-center justify-center h-[48px] p-1 hover:bg-[#F2F2F2] w-full rounded-lg mx-1 cursor-pointer">
        <TelevisionPlay class="mx-auto" :size="27" fillColor="#64676B"/>
      </button>
      <button class="flex items-center justify-center h-[48px] p-1 hover:bg-[#F2F2F2] w-full rounded-lg mx-1 cursor-pointer">
        <StorefrontOutline class="mx-auto" :size="27" fillColor="#64676B"/>
      </button>
      <button class="flex items-center justify-center h-[48px] p-1 hover:bg-[#F2F2F2] w-full rounded-lg mx-1 cursor-pointer">
        <ControllerClassicOutline class="mx-auto" :size="32" fillColor="#64676B"/>
      </button>
    </div>

    <!-- Right Section -->
    <div class="flex items-center justify-end pr-4">
     
      <!-- <button class="rounded-full bg-[#E3E6EA] p-2 hover:bg-gray-300 mx-1 cursor-pointer">
        <FacebookMessenger :size="23" fillColor="#050505"/>
      </button> -->
      <button class="relative rounded-full bg-[#E3E6EA] p-2 hover:bg-gray-300 mx-1 cursor-pointer">
        <Bell :size="23" fillColor="#050505" />
        <span
          v-if="$page.props.notificationsCount > 0"
          class="absolute -top-1 -right-1 bg-red-600 text-white text-xs font-bold px-1.5 py-0.5 rounded-full"
        >
          {{ $page.props.notificationsCount }}
        </span>
      </button>

      <div class="flex items-center justify-center relative">
         <button @click="handleAvatarClick">
            <img
              class="rounded-full ml-1 min-w-[40px] max-h-[40px] cursor-pointer"
              :src="user?.image 
                ? `/storage/${user.image}` 
                : 'https://i.pinimg.com/736x/15/0f/a8/150fa8800b0a0d5633abc1d1c4db3d87.jpg'"
            >
          </button>
        <div
          v-if="showMenu"
          class="absolute bg-white shadow-xl top-10 right-0 w-[330px] rounded-lg p-1 border mt-1"
        >
          <Link :href="route('user.show' , {id: user.id})" @click="showMenu = !showMenu">
            <div class="flex items-center gap-3 hover:bg-gray-200 p-2 rounded-lg">
              <img class="rounded-full ml-1 min-w-[35px] max-h-[35px] cursor-pointer" 
                :src="`/storage/${user?.image}`">
              <span>{{ user.name }}</span>
            </div>
          </Link>
          <Link 
            :href="route('logout')" 
            method="post" 
            as="button" 
            class="w-full"
          >
            <div class="flex items-center gap-3 hover:bg-gray-200 px-2 py-2.5 rounded-lg">
              <Logout class="pl-2" :size="30"/>
              <span>Logout</span>
            </div>
          </Link>
          <div class="text-xs font-semibold p-2 pt-3 border-t mt-1">
            Privacy · Terms · Advertising · Ad Choices · Cookies · Meta © 2023
          </div>
        </div>
      </div>
    </div>
  </div>

  <main class="ContentPost">
    <slot />
  </main>

  <CreatePostOverlay
    v-if="isPostOverlay"
    @showModal="isPostOverlay = false"
  />

  <CropperModal
    v-if="isCropperModal"
    @showModal="isCropperModal = false"
  />

  <ImageDisplay
    v-if="isImageDisplay"
  />
</template>

<style scoped>
#MainNav {
  height: 56px;
  padding: 0 1rem;
}

/* On mobile */
@media (max-width: 767px) {
  #MainNav {
    flex-wrap: wrap;
    height: auto;
    padding-bottom: 0.5rem;
  }
  .ContentPost{
    margin-top: 3rem;
  }

  #NavCenter {
    order: 3;
    flex: 1 0 100%;
    justify-content: space-around;
    margin-top: 0.5rem;
  }
}

/* On laptop and above */
@media (min-width: 1024px) {
  #MainNav {
    display: grid;
    grid-template-columns: auto 1fr auto;
    align-items: center;
  }

  #NavLeft {
    justify-content: flex-start;
  }

  #NavCenter {
    display: flex;
    justify-content: center;
    max-width: 50%;
    margin-left: 200px;
    
  }
}
</style>
