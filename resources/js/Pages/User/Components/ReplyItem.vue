<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import ThumbUp from 'vue-material-design-icons/ThumbUp.vue'

const props = defineProps({
  reply: Object,
  parentUser: Object,
  authUser: Object,
  replyForm: Object,
  toggleCommentLike: Function,
  createReply: Function,
  deleteComment: Function,
})

// Helper for image path
const userImage = (image) => {
  if (!image) {
    return 'https://i.pinimg.com/736x/15/0f/a8/150fa8800b0a0d5633abc1d1c4db3d87.jpg'
  }
  // If it's a full URL (starts with http), return as-is
  if (image.startsWith('http')) {
    return image
  }
  // Otherwise, assume Laravel storage
  return `/storage/${image}`
}
</script>

<template>
  <div class="flex flex-col">
    <div class="flex items-start">
      <Link :href="route('user.show', { id: reply.user.id })" class="mr-2">

      
        <img
          class="rounded-full min-w-[30px] max-h-[30px]"
          :src="reply.user.image ?? 'https://i.pinimg.com/736x/15/0f/a8/150fa8800b0a0d5633abc1d1c4db3d87.jpg'"
        />

        
      </Link>
      <!-- <Link :href="route('user.show', { id: reply.user.id })" class="mr-2">


        <img
          class="rounded-full min-w-[30px] max-h-[30px]"
           :src="userImage(reply.user.image)"
        />

        
      </Link> -->
      <div class="w-full">
        <div class="bg-[#EFF2F5] text-xs p-2 rounded-lg inline-block">
          <p class="font-bold">
            {{ authUser && authUser.id === reply.user.id ? authUser.name : reply.user.name }}
          </p>
          <span class="text-gray-600">Reply {{ parentUser.name }}: </span>
          <span class="text-gray-800">{{ reply.text }}</span>
        </div>

        <div class="flex items-center gap-3 ml-1 mt-1 text-[11px] text-gray-500">
          <button
            @click="toggleCommentLike(reply)"
            class="flex items-center gap-1 cursor-pointer hover:underline"
          >
            <ThumbUp :fillColor="reply.user_comment ? '#1D72E2' : '#64676B'" :size="16" />
            <span :class="reply.user_comment ? 'text-blue-600 font-bold' : 'text-gray-600'">
              {{ reply.likes_count_comment }} Like
            </span>
          </button>

          <button
            @click="replyForm.parent_id = reply.id; replyForm.replyingToUser = reply.user.name; replyForm.text = ''"
            class="hover:underline"
          >
            Reply
          </button>

          <button
            v-if="authUser && authUser.id === reply.user.id"
            @click="deleteComment(reply.id)"
            class="hover:underline text-red-500"
          >
            Delete
          </button>
        </div>
      </div>
    </div>

    <!-- Reply input under a reply -->
    <div v-if="replyForm.parent_id === reply.id" class="mt-2 flex items-start gap-2 ml-8">
      <Link :href="route('user.show', { id: authUser.id })" class="mt-1">
        <img class="rounded-full min-w-[30px] max-h-[30px]" :src="`/storage/${authUser.image}`" />
      </Link>
      <div class="flex flex-col w-full">
        <div class="text-xs text-gray-600 mb-1">
          Replying to <span class="font-semibold">{{ replyForm.replyingToUser }}</span>
        </div>
        <div class="flex gap-2">
          <input
            v-model="replyForm.text"
            type="text"
            placeholder="Write a reply..."
            class="lg:w-[200px] w-[155px] p-2 border rounded text-sm"
          />
          <button
            @click="createReply(reply.id)"
            class="bg-blue-500 hover:bg-blue-600 text-white px-4 rounded"
            :disabled="!replyForm.text.trim()"
          >
            Send
          </button>
        </div>
      </div>
    </div>

    <!-- RECURSION: render nested replies -->
    <div v-if="reply.replies && reply.replies.length" class="mt-2 ml-8 space-y-2">
      <ReplyItem
        v-for="nestedReply in reply.replies"
        :key="nestedReply.id"
        :reply="nestedReply"
        :parentUser="reply.user"
        :authUser="authUser"
        :replyForm="replyForm"
        :toggleCommentLike="toggleCommentLike"
        :createReply="createReply"
        :deleteComment="deleteComment"
      />
    </div>
  </div>
</template>
