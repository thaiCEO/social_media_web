<script setup>
import { watch } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { useI18n } from 'vue-i18n';
import AdminLayout from '../Layout/AdminLayout.vue';
import { formatDateTime } from '../composable/formatDate';
const { t } = useI18n();

defineProps({
  user: Object,
});

const page = usePage();

// Show flash messages
// watch(
//   () => page.props.flash.success,
//   (message) => {
//     if (message) {
//       Swal.fire({
//         icon: 'success',
//         title: t('userPostAlert.successTitle'),
//         text: message,
//         confirmButtonText: t('userPostAlert.successButton'),
//       });
//     }
//   },
//   { immediate: true }
// );
</script>

<template>
  <AdminLayout>
    <div class="w-full min-h-screen bg-white border-b border-gray-200 px-4 py-2.5 dark:bg-gray-800 dark:border-gray-700">
      <!-- Back Button -->
      <div class="mt-2">
        <Link
          :href="route('user-post.index')"
          type="button"
          class="px-3 py-2 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
        >
          {{ t('userPostShow.backToUser') }}
        </Link>
      </div>

      <!-- Card -->
      <div
        class="max-w-4xl mx-auto mt-10 p-6 bg-white rounded-2xl shadow-lg dark:bg-gray-800"
      >
        <div class="flex flex-col md:flex-row items-center gap-6">
          <!-- User Image -->
          <div
            class="w-32 h-32 flex-shrink-0 overflow-hidden rounded-full border border-gray-300 dark:border-gray-700"
          >
            <img
              :src="
                user.image
                  ? `/storage/${user.image}`
                  : 'https://png.pngtree.com/png-vector/20190820/ourmid/pngtree-no-image-vector-illustration-isolated-png-image_1694547.jpg'
              "
              alt="User Image"
              class="w-full h-full object-cover"
            />
          </div>

          <!-- User Info -->
          <div class="flex flex-col gap-2">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
              {{ user.name }}
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-300">
             {{ t('showInfoUserPost.Email') }}: {{ user.email }}
            </p>
            <p class="text-sm text-gray-500 dark:text-gray-300">
                {{ t('showInfoUserPost.CreatedAt') }}: {{ formatDateTime(user.created_at) }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
