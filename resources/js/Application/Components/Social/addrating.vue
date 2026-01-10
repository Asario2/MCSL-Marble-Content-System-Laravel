<template>
    <div class="rating-container w300 relative border border-gray-300 p-4 pt-1 rounded-lg shadow-sm bg-white dark:bg-gray-800">
    <div class="stars">
    <span
        v-for="star in 5"
        :key="star"
        class="star"
        :class="{'filled': star <= rating, 'hovered': star <= hoverRating}"
        @mouseover="hoverRating = star"
        @mouseleave="hoverRating = 0"
        @click="setRating(star)"
    >
        <icon-star class="inline-block" wi="24" he="24" />
    </span>
    </div>
      <!-- Text anzeigen, dass die Bewertung gesetzt wurde -->
      <p v-if="rating > 0">Du hast {{ rating }} Sterne bewertet</p>
    </div>
  </template>

 <script>
  import axios from "axios";
  import { CleanTable, checkAuthAndRedirect,GetAuth } from '@/helpers';
  import { toastBus } from '@/utils/toastBus';
  import {route} from 'ziggy-js';
  import IconStar from "@/Application/Components/Icons/IconStar.vue";
  export default {
    components: {

    IconStar,

    },
    props: {
      postId: Number, // Post ID wird als Prop übergeben
      table: String,  // Table-Name wird als Prop übergeben
    },
    data() {
      return {
        rating: 0, // Bewertung aus der DB
        hoverRating: 0, // Hover-Effekt
      };
    },
    methods: {
      // Bewertung setzen und speichern
      async setRating(star) {
        this.rating = star;
        await this.saveRating(star);
      },

      // Bewertung in die Datenbank speichern
      async saveRating(star) {
        try {
             if(GetAuth() !== true)
             {

                // location.href=route("login");
                // return;
             }

            if (await checkAuthAndRedirect() === "login") {

                // location.href = route('login');
                // return;
            } else {
                const table = CleanTable();
                await axios.post("/save-rating", {
                    rating: star,
                    postId: this.postId,
                    table: table,
                    _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                });
                //window.location.hash = '#st' + this.postId;

            }
            window.toastBus.emit( {
                    status: 'points',          // success, info, warning, error
                    message: 'Du hast 1 MCSL Point gesammelt'
                });
                // window.location.reload();

        } catch (error) {
          console.error("Fehler beim Speichern der Bewertung:", error.response?.data || error);
        }
      },

      // Bewertung aus der DB abrufen
      async fetchRating() {
        try {
            const table = CleanTable
            ();
          const response = await axios.get(`/get-rating/${table}/${this.postId}`);
          this.rating = Math.round(response.data.rating); // Rundet auf die nächste ganze Zahl
        } catch (error) {
          console.error("Fehler beim Laden der Bewertung:", error);
        }
      },


    cleanPath() {
      const searchableTables = ["images", "blogs", "didyouknow", "shortpoems"];
      const parts = window.location.pathname.split("/");
      for (const ta of searchableTables) {
        if (parts.includes(ta)) {
          return ta;
        }
      }
      return null;
    },
},
    mounted() {
      this.fetchRating(); // Abrufen der vorhandenen Bewertung beim Laden der Seite
    },
  };
  </script>

  <style>
  .star {
    font-size: 30px;
    cursor: pointer;
    transition: color 0.3s;
  }
  .star.filled {
    color: yellow; /* Gelb für bewertete Sterne */
  }
  .star.hovered {
    color: orange; /* Hover-Farbe */
  }
  .rating-container{
    z-index:100;
    margin-top:-16px;
  }
.stars {
  display: flex;
  align-items: center;
}

.star {
  cursor: pointer;
}
.star svg {
  display: inline-block !important;
}

.star {
  display: inline-block;
}
.stars {
  display: flex;
  align-items: center;
  gap: 6px;
}
  </style>















