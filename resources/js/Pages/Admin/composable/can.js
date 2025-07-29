// resources/js/composables/can.js
import { usePage } from '@inertiajs/vue3'

export function useCan() {
  const page = usePage()

  const permissions = page.props.auth?.permissions || []
  const roles = page.props.auth?.roles || []

  /**
   * Check if user has a specific permission
   * @param {string} permission
   * @returns {boolean}
   */
  function can(permission) {
    return permissions.includes(permission)
  }

  /**
   * Check if user has at least one of the given permissions
   * @param {string[]} permissionList
   * @returns {boolean}
   */
  function canAny(permissionList = []) {
    return permissionList.some(p => permissions.includes(p))
  }

  /**
   * Check if user has a specific role
   * @param {string} role
   * @returns {boolean}
   */
  function hasRole(role) {
    return roles.includes(role)
  }

  /**
   * Check if user has at least one of the given roles
   * @param {string[]} roleList
   * @returns {boolean}
   */
  function hasAnyRole(roleList = []) {
    return roleList.some(r => roles.includes(r))
  }

  return {
    can,
    canAny,
    hasRole,
    hasAnyRole,
  }
}
