<script setup>
import { computed, onMounted } from 'vue'
import { initFlowbite } from 'flowbite'
import AdminLayout from './Layout/AdminLayout.vue'
import { usePage } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'
import { Bar } from 'vue-chartjs'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

onMounted(() => initFlowbite())

const { t, locale } = useI18n()
const page = usePage()
const stats = page.props.stats

// 👇 Receive props from Laravel (Inertia)
const props = defineProps({
  statsCount: Object,
  stats: Object
})

// Choose longest labels array
const labels = computed(() => {
  const dailyLen = stats.daily.labels.length
  const monthlyLen = stats.monthly.labels.length
  const yearlyLen = stats.yearly.labels.length

  if (dailyLen >= monthlyLen && dailyLen >= yearlyLen) return stats.daily.labels
  if (monthlyLen >= dailyLen && monthlyLen >= yearlyLen) return stats.monthly.labels
  return stats.yearly.labels
})

function padData(data, length) {
  if (data.length >= length) return data.slice(0, length)
  return [...data, ...Array(length - data.length).fill(null)]
}

const chartData = computed(() => {
  const commonLabels = labels.value
  const length = commonLabels.length
  return {
    labels: commonLabels,
    datasets: [
      {
        label: t('chart.daily'),
        backgroundColor: '#3b82f6',
        data: padData(stats.daily.data, length),
      },
      {
        label: t('chart.monthly'),
        backgroundColor: '#10b981',
        data: padData(stats.monthly.data, length),
      },
      {
        label: t('chart.yearly'),
        backgroundColor: '#f59e0b',
        data: padData(stats.yearly.data, length),
      },
    ],
  }
})

const chartTitle = computed(() =>
  locale.value === 'km' ? t('chart.user_registration_km') : t('chart.user_registration_en')
)

const chartOptions = computed(() => ({
  responsive: true,
  plugins: {
    legend: {
      position: 'top',
      labels: {
        font: {
          family: locale.value === 'km' ? 'Battambang' : 'system-ui',
          weight: '400',
          size: 14,
        },
      },
    },
    title: {
      display: true,
      text: chartTitle.value,
      font: {
        family: locale.value === 'km' ? 'Battambang' : 'system-ui',
        weight: '400',
        size: 18,
      },
    },
  },
}))
</script>


<template>
  <AdminLayout>
    <!-- Summary Boxes -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
      <div class="p-6 min-h-[150px] bg-white dark:bg-gray-800 rounded-2xl shadow flex flex-col justify-center">
        <h2 class="text-gray-500 dark:text-gray-300 text-base mb-2">{{ t('dashboard.admin') }}</h2>
        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ statsCount.admins }}</p>
      </div>
      <div class="p-6 min-h-[150px] bg-white dark:bg-gray-800 rounded-2xl shadow flex flex-col justify-center">
        <h2 class="text-gray-500 dark:text-gray-300 text-base mb-2">{{ t('dashboard.moderators') }}</h2>
        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ statsCount.moderators }}</p>
      </div>
      <div class="p-6 min-h-[150px] bg-white dark:bg-gray-800 rounded-2xl shadow flex flex-col justify-center">
        <h2 class="text-gray-500 dark:text-gray-300 text-base mb-2">{{ t('dashboard.posts') }}</h2>
        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ statsCount.posts }}</p>
      </div>
      <div class="p-6 min-h-[150px] bg-white dark:bg-gray-800 rounded-2xl shadow flex flex-col justify-center">
        <h2 class="text-gray-500 dark:text-gray-300 text-base mb-2">{{ t('dashboard.users') }}</h2>
        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ statsCount.users }}</p>
      </div>
    </section>

    <!-- Full-Width Chart -->
    <section class="grid grid-cols-1 md:w-full lg:grid-cols-12 gap-6 mt-6 dark:bg-gray-800">
      <div class="lg:col-span-12 bg-white rounded-2xl shadow p-6 dark:bg-gray-800">
        <Bar :data="chartData" :options="chartOptions" />
      </div>
    </section>
  </AdminLayout>
</template>

