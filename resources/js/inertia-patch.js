import { Inertia } from '@inertiajs/inertia'

// Originalfunktion patchen
const originalVisit = Inertia.visit

Inertia.visit = function (url, options = {}) {
    // Null-Werte aus Daten entfernen
    if (options.data) {
        options.data = Object.fromEntries(
            Object.entries(options.data).filter(([_, value]) => value !== null)
        )
    }
    
    return originalVisit(url, options)
}

export default Inertia