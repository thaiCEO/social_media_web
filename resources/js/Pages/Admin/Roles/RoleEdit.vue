<script setup>
import { ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import Swal from 'sweetalert2'

const { t } = useI18n()


const emit = defineEmits(['close', 'updated'])

// Props from parent
const props = defineProps({
  role: { type: Object, required: true },        // Selected role {id, name, permissions: []}
  permissions: { type: Array, required: true }   // All available permissions as array of strings
})

const dialogVisible = ref(true)

// Initial form state
const form = useForm({
  name: props.role?.name || '',
  permissions: props.role?.permissions
    ? props.role.permissions.map(p => p.name)
    : []
})

// Reset form values when prop changes
watch(
  () => props.role,
  (newRole) => {
    if (newRole) {
      form.name = newRole.name
      form.permissions = newRole.permissions.map(p => p.name)
    }
  },
  { deep: true }
)

function handleClose() {
  emit('close')
  form.reset()
}

function submit() {
  form.put(route('roles.update', props.role.id), {
    preserveScroll: true,
    onSuccess: () => {
      Swal.fire({
       icon: 'success',
        title: t('alertSuccessRole.update'),
        text: t('alertSuccessRole.updateText'),
        timer: 1500,
        showConfirmButton: false
      })
      emit('updated')
      form.reset()
    },
  })
}
</script>

<template>
  <el-dialog
    :title="t('roleCreate.editRole')"
    v-model="dialogVisible"
    width="50%"
    :before-close="handleClose"
  >
    <el-form label-position="top" :model="form">
      <!-- Role Name -->
      <el-form-item :label="t('roleCreate.name')" :error="$page.props.errors.name">
        <el-input v-model="form.name" placeholder="Enter role name" />
      </el-form-item>

      <!-- Permissions checklist -->
      <el-form-item :label="t('roleCreate.permissions')" :error="$page.props.errors.permissions">
        <div class="grid grid-cols-2 gap-2">
          <el-checkbox-group v-model="form.permissions">
            <div
              v-for="permission in permissions"
              :key="permission"
              class="flex items-center"
            >
              <el-checkbox :label="permission">{{ permission }}</el-checkbox>
            </div>
          </el-checkbox-group>
        </div>
      </el-form-item>
    </el-form>

    <template #footer>
      <el-button @click="handleClose">{{ t('roleCreate.cancel') }}</el-button>
      <el-button type="primary" :loading="form.processing" @click="submit">
        {{ t('roleCreate.submit') }}
      </el-button>
    </template>
  </el-dialog>
</template>

<style scoped>
/* Style for Element Plus inputs inside this modal */
:deep(.el-input__wrapper.is-focus) {
  box-shadow: none !important;  /* remove the glowing effect */
  border: 1px solid #dcdfe6 !important; /* keep normal border */
}

:deep(.el-input__wrapper) {
  border-radius: 6px;
}

/* Remove native outline for inner input */
:deep(.el-input__inner) {
  outline: none !important;
  box-shadow: none !important;
}
</style>
