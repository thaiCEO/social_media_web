<script setup>
import { usePage, Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import AdminLayout from '../Layout/AdminLayout.vue'
import { formatDateTime } from '../composable/formatDate'

const { t } = useI18n()

const props = defineProps({
  role: Object,
})

const page = usePage()
</script>

<template>
  <AdminLayout>
    <div class="w-full min-h-screen bg-white border-b border-gray-200 px-4 py-2.5 dark:bg-gray-800 dark:border-gray-700">
      
      <!-- Back Button -->
      <div class="mt-2">
        <Link
          :href="route('roles.index')"
          class="px-3 py-2 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
        >
          {{ t('roleShow.backToRoles') }}
        </Link>
      </div>

      <!-- Card -->
      <div class="max-w-4xl mx-auto mt-10 p-6 bg-white rounded-2xl shadow-lg dark:bg-gray-800">
        <div class="flex flex-col gap-4">
          <!-- Role Name -->
          <h1 class="text-3xl font-bold text-gray-800 dark:text-white">
            {{ role.name }}
          </h1>

        <!-- Permissions List -->
        <div>
          <h2 class="text-xl font-semibold mb-3 text-gray-700 dark:text-gray-300">
            {{ t('roleShow.permissions') }}
          </h2>

          <div
            v-if="role.permissions && role.permissions.length"
            class="grid grid-cols-4 gap-2"
          >
            <span
              v-for="permission in role.permissions"
              :key="permission.id"
              class="inline-block bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-100 rounded px-3 py-1 text-center"
            >
              {{ permission.name }}
            </span>
          </div>

          <p v-else class="text-gray-500 dark:text-gray-400">
            {{ t('roleShow.noPermissions') }}
          </p>
        </div>

          <!-- Created and Updated At -->
          <div class="text-gray-500 dark:text-gray-400 space-y-1">
            <p>{{ t('roleShow.createdAt') }}: {{ formatDateTime(role.created_at) }}</p>
            <p>{{ t('roleShow.updatedAt') }}: {{ formatDateTime(role.updated_at) }}</p>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
