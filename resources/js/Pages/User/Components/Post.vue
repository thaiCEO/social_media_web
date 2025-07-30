<script setup>
import { toRefs, reactive, computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import ThumbUp from 'vue-material-design-icons/ThumbUp.vue'
import Delete from 'vue-material-design-icons/Delete.vue'
import AccountMultiple from 'vue-material-design-icons/AccountMultiple.vue'
import Check from 'vue-material-design-icons/Check.vue'
import Earth from 'vue-material-design-icons/Earth.vue';

const page = usePage()
const authUser = computed(() => page.props.auth.user ?? null)




const form = reactive({ comment: null })

const props = defineProps({
    user: Object,
    post: Object,
    comments: Object,
});

const { post, user, comments } = toRefs(props)

const createComment = () => {

     if (!authUser) {
        // redirect guest to login
        router.get('/register')
        return
    }

    router.post('/comment', {
        post_id: post.value.id,
        text: form.comment
    }, { preserveScroll: true })
}

const deleteComment = (id) => {
    if (!authUser) return
    router.delete('/comment/' + id, { preserveScroll: true })
}

const deletePost = (id) => {
    if (!authUser) return
    router.delete('/post/' + id, { preserveScroll: true })
}

const isUser = () => {
    if (authUser) router.get('/user/' + user.value.id)
}

const toggleLike = () => {

      if (!authUser) {
        router.get('/register')
        return
      }
    
    if (post.value.user_liked) {
        router.delete(`/posts/${post.value.id}/like`, { preserveScroll: true })
    } else {
        router.post(`/posts/${post.value.id}/like`, {}, { preserveScroll: true })
    }
}
</script>

<template>
  <div id="Post" class="w-full bg-white rounded-lg my-4 shadow-md ">
    <!-- Post Header -->
    <div class="flex items-center py-3 px-3">
      <button @click="isUser" class="mr-2">
        <img class="rounded-full ml-1 min-w-[42px] max-h-[42px]" :src="`/storage/${user.image}`">
      </button>
      <div class="flex items-center justify-between p-2 rounded-full w-full">
        <div>
          <div class="font-extrabold text-[15px]">{{ user.name }}</div>
          <div class="flex items-center text-xs text-gray-600">
            {{ post.created_at }} 
            <Earth v-if="post.visibility === 'public'" class="ml-1" :size="15" fillColor="#64676B" />
             <AccountMultiple v-else class="ml-1" :size="15" fillColor="#64676B" />
          </div>
        </div>
        <div class="flex items-center">
          <!-- Only show delete button if user is logged in and owner -->
          <button
            v-if="authUser && authUser.id === post.user.id"
            @click="deletePost(post.id)"
            class="rounded-full p-1.5 cursor-pointer hover:bg-[#F2F2F2]">
            <Delete fillColor="#64676B" />
          </button>
        </div>
      </div>
    </div>

    <!-- Post Content -->
    <div class="px-5 pb-2 text-[17px] font-semibold">{{ post.text }}</div>
    <img v-if="post.image" class="mx-auto cursor-pointer" :src="`/storage/${post.image}`">

    <!-- Likes -->
    <div id="Likes" class="px-5">
      <div class="flex items-center justify-between py-3 border-b">
        <button 
          @click="toggleLike" 
          class="flex items-center gap-1 cursor-pointer"
         >
          <ThumbUp :fillColor="post.user_liked ? '#1D72E2' : '#64676B'" :size="18" />
          <span  :class="post.user_liked ? 'text-blue-600 font-bold' : 'text-gray-600'">
            {{ post.likes_count }} Like
          </span>
        </button>
        <div class="text-sm text-gray-600 font-semibold">{{ comments.length }} comments</div>
      </div>
    </div>

    <!-- Comments -->
    <div id="Comments" class="px-3">
      <!-- Create Comment -->
      <div id="CreateComment" class="flex items-center justify-between py-2" v-if="authUser">
        <div class="flex items-center w-full">
          <Link :href="route('user.show', { id: authUser.id })" class="mr-2">
            <img class="rounded-full ml-1 min-w-[36px] max-h-[36px]" :src="`/storage/${authUser.image}`">
          </Link>
          <div class="flex items-center justify-center bg-[#EFF2F5] p-2 rounded-full w-full">
            <input
              v-model="form.comment"
              class="w-full mx-1 border-none p-0 text-sm bg-[#EFF2F5] ring-0 focus:ring-0"
              placeholder="Write a public comment..."
              type="text"
            >
            <button
              type="button"
              @click="createComment"
              class="flex items-center text-sm pl-2 pr-3.5 rounded-full bg-blue-500 hover:bg-blue-600 text-white font-bold"
            >
              <Check fillColor="#FFFFFF" :size="20"/> Send
            </button>
          </div>
        </div>
      </div>

      <!-- Comment List -->
      <div v-if="comments" class="max-h-[120px] overflow-auto pb-2 px-4">
        <div
          class="flex items-center justify-between pb-2"
          v-for="comment in comments" :key="comment.id">
          <div class="flex items-center w-full mb-1">
            <Link :href="route('posts.index')" class="mr-2">
              <img class="rounded-full ml-1 min-w-[36px] max-h-[36px]" :src="`/storage/${comment.user.image}`">
            </Link>
            <div class="flex items-center w-full">
              <div class="flex items-center bg-[#EFF2F5] text-xs p-2 rounded-lg w-full">
                {{ comment.text }}
              </div>
              <button
                v-if="authUser && authUser.id === comment.user.id"
                @click="deleteComment(comment.id)"
                class="rounded-full p-1.5 ml-2 cursor-pointer hover:bg-[#F2F2F2]">
                <Delete fillColor="#64676B"/>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>


