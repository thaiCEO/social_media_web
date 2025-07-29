<script setup>
import { ref, onMounted } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import Swal from 'sweetalert2'

const { t } = useI18n()

const emit = defineEmits(['close', 'updated'])

const props = defineProps({
  user: Object,
  roles: Array,
})

// Modal visibility
const dialogVisible = ref(true)
const fileList = ref([])

// Form state
const form = useForm({
  id: props.user.id,
  name: props.user.name,
  email: props.user.email,
  password: '', 
  roles:  props.user.roles || [] ,
  image: null,
  _method: 'PUT',
})

// Show existing image if available
onMounted(() => {
  if (props.user.image) {
    fileList.value = [
      {
        name: 'Existing Image',
        url: `/storage/${props.user.image}`,
      },
    ]
  }
})

// Close handler
function handleClose() {
  emit('close')
  form.reset()
  fileList.value = []
}

// When file changes
function handleFileChange(file, fileListRef) {
  fileList.value = fileListRef

  if (file?.raw) {
    form.image = file.raw
  } else {
    form.image = null
  }
}

// Submit update
function submit() {
  form.post(route('user-post.update', form.id), {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => {
      Swal.fire({
        icon: 'success',
        title: t('userPostAlert.successUpdateTitle'),
        confirmButtonText: t('userPostAlert.successButton'),
      })
      emit('updated')
      form.reset()
      fileList.value = []
    },
  })
}
</script>


<template>
  <el-dialog :title="t('userPostCreate.editUser')" v-model="dialogVisible" width="50%" :before-close="handleClose">
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
