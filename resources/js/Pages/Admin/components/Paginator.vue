<script setup>
import { useI18n } from 'vue-i18n'
import { computed } from 'vue'

const { t } = useI18n()

const props = defineProps({
  paginator: Object
})

const visibleLinks = computed(() => {
  if (!props.paginator) return []

  const all = props.paginator.links
  const maxPagesToShow = 9

  const prev = all[0]
  const next = all[all.length - 1]

  // Extract page number links (exclude prev/next)
  const numbered = all.slice(1, all.length - 1)

  let display = numbered

  if (numbered.length > maxPagesToShow) {
    display = numbered.slice(0, maxPagesToShow)
    display.push({ label: '...', url: null })
  }

  return [prev, ...display, next]
})
</script>


<template>
  <nav
    class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-3 md:space-y-0 p-4"
    aria-label="Table navigation"
    v-if="paginator && paginator.links.length > 0"
  >
    <!-- Result Summary -->
    <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
      {{ t('pagination.showing') }}
      <span class="font-semibold text-gray-900 dark:text-white">{{ paginator.from }}</span>
      {{ t('pagination.to') }}
      <span class="font-semibold text-gray-900 dark:text-white">{{ paginator.to }}</span>
      {{ t('pagination.of') }}
      <span class="font-semibold text-gray-900 dark:text-white">{{ paginator.total }}</span>
      {{ t('pagination.results') }}
    </span>

    <!-- Pagination Links -->
    <ul class="inline-flex items-stretch -space-x-px">
      <li v-for="(link, index) in visibleLinks" :key="index">
        <button
          v-if="link.url"
          @click="$inertia.visit(link.url)"
          :aria-current="link.active ? 'page' : null"
          :class="[
            'flex items-center justify-center text-sm py-2 px-3 leading-tight border',
            index === 0 ? 'rounded-l-lg' : index === visibleLinks.length - 1 ? 'rounded-r-lg' : '',
            link.active
              ? 'z-10 text-blue-600 bg-blue-100 border-blue-300 hover:bg-blue-200 hover:text-blue-700 dark:border-gray-700 dark:bg-gray-700 dark:text-white'
              : 'text-gray-500 bg-white border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white'
          ]"
        >
          <span v-if="link.label.includes('Previous')">{{ t('pagination.prev') }}</span>
          <span v-else-if="link.label.includes('Next')">{{ t('pagination.next') }}</span>
          <span v-else v-html="link.label" />
        </button>

        <span
          v-else
          class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-400 bg-gray-100 border border-gray-300 dark:border-gray-700 dark:bg-gray-700"
        >
          <span v-if="link.label.includes('Previous')">{{ t('pagination.prev') }}</span>
          <span v-else-if="link.label.includes('Next')">{{ t('pagination.next') }}</span>
          <span v-else v-html="link.label" />
        </span>
      </li>
    </ul>
  </nav>
</template>


