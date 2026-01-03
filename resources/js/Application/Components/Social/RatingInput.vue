<template>
  <div class="rating-container border p-4 rounded-lg">
    <div class="stars">
      <span
        v-for="star in 5"
        :key="star"
        class="star"
        :class="{ filled: star <= rating, hovered: star <= hoverRating }"
        @mouseover="hoverRating = star"
        @mouseleave="hoverRating = 0"
        @click="setRating(star)"
      >
        <IconStar wi="24" he="24" />
      </span>
    </div>

    <p v-if="rating > 0">Du hast {{ rating }} Sterne bewertet</p>
  </div>
</template>

<script>
import axios from "axios";
import IconStar from "@/Application/Components/Icons/IconStar.vue";
import { CleanTable } from "@/helpers";
import { toastBus } from "@/utils/toastBus";
import { ratingBus } from "@/utils/ratingBus";
import { route } from "ziggy-js";

export default {
  name: "RatingInput",
  components: { IconStar },

  props: {
    postId: { type: Number, required: true },
    table: { type: String, required: true },
  },

  data() {
    return {
      rating: 0,
      hoverRating: 0,
    };
  },

  methods: {
    async setRating(star) {
      this.rating = star;
      await this.saveRating(star);
    },

    async saveRating(star) {
        try {
            const res = await axios.post("/save-rating", {
            rating: star,
            postId: this.postId,
            table: CleanTable(),
            },
            {
                requiresAuth: true, // <- Hier richtig in config
            }, {
  headers: {
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
  }
        });
        // 🔁 Rating neu laden (andere Komponenten)
        ratingBus.emit("rating-updated", {
        postId: this.postId,
        });

        // 🔥 Parent informieren
        this.$emit("rated", {
        average: res.data.average,
        total: res.data.total,
        });

        // ✅ Erfolg-Toast
        toastBus.emit("toast", {
        status: "points",
        message: "Du hast 1 MCSL Point gesammelt",
        });

    } catch (e) {
        // 🔐 NICHT EINGELOGGT
        if (e.response?.status === 401) {
        const currentUrl = window.location.href;

        window.location.href =
            route("login") + "?redirect=" + encodeURIComponent(currentUrl);
        return;
        }

        // ❌ Sonstiger Fehler
        console.error("Rating speichern fehlgeschlagen", e);

        toastBus.emit("toast", {
        status: "error",
        message: "Bewertung konnte nicht gespeichert werden",
        });
    }
    },
  },
};
</script>

<style scoped>
.star {
  cursor: pointer;
}
.star.filled {
  color: yellow;
}
.star.hovered {
  color: orange;
}
</style>
