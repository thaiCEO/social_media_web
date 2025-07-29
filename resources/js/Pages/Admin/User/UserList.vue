<script setup>
import { ref } from "vue";
import { Link, router } from "@inertiajs/vue3";
import Swal from 'sweetalert2';
import UserCreate from "./Create.vue";
import UserEdit from "./UserEdit.vue";
import { formatDateTime } from '../composable/formatDate.js'
import Paginator from "../components/Paginator.vue";

import { useI18n } from 'vue-i18n'

import { useCan } from "../composable/can";

const { t } = useI18n()

const { can } = useCan()


defineProps({
    users: Object,
    roles: Array,
})

// Dialog state
const createDialogVisible = ref(false)
const editDialogVisible = ref(false)

const selectedUser = ref(null)

function openAddModal() {
  createDialogVisible.value = true
}

function onEdit(user) {
  selectedUser.value = user
  editDialogVisible.value = true
}

function deleteUser(id) {
  Swal.fire({
    title: t('userAlert.confirmDeleteTitle'),
    text: t('userAlert.confirmDeleteText'),
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: t('userAlert.confirmButton'),
    cancelButtonText: t('userAlert.cancelButton'),
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('user-post.destroy', id), {
        preserveScroll: true,
        onSuccess: () => {
          Swal.fire({
            icon: 'success',
            title: t('userAlert.deletedTitle'),
            text: t('userAlert.deletedText'),
            timer: 1500,
            showConfirmButton: false
          });
        },
        onError: () => {
          Swal.fire({
            icon: 'error',
            title: t('userAlert.errorTitle'),
            text: t('userAlert.errorText'),
          });
        }
      });
    }
  });
}

// delete multiple users by select
const selectedUserIds = ref([])

function toggleSelection(userId) {
  const index = selectedUserIds.value.indexOf(userId)
  if (index > -1) {
    selectedUserIds.value.splice(index, 1)
  } else {
    selectedUserIds.value.push(userId)
  }
}

function isSelected(userId) {
  return selectedUserIds.value.includes(userId)
}

function deleteSelected() {
  Swal.fire({
    title: t('userAlert.confirmSelectedDeleteTitle'),
    text: t('userAlert.confirmSelectedDeleteText'),
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: t('userAlert.confirmSelectedButton'),
    cancelButtonText: t('userAlert.cancelSelectedButton'),
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(route('users.select-delete'), { // adjust your route
        user_ids: selectedUserIds.value,
      }, {
        preserveScroll: true,
        onSuccess: () => {
          selectedUserIds.value = []
          Swal.fire({
            title: t('userAlert.deletedSelectedTitle'),
            text: t('userAlert.deletedSelectedText'),
            icon: 'success',
            confirmButtonText: t('userAlert.okButton'),
          })
        },
      })
    }
  })
}
</script>

<template>
  <!-- Create user Modal -->
  <UserCreate
    v-if="createDialogVisible"
    :roles="roles"
    @close="createDialogVisible = false"
    @created="createDialogVisible = false"
  />

  <!-- Edit user Modal -->
  <UserEdit
    v-if="editDialogVisible"
    :user="selectedUser"
    :roles="roles"
    @close="editDialogVisible = false"
    @updated="editDialogVisible = false"
  />

  <section class="bg-gray-50 dark:bg-gray-900">
    <div class="mx-0 max-w-screen-2xl">
      <div class="bg-white dark:bg-gray-800 relative shadow-md">
        <div
          class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4"
        >
          <div class="w-full md:w-1/2"></div>
          <div
            class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0"
          >
            <el-button
              v-if="can('users.create')"
              type="primary"
              class="flex items-center justify-center text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
              @click="openAddModal"
            >
              <svg
                class="h-3.5 w-3.5 mr-2"
                fill="currentColor"
                viewBox="0 0 20 20"
                xmlns="http://www.w3.org/2000/svg"
                aria-hidden="true"
              >
                <path
                  clip-rule="evenodd"
                  fill-rule="evenodd"
                  d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                />
              </svg>
              {{ t('userList.addUser') }}
            </el-button>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead
              class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
            >
              <tr>
                <th scope="col" class="px-4 py-3">@</th>
                <th scope="col" class="px-4 py-3">{{ t('userList.userName') }}</th>
                <th scope="col" class="px-3 py-3 ml-4">{{ t('userList.createdAt') }}</th>
                <th scope="col" class="px-3 py-3 ml-4">{{ t('userList.updatedAt') }}</th>
                <th scope="col" class="px-3 py-3">
                  <span class="sr-only">{{ t('userList.actions') }}</span>
                </th>
              </tr>
            </thead>
            <tbody>
              <tr
                class="border-b dark:border-gray-700"
                v-for="user in users.data"
                :key="user.id"
              >
                <td class="w-4 px-4 py-3">
                  <div class="flex items-center">
                    <input
                      type="checkbox"
                      :value="user.id"
                      :checked="isSelected(user.id)"
                      @change="toggleSelection(user.id)"
                      class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                    />
                  </div>
                </td>

                <th
                  scope="row"
                  class="flex items-center px-4 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                >
                  <img
                    :src="`/storage/${user.image}`"
                    alt="User Image"
                    class="w-auto h-8 mr-3 rounded"
                  />
                  {{ user.name }}
                </th>
              
                <td class="px-4 py-3">{{ formatDateTime(user.created_at) }}</td>
                <td class="px-4 py-3">{{ formatDateTime(user.updated_at) }}</td>
               <td class="px-4 py-3 flex items-center justify-end space-x-4">
  
                  <Link
                     v-if="can('users.view')"
                    :href="route('user-post.show', user.id)"
                    class="block py-1.5 px-4 bg-blue-600 text-white rounded hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600"
                  >
                    {{ t('userList.show') }}
                  </Link>

                    <button
                      v-if="can('users.edit')"
                      type="button"
                      @click.prevent="onEdit(user)"
                      class="block py-1.5 px-4 bg-gray-600 text-white rounded hover:bg-gray-700 dark:bg-blue-500 dark:hover:bg-gray-600"
                    >
                      {{ t('userList.edit') }}
                    </button>


                  <div class="py-1">
                    <button
                      v-if="can('users.delete')"
                      @click.prevent="deleteUser(user.id)"
                      class="block py-1.5 px-4 text-white bg-red-600 rounded hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600"
                    >
                      {{ t('userList.delete') }}
                    </button>
                  </div>

                </td>

              </tr>
            </tbody>
          </table>

          <!-- delete by selected -->
          <div class="mt-4 px-3 w-full flex justify-end" v-if="selectedUserIds.length > 0">
            <el-button
              v-if="can('users.delete')"
              type="danger"
              :disabled="selectedUserIds.length === 0"
              @click="deleteSelected"
            >
              {{ t('userList.deleteSelected') }}
            </el-button>
          </div>
          <!-- delete by selected -->
        </div>

        <!-- Pagination -->
        <Paginator :paginator="users" />
      </div>
    </div>
  </section>
</template>

<style scoped></style>
