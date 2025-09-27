<template>
    <!-- zeigt Link erst, wenn Datum da ist -->
    <a v-if="createdAt" :href="`/${tablePath}?search=${createdAt}`">
      <slot></slot>
    </a>
    <span v-else>lädt …{{ data }}</span>
  </template>

  <script>
  import axios from 'axios'

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
      return { crea: {} }      // reaktiver State pro Instanz
    },

    computed: {
      /* Datum für gegebenes table + post_id */
      createdAt () {
        return this.crea[this.table]?.[this.post_id]?.created ?? null
      },

      /* Pfadlogik nur einmal definiert */
      tablePath () {
        const tbl = this.table
        if (tbl === 'images') return 'home/search_cat/pictures'
        if (tbl !== 'blogs')  return `home/${tbl}`
        return tbl
      }
    },

    created () {
      this.fetchCreatedAt()
    },

    methods: {
      async fetchCreatedAt () {
        /* 1) Daten sind schon gecacht → sofort zuweisen */
        if (creaCache) {
          this.crea = creaCache
          return
        }

        /* 2) Request läuft bereits → Ergebnis abwarten */
        if (fetchPromise) {
          this.crea = await fetchPromise
          return
        }

        /* 3) Erster Aufruf → echten Request starten */
        fetchPromise = axios.get('/api/created-at')
          .then(({ data }) => {
            if(!data){
                data = '';
            }
            const formatted = {}
            for (const [tbl, rowsRaw] of Object.entries(data)) {
              formatted[tbl] = {}
              ;(Array.isArray(rowsRaw) ? rowsRaw : Object.values(rowsRaw))
                .forEach(row => { formatted[tbl][row.post_id] = row })
            }
            creaCache = formatted      // global cachen
            return formatted
          })
          .catch(err => {
            console.error('Fehler beim Laden:', err)
            return {}
          })
          .finally(() => { fetchPromise = null })

        /*  Ergebnis in die reaktive Instanz kopieren  */
        this.crea = await fetchPromise
      }
    }
  }
  </script>
p
