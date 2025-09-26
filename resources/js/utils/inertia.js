	import { Inertia } from '@inertiajs/inertia'

/**
 * Wrapper für Inertia-Requests, der null/undefined automatisch entfernt.
 *
 * @param {String} url - Ziel-Route
 * @param {Object} data - Query-/Formdaten
 * @param {Object} options - Inertia Optionen (preserveState, replace usw.)
 */
export function safeInertiaGet(url, data = {}, options = {}) {
  let cleanData = {}

  Object.keys(data).forEach(key => {
    if (data[key] !== null && data[key] !== undefined) {
      cleanData[key] = data[key]
    }
  })

  return Inertia.get(url, cleanData, options)
}

export function safeInertiaVisit(url, data = {}, options = {}) {
  let cleanData = {}

  Object.keys(data).forEach(key => {
    if (data[key] !== null && data[key] !== undefined) {
      cleanData[key] = data[key]
    }
  })

  return Inertia.visit(url, { ...options, data: cleanData })
}
