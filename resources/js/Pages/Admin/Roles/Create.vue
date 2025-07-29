<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import Swal from 'sweetalert2'

const { t } = useI18n()

const emit = defineEmits(['close', 'created'])

const props = defineProps({
  permissions: Array // Pass permissions list from controller
})

const dialogVisible = ref(true)

const form = useForm({
  name: '',
  permissions: [], // selected permissions
})

function handleClose() {
  emit('close')
  form.reset()
}

function submit() {
  form.post(route('roles.store'), {
    preserveScroll: true,
    onSuccess: () => {
      Swal.fire({
        icon: 'success',
        title: t('alertSuccessRole.create'),
        text: t('alertSuccessRole.createText'),
        timer: 1500,
        showConfirmButton: false
      })
      emit('created')
      form.reset()
    },
  })
}
</script>

<template>
  <el-dialog :title="t('roleCreate.addRole')" v-model="dialogVisible" width="50%" :before-close="handleClose">
    <el-form label-position="top" :model="form">

      <!-- Role Name -->
      <el-form-item :label="t('roleCreate.name')" :error="$page.props.errors.name">
        <el-input v-model="form.name" placeholder="Enter role name" />
      </el-form-item>

      <!-- Permissions checklist -->
     <el-form-item :label="t('roleCreate.permissions')" :error="$page.props.errors.permissions">
        <div class="grid grid-cols-2 gap-2">
          <el-checkbox-group v-model="form.permissions">
            <div v-for="permission in props.permissions" :key="permission">
              <el-checkbox :label="permission">
                {{ permission }}
              </el-checkbox>
            </div>
          </el-checkbox-group>
        </div>
      </el-form-item>


    </el-form>

    <template #footer>
      <el-button @click="handleClose">{{ t('roleCreate.cancel') }}</el-button>
      <el-button type="primary" :loading="form.processing" @click="submit">{{ t('roleCreate.submit') }}</el-button>
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
