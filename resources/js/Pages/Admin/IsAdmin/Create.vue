<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import Swal from 'sweetalert2'
const { t } = useI18n()

const emit = defineEmits(['close', 'created'])

// Modal visibility state
const dialogVisible = ref(true)

const fileList = ref([])


defineProps({
  roles:Array
})

const form = useForm({
  name: '',
  email: '',
  password: '',
  image: '',
  roles: [],
  is_Admin: false,
})

function handleClose() {
  emit('close')
  form.reset()
  fileList.value = []
}

function handleFileChange(file, fileListRef) {
  fileList.value = fileListRef
  form.image = fileListRef[0]?.raw || null
}

function submit() {
  form.post(route('user-post.store'), {
    preserveScroll: true,
    forceFormData: true,
    onSuccess: () => {
      Swal.fire({
        icon: 'success',
        title: t('userPostAlert.successCreateTitle'),
        confirmButtonText: t('userPostAlert.successButton'),
      })
      emit('created')
      form.reset()
      fileList.value = []
    },
  })
}
</script>

<template>
  <el-dialog :title="t('userPostCreate.addUser')" v-model="dialogVisible" width="50%" :before-close="handleClose">
    <el-form label-position="top" :model="form">

      <!-- Name -->
      <el-form-item :label="t('userPostCreate.name')" :error="$page.props.errors.name">
        <el-input v-model="form.name" />
      </el-form-item>

      <!-- Email -->
      <el-form-item :label="t('userPostCreate.email')" :error="$page.props.errors.email">
        <el-input v-model="form.email" />
      </el-form-item>

      <!-- Password -->
      <el-form-item :label="t('userPostCreate.password')" :error="$page.props.errors.password">
        <el-input v-model="form.password" type="password" />
      </el-form-item>


       <el-form-item :label="t('roleCreate.permissions')" :error="$page.props.errors.roles">
        <div class="grid grid-cols-2 gap-2">
          <el-checkbox-group v-model="form.roles">
            <div v-for="role in roles" :key="role">
              <el-checkbox :label="role">
                {{ role }}
              </el-checkbox>
            </div>
          </el-checkbox-group>
        </div>
      </el-form-item>


     <!-- Is Admin -->
     <el-form-item :label="t('IsAdminCreate.isAdmin')" :error="$page.props.errors.is_Admin">
        <el-switch
          v-model="form.is_Admin"
          :active-text="t('IsAdminCreate.active')"
          :inactive-text="t('IsAdminCreate.inactive')"
        />
      </el-form-item>


      <!-- Upload image -->
      <el-form-item :label="t('userPostCreate.userImage')" :error="$page.props.errors['image']">
        <el-upload
          drag
          accept="image/*"
          :auto-upload="false"
          :on-change="handleFileChange"
          :file-list="fileList"
          list-type="picture-card"
          :limit="1"
        >
          <template #default>
            <div class="upload-box">
              <div class="upload-text">
                <p>{{ t('userPostCreate.uploadText') }}</p>
                <small>{{ t('userPostCreate.uploadNote') }}</small>
              </div>
            </div>
          </template>
        </el-upload>
      </el-form-item>

    </el-form>

    <template #footer>
      <el-button @click="handleClose">{{ t('userPostCreate.cancel') }}</el-button>
      <el-button type="primary" :loading="form.processing" @click="submit">{{ t('userPostCreate.submit') }}</el-button>
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
