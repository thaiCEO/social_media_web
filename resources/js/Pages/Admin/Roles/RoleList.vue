<script setup>
import { ref } from "vue";
import { Link, router } from "@inertiajs/vue3";
import Swal from 'sweetalert2';
import RoleCreate from "./Create.vue";
import RoleEdit from "./RoleEdit.vue";
import { formatDateTime } from '../composable/formatDate.js'
import Paginator from "../components/Paginator.vue";
import { useI18n } from 'vue-i18n'
const { t } = useI18n()

defineProps({
    roles: Object,
    permissions:Object,
})

// Dialog state
const createDialogVisible = ref(false)
const editDialogVisible = ref(false)

const selectedUser = ref(null)

function openAddModal() {
  createDialogVisible.value = true
}

function onEdit(role) {
  selectedUser.value = role
  editDialogVisible.value = true
}

// Delete single role
function deleteRole(id) {
  Swal.fire({
    title: t('roleAlert.confirmDeleteTitle'),
    text: t('roleAlert.confirmDeleteText'),
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: t('roleAlert.confirmButton'),
    cancelButtonText: t('roleAlert.cancelButton'),
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('roles.destroy', id), {
        preserveScroll: true,
        onSuccess: () => {
          Swal.fire({
            icon: 'success',
            title: t('roleAlert.deletedTitle'),
            text: t('roleAlert.deletedText'),
            timer: 1500,
            showConfirmButton: false
          });
        },
        onError: () => {
          Swal.fire({
            icon: 'error',
            title: t('roleAlert.errorTitle'),
            text: t('roleAlert.errorText'),
          });
        }
      });
    }
  });
}

// Multiple delete
const selectedRoleIds = ref([])

function toggleSelection(RoleId) {
  const index = selectedRoleIds.value.indexOf(RoleId)
  if (index > -1) {
    selectedRoleIds.value.splice(index, 1)
  } else {
    selectedRoleIds.value.push(RoleId)
  }
}

function isSelected(RoleId) {
  return selectedRoleIds.value.includes(RoleId)
}

function deleteSelected() {
  Swal.fire({
    title: t('roleAlert.confirmSelectedDeleteTitle'),
    text: t('roleAlert.confirmSelectedDeleteText'),
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: t('roleAlert.confirmSelectedButton'),
    cancelButtonText: t('roleAlert.cancelSelectedButton'),
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(route('roles.select-delete'), {
        role_ids: selectedRoleIds.value,
      }, {
        preserveScroll: true,
        onSuccess: () => {
          selectedRoleIds.value = []
          Swal.fire({
            title: t('roleAlert.deletedSelectedTitle'),
            text: t('roleAlert.deletedSelectedText'),
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
          })
        },
      })
    }
  })
}
</script>

<template>
  <!-- Create Role Modal -->
  <RoleCreate
    v-if="createDialogVisible"
    :permissions="permissions"
    @close="createDialogVisible = false"
    @created="createDialogVisible = false"
  />

  <!-- Edit Role Modal -->
  <RoleEdit
    v-if="editDialogVisible"
    :role="selectedUser"
    :permissions="permissions"
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
              {{ t('roleList.addRole') }}
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
      <th scope="col" class="px-4 py-3">{{ t('roleList.roleName') }}</th>
      <th scope="col" class="px-3 py-3">{{ t('roleList.createdAt') }}</th>
      <th scope="col" class="px-3 py-3">{{ t('roleList.updatedAt') }}</th>
      <th scope="col" class="px-3 py-3">
        <span class="sr-only">{{ t('roleList.actions') }}</span>
      </th>
    </tr>
  </thead>

  <tbody>
    <tr
      class="border-b dark:border-gray-700"
      v-for="role in roles"
      :key="role.id"
    >
      <td class="w-4 px-4 py-3">
        <div class="flex items-center">
          <input
            type="checkbox"
            :value="role.id"
            :checked="isSelected(role.id)"
            @change="toggleSelection(role.id)"
            class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
          />
        </div>
      </td>

      <th
        scope="row"
        class="flex items-center px-4 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white"
      >
        {{ role.name }}
      </th>

    
      <td class="px-4 py-3">{{ formatDateTime(role.created_at) }}</td>
      <td class="px-4 py-3">{{ formatDateTime(role.updated_at) }}</td>

      <td class="px-4 py-3 flex items-center justify-end space-x-4">

        <Link
          :href="route('roles.show', role.id)"
          class="block py-1.5 px-4 bg-blue-600 text-white rounded hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600"
        >
          {{ t('roleList.show') }}
        </Link>


        <button
          type="button"
          @click.prevent="onEdit(role)"
          class="block py-1.5 px-4 bg-gray-600 text-white rounded hover:bg-gray-700 dark:bg-blue-500 dark:hover:bg-gray-600"
        >
          {{ t('roleList.edit') }}
        </button>


        <div class="py-1">
          <button
            @click.prevent="deleteRole(role.id)"
            class="block py-1.5 px-4 text-white bg-red-600 rounded hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600"
          >
            {{ t('roleList.delete') }}
          </button>
        </div>
      </td>
    </tr>
  </tbody>
</table>


          <!-- delete by selected -->
          <div class="mt-4 px-3 w-full flex justify-end" v-if="selectedRoleIds.length > 0">
            <el-button
              type="danger"
              :disabled="selectedRoleIds.length === 0"
              @click="deleteSelected"
            >
              {{ t('userList.deleteSelected') }}
            </el-button>
          </div>
          <!-- delete by selected -->
        </div>

        <!-- Pagination -->
        <!-- <Paginator :paginator="users" /> -->
      </div>
    </div>
  </section>
</template>

<style scoped></style>
