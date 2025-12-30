<template>
    <!-- zeigt Link erst, wenn Datum da ist -->
    <a v-if="createdAt" :href="`/${tablePath}?search=${createdAt}`">
      <slot></slot>
    </a>
    <span v-else>lädt …{{ data }}</span>
  </template>

<script>
import axios from 'axios';
import {CleanTable} from "@/helpers";
/* --------  Modulweiter Cache  -------- */
let creaCache    = null   // fertige Daten
let fetchPromise = null   // laufender Request

export default {
  name: 'CreatedAtProvider',

  props: {
    table:   { type: String, required: true },
    post_id: { type: Number, required: true }
  },

  data () {
    return { crea: {} } // reaktiver State pro Instanz
  },

  computed: {
    /* Datum für gegebenes table + post_id */
    createdAt () {
      return this.crea[this.table]?.[this.post_id]?.created ?? null
    },

    /* Pfadlogik */
    tablePath () {
      const tbl = this.table;
       if (!tbl) return '';
      if (tbl === 'images') return 'home/search_cat/pictures';
      if (tbl !== 'blogs')  return `home/${tbl}`;
      return tbl
    }
  },

  created () {
    this.fetchCreatedAt()
  },

  watch: {
    table: 'fetchCreatedAt',
    post_id: 'fetchCreatedAt'
  },

  methods: {
    CleanTable,
    async fetchCreatedAt () {
      // 1) Cache vorhanden → direkt verwenden
      if (creaCache) {
        this.crea = creaCache
        return
      }

      // 2) Request läuft bereits → abwarten
      if (fetchPromise) {
        this.crea = await fetchPromise
        return
      }

      // 3) Neuer Request
      fetchPromise = axios.get('/api/created-at')
        .then(({ data }) => {
          if (!data) return {}

          //console.log('API-Daten:', JSON.stringify(data, null, 2)) // Debug-Ausgabe

          const formatted = {}

          for (const [tbl, rowsRaw] of Object.entries(data)) {
            formatted[tbl] = {}

            const rows = Array.isArray(rowsRaw)
              ? rowsRaw
              : Object.values(rowsRaw)

            rows.forEach(row => {
              const id = row.post_id ?? row.id ?? null
              if (!id) return

              formatted[tbl][id] = {
                post_id: id,
                created: row.created ?? row.created_at ?? null
              }
            })
          }

          creaCache = formatted
          return formatted
        })
        .catch(err => {
          console.error('Fehler beim Laden:', err)
          return {}
        })
        .finally(() => { fetchPromise = null })

      // Ergebnis reaktiv zuweisen
      this.crea = await fetchPromise
    }
  }
}
</script>



