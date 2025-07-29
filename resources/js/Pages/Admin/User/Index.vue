<template>
   <AdminLayout>

         <!-- Your content here -->
       <BrandList  :users="users" :roles="roles"/>
       
   </AdminLayout>
</template>

<script setup>
import { watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

import BrandList from './UserList.vue';
import AdminLayout from '../Layout/AdminLayout.vue';


defineProps({
    users:Object,
    roles: Array,
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