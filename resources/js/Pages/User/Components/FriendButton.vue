<script setup>
import { router } from '@inertiajs/vue3'

const props = defineProps({
  userId: Number,
  friendshipStatus: String
})

const sendRequest = () => {
  router.post(route('friendship.sendRequest', props.userId))
}

const cancelRequest = () => {
  router.delete(route('friendship.cancelRequest', props.userId))
}

const acceptRequest = () => {
  router.post(route('friendship.acceptRequest', props.userId))
}

const declineRequest = () => {
  router.post(route('friendship.declineRequest', props.userId))
}

const removeFriend = () => {
  router.delete(route('friendship.removeFriend', props.userId))
}
</script>





<template>
  <div>
    <!-- Not Friends -->
    <button
      v-if="friendshipStatus === 'not_friends'"
      @click="sendRequest"
      class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700"
    >
      Add Friend
    </button>

    <!-- Pending Sent -->
    <button
      v-else-if="friendshipStatus === 'pending_sent'"
      @click="cancelRequest"
      class="px-4 py-2 rounded-lg bg-gray-300 text-gray-700 hover:bg-gray-400"
    >
      Cancel Request
    </button>

    <!-- Pending Received -->
    <div
      v-else-if="friendshipStatus === 'pending_received'"
      class="flex gap-2"
    >
      <button
        @click="acceptRequest"
        class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700"
      >
        Confirm
      </button>
      <button
        @click="declineRequest"
        class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700"
      >
        Cancel
      </button>
    </div>

    <!-- Already Friends -->
    <button
      v-else-if="friendshipStatus === 'friends'"
      @click="removeFriend"
      class="px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300"
    >
      Friends
    </button>
  </div>
</template>


<style>
</style>