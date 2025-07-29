<!-- resources/js/Components/Charts/PieChart.vue -->
<script setup>
import { Pie } from 'vue-chartjs'
import {
  Chart as ChartJS, Title, Tooltip, Legend, ArcElement
} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, ArcElement)

const props = defineProps({
  chartData: Object,
  chartOptions: Object
})
</script>

<template>
  <div class="w-full mt-6">
     <Pie :data="chartData" :options="chartOptions" />
  </div>
</template>
