<!-- resources/js/Components/Charts/Chart.vue -->
<script setup>
import { Bar } from 'vue-chartjs'
import {
  Chart as ChartJS, Title, Tooltip, Legend,
  CategoryScale, LinearScale, BarElement
} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, CategoryScale, LinearScale, BarElement)

const props = defineProps({
  chartData: Object,
  chartOptions: Object,
})
</script>

<template>
   <div class="w-full mt-6">
      <Bar :data="chartData" :options="chartOptions" />
   </div>
</template>
