import dayjs from 'dayjs'
import 'dayjs/locale/en'
import 'dayjs/locale/km'
import { useI18n } from 'vue-i18n'

export function formatDateTime(dateString) {
  const { locale } = useI18n()

  // Set the locale for dayjs dynamically
  dayjs.locale(locale.value)

  // Add "ថ្ងៃ" (day) prefix only for Khmer
  const prefix = locale.value === 'km' ? 'ថ្ងៃ ' : ''

  // Format date
  return prefix + dayjs(dateString).format('dddd, DD MMMM YYYY HH:mm')

}