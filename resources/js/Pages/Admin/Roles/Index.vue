<template>
   <AdminLayout>

         <!-- Your content here -->
       <RoleList  :roles="roles" :permissions="permissions" />
       
   </AdminLayout>
</template>

<script setup>
import { watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

import RoleList from './RoleList.vue';
import AdminLayout from '../Layout/AdminLayout.vue';


defineProps({
    roles:Object,
    permissions:Object,
})



// Watch for changes in flash success
// watch(
//   () => page.props.flash.success,
//   (message) => {
//     if (message) {
//       Swal.fire({
//         icon: 'success',
//         title: t('brandAlert'),
//         text: message,
//         confirmButtonText: t('brandAlert'),
//       });
//     }
//   },
//   { immediate: true } 
// );
</script>

<style>

</style>