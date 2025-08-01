import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'
import utc from 'dayjs/plugin/utc'
import timezone from 'dayjs/plugin/timezone'

dayjs.extend(relativeTime)
dayjs.extend(utc)
dayjs.extend(timezone)

export function postTime(date) {
  // Parse date as UTC, convert to Asia/Phnom_Penh timezone, then compute fromNow
  return dayjs.utc(date).tz('Asia/Phnom_Penh').fromNow()
  
}
