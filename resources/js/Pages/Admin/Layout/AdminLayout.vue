<script setup>
import { onMounted } from 'vue'
import { initFlowbite } from 'flowbite'
import Navbar from '../components/Navbar.vue';
import Sidebar from '../components/Sidebar.vue';


// initialize components based on data attribute selectors
onMounted(() => {
    initFlowbite();
})
</script>

<template>
    <div class="antialiased bg-gray-50 dark:bg-gray-900">
   
    <Navbar/>
    <!-- Sidebar -->

    <Sidebar/>

     <main class="p-4 md:ml-64 pt-20 space-y-6">

        <slot/>
        
    </main>
    
  </div>
</template>
