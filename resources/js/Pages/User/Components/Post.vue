<script setup>
import { toRefs, reactive, computed , ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import ThumbUp from 'vue-material-design-icons/ThumbUp.vue'
import Delete from 'vue-material-design-icons/Delete.vue'
import AccountMultiple from 'vue-material-design-icons/AccountMultiple.vue'
import Check from 'vue-material-design-icons/Check.vue'
import Earth from 'vue-material-design-icons/Earth.vue';
import ReplyItem from './ReplyItem.vue';

const page = usePage()
const authUser = computed(() => page.props.auth.user ?? null)


const form = reactive({ comment: null })

const props = defineProps({
    user: Object,
    post: Object,
    comments: Object,
});

const { post, user, comments } = toRefs(props)


// Compute total comments + replies count
const totalCommentsCount = computed(() => {
  if (!comments.value) return 0;

  // If comments is object, convert to array
  const commentsArray = Array.isArray(comments.value) ? comments.value : Object.values(comments.value);

  return commentsArray.reduce((total, comment) => {
    return total + 1 + (comment.replies ? comment.replies.length : 0);
  }, 0);
});






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


//like comment reply comment

const replyForm = reactive({
  text: '',
  parent_id: null,
  replyingToUser: null,  // store username of the comment being replied to
});

const toggleCommentLike = (comment) => {
  if (!authUser.value) { 
    router.get('/register');
    return
  }

  if (comment.user_comment) {
    router.delete(`/comments/${comment.id}/like`, { preserveScroll: true })
  } else {
    router.post(`/comments/${comment.id}/like`, {}, { preserveScroll: true })
  }
}



const createReply = (parentId) => {
  router.post('/comment', {
    post_id: post.value.id,
    text: replyForm.text,
    parent_id: parentId
  }, { preserveScroll: true })

  replyForm.text = ''
  replyForm.parent_id = null
  replyForm.replyingToUser = ''
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
        <div class="text-sm text-gray-600 font-semibold">{{ totalCommentsCount }} comments</div>
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

   
<!-- Comment List start -->
<div v-if="comments" class="max-h-[300px] overflow-auto pb-2 px-4">
  <div v-for="comment in comments" :key="comment.id" class="pb-3">
    <!-- Main Comment -->
    <div class="flex items-start">
      <Link :href="route('user.show',{id: comment.user.id})" class="mr-2">
        <img class="rounded-full min-w-[36px] max-h-[36px]"  
             :src="comment.user.image ?? 'https://i.pinimg.com/736x/15/0f/a8/150fa8800b0a0d5633abc1d1c4db3d87.jpg'">
      </Link>
      <div class="w-full">
        <!-- Main comment text -->
        <div class="bg-[#EFF2F5] text-xs p-2 rounded-lg inline-block">
          <p>{{ comment.user.name }}</p>
          <span class="text-gray-800">{{ comment.text }}</span>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-3 ml-1 mt-1 text-[11px] text-gray-500">
          <button 
            @click="toggleCommentLike(comment)" 
            class="flex items-center gap-1 cursor-pointer hover:underline"
          >
            <ThumbUp :fillColor="comment.user_liked ? '#1D72E2' : '#64676B'" :size="16" />
            <span :class="comment.user_comment ? 'text-blue-600 font-bold' : 'text-gray-600'">
              {{ comment.likes_count_comment }} Like
            </span>
          </button>

          <button
            @click="replyForm.parent_id = comment.id; replyForm.replyingToUser = comment.user.name"
            class="hover:underline"
          >
            Reply
          </button>

          <button
            v-if="authUser && authUser.id === comment.user.id"
            @click="deleteComment(comment.id)"
            class="hover:underline text-red-500"
          >
            Delete
          </button>
        </div>

        <!-- Reply Input (for replying to this main comment) -->
        <div v-if="replyForm.parent_id === comment.id" class="mt-2 flex items-start gap-2 ml-8">
          <Link :href="route('user.show', { id: authUser.id })" class="mt-1">
            <img class="rounded-full min-w-[36px] max-h-[36px]" :src="`/storage/${authUser.image}`" />
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
                @click="createReply(comment.id)"
                class="bg-blue-500 hover:bg-blue-600 text-white px-4 rounded"
                :disabled="!replyForm.text.trim()"
              >
                Send
              </button>
            </div>
          </div>
        </div>

        <!-- Nested Replies -->

        <div v-if="comment.replies && comment.replies.length" class="mt-2 ml-8 space-y-2">
          <ReplyItem
            v-for="reply in comment.replies"
            :key="reply.id"
            :reply="reply"
            :parentUser="comment.user"
            :authUser="authUser"
            :replyForm="replyForm"
            :toggleCommentLike="toggleCommentLike"
            :createReply="createReply"
            :deleteComment="deleteComment"
          />
        </div>


      </div>
    </div>
  </div>
</div>
<!-- Comment List end -->


    </div>
  </div>
</template>


