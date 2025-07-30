<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3'
import MainNavLayout from './Layouts/MainNavLayout.vue'
import FriendButton from './Components/FriendButton.vue'
import AccountMultiple from 'vue-material-design-icons/AccountMultiple.vue'
import Flag from 'vue-material-design-icons/Flag.vue'
import ClockTimeTwoOutline from 'vue-material-design-icons/ClockTimeTwoOutline.vue'
import AccountGroup from 'vue-material-design-icons/AccountGroup.vue'
import StorefrontOutline from 'vue-material-design-icons/StorefrontOutline.vue'
import TelevisionPlay from 'vue-material-design-icons/TelevisionPlay.vue'
import Restore from 'vue-material-design-icons/Restore.vue'
import VideoImage from 'vue-material-design-icons/VideoImage.vue'
import Magnify from 'vue-material-design-icons/Magnify.vue'
import DotsHorizontal from 'vue-material-design-icons/DotsHorizontal.vue'

const props = defineProps({
  requests: Array,
  searchResults: Array,
  searchTerm: String,
  friends: Array,
})

const user = usePage().props.auth.user
</script>

<template>
  <MainNavLayout>
    <Head title="Friend Requests" />

    <div class="fixed w-full h-[100%] bg-[#F1F2F5]">
      <div
        class="grid grid-cols-12 gap-8 w-full max-w-[1600px] mt-[56px] h-[calc(100%-56px)] mx-auto"
      >
        <!-- Left Sidebar -->
        <div class="hidden lg:block col-span-3 xl:w-[345px]">
          <div class="pt-4 max-w-[320px] pr-4 sticky top-[56px] bg-[#F1F2F5] rounded-md">
            <Link
              href=""
              class="flex items-center justify-start w-full cursor-pointer hover:bg-[#E5E6E9] p-2 rounded-md mb-2"
            >
              <img
                class="rounded-full ml-1 min-w-[38px] max-h-[38px]"
                :src="user?.image
                  ? `/storage/${user?.image}`
                  : 'https://i.pinimg.com/736x/15/0f/a8/150fa8800b0a0d5633abc1d1c4db3d87.jpg'"
                alt="User profile"
              />
              <div class="text-[15px] text-gray-800 font-extrabold pl-3">
                {{ user?.name }}
              </div>
            </Link>
            <button
              class="flex items-center justify-start w-full cursor-pointer hover:bg-[#E5E6E9] px-2 py-1.5 rounded-md mb-2"
            >
              <AccountMultiple size="40" fillColor="#5BD7C6" />
              <div class="text-[15px] text-gray-800 font-extrabold pl-3">Friends</div>
            </button>
            <button
              class="flex items-center justify-start w-full cursor-pointer hover:bg-[#E5E6E9] px-2 py-1.5 rounded-md mb-2"
            >
              <Flag size="40" fillColor="#F2682C" />
              <div class="text-[15px] text-gray-800 font-extrabold pl-3">Pages</div>
            </button>
            <button
              class="flex items-center justify-start w-full cursor-pointer hover:bg-[#E5E6E9] px-2 py-1.5 rounded-md mb-2"
            >
              <ClockTimeTwoOutline size="40" fillColor="#21AAFA" />
              <div class="text-[15px] text-gray-800 font-extrabold pl-3">Most Recent</div>
            </button>
            <button
              class="flex items-center justify-start w-full cursor-pointer hover:bg-[#E5E6E9] px-2 py-1.5 rounded-md mb-2"
            >
              <AccountGroup size="40" fillColor="#20A9FD" />
              <div class="text-[15px] text-gray-800 font-extrabold pl-3">Groups</div>
            </button>
            <button
              class="flex items-center justify-start w-full cursor-pointer hover:bg-[#E5E6E9] px-2 py-1.5 rounded-md mb-2"
            >
              <StorefrontOutline size="40" fillColor="#48C0D8" />
              <div class="text-[15px] text-gray-800 font-extrabold pl-3">Marketplace</div>
            </button>
            <button
              class="flex items-center justify-start w-full cursor-pointer hover:bg-[#E5E6E9] px-2 py-1.5 rounded-md mb-2"
            >
              <TelevisionPlay size="40" fillColor="#9739CF" />
              <div class="text-[15px] text-gray-800 font-extrabold pl-3">Watch</div>
            </button>
            <button
              class="flex items-center justify-start w-full cursor-pointer hover:bg-[#E5E6E9] px-2 py-1.5 rounded-md"
            >
              <Restore size="40" fillColor="#32B4D0" />
              <div class="text-[15px] text-gray-800 font-extrabold pl-3">Memories</div>
            </button>
          </div>
        </div>

        <!-- Center Friend Requests -->
       <div
            class="
              col-span-12 
              lg:col-span-6 
              max-w-3xl 
              mx-auto 
              bg-white 
              w-full sm:w-[90%] md:w-1/2 lg:w-[800px]
              rounded-lg 
              shadow-md 
              p-6 
              overflow-auto
            "
          >

          <h1 class="text-2xl font-bold mb-6">Friend Requests</h1>

          <!-- Search Results -->
          <div v-if="searchTerm">
            <h2 class="text-xl font-semibold mb-4">
              Search results for "{{ searchTerm }}"
            </h2>

            <div v-if="searchResults.length === 0" class="text-gray-600 mb-6">
              No users found.
            </div>

            <ul v-else>
              <li
                v-for="user in searchResults"
                :key="user.id"
                class="flex items-center justify-between py-3 border-b border-gray-200"
              >
                <div class="flex items-center gap-4">
                  <img
                    v-if="user.image"
                    :src="`/storage/${user.image}`"
                    alt="Profile"
                    class="w-12 h-12 rounded-full object-cover"
                  />
                  <div
                    v-else
                    class="w-12 h-12 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 font-bold text-lg"
                  >
                    {{ user.name.charAt(0) }}
                  </div>

                  <Link
                    :href="route('user.show', user.id)"
                    class="font-semibold hover:underline"
                  >
                    {{ user.name }}
                  </Link>
                </div>

                <FriendButton
                  v-if="$page.props.auth.user.id !== user.id"
                  :user-id="user.id"
                  :friendship-status="user.friendshipStatus"
                />
              </li>
            </ul>
          </div>

          <hr class="my-6" />

          <!-- Pending Requests -->
          <h2 class="text-xl font-semibold mb-4">Pending Friend Requests</h2>

          <div
            v-if="requests.length === 0"
            class="text-gray-600 text-center py-20"
          >
            No new friend requests
          </div>

          <ul v-else>
            <li
              v-for="request in requests"
              :key="request.id"
              class="flex items-center justify-between py-3 border-b border-gray-200"
            >
              <div class="flex items-center gap-4">
                <img
                  v-if="request.sender.image"
                  :src="`/storage/${request.sender.image}`"
                  alt="Profile"
                  class="w-12 h-12 rounded-full object-cover"
                />
                <div
                  v-else
                  class="w-12 h-12 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 font-bold text-lg"
                >
                  {{ request.sender.name.charAt(0) }}
                </div>

                <Link
                  :href="route('user.show', request.sender.id)"
                  class="font-semibold hover:underline"
                >
                  {{ request.sender.name }}
                </Link>
              </div>

              <FriendButton
                v-if="$page.props.auth.user.id !== request.sender.id"
                :user-id="request.sender.id"
                friendship-status="pending_received"
              />
            </li>
          </ul>
        </div>

        <!-- Right Contacts -->
        <div class="hidden md:block col-span-3 max-w-[340px] min-w-[250px] mx-auto pt-4 overflow-auto sticky top-[56px]">
          <div class="border-b border-b-gray-300 flex items-center justify-between px-4 pb-2">
            <div class="font-semibold">Friends</div>
            <div class="flex items-center gap-3">
              <div
                class="p-2 hover:bg-gray-300 rounded-full cursor-pointer transition"
                title="Video"
              >
                <VideoImage size="23" fillColor="#050505" />
              </div>
              <div
                class="p-2 hover:bg-gray-300 rounded-full cursor-pointer transition"
                title="Search"
              >
                <Magnify size="23" fillColor="#050505" />
              </div>
              <div
                class="p-2 hover:bg-gray-300 rounded-full cursor-pointer transition"
                title="More"
              >
                <DotsHorizontal size="23" fillColor="#050505" />
              </div>
            </div>
          </div>

          <div class="h-[calc(100vh-115px)] overflow-auto pt-2 px-4">
            <div
              v-for="friend in props.friends"
              :key="friend.id"
              class="flex items-center justify-start cursor-pointer hover:bg-[#E5E6E9] py-2 rounded-md"
            >
              <Link
                :href="route('user.show', { id: friend.id })"
                class="flex items-center w-full"
              >
                <img
                  class="rounded-full ml-1 min-w-[38px] max-h-[38px] object-cover"
                  :src="friend.image"
                  :alt="friend.name"
                />
                <div class="text-[15px] text-gray-800 font-extrabold pl-3">
                  {{ friend.name }}
                </div>
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </MainNavLayout>
</template>
