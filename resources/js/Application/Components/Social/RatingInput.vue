<template>
  <div class="rating-container border p-4 rounded-lg">
    <div class="stars flex gap-1">
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
  <p v-if="rating > 0" class="mt-2 text-sm">
      Du hast {{ rating }} Sterne bewertet
    </p>
    <!-- 🔑 Child mit v-model -->
    <NoLogin
      v-if="!AID"
      v-model="form"
    />


  </div>
</template>

<script>
import axios from "axios";
import IconStar from "@/Application/Components/Icons/IconStar.vue";
import NoLogin from "@/Application/Components/Social/NoLogin.vue";
import { CleanTable } from "@/helpers";
import { toastBus } from "@/utils/toastBus";
import { ratingBus } from "@/utils/ratingBus";

export default {
  name: "RatingInput",

  components: {
    IconStar,
    NoLogin,
  },

  props: {
    postId: {
      type: Number,
      required: true,
    },
    table: {
      type: String,
      required: true,
    },
  },

  data() {
    return {
      rating: 0,
      hoverRating: 0,

      // 🔐 eingeloggter User?
      AID: true,

      // 🧾 Formular (kommt vom Child)
      form: {
        name: null,
        email: null,
        password: null,
      },

      emailValid: true,
    };
  },

  mounted() {
    // globaler Auth-Status
    this.AID = Boolean(window.authid);

  console.log("Mounted RatingInput");
  console.log("Auth:", this.AID);


  },

  methods: {

async setRating(star) {
console.log("⭐ Stern geklickt:", star);
    if (!this.AID) {
    if (!this.form.email) {
      window.toastBus.emit({
        status: "error",
        message: "Bitte gib eine Email-Adresse ein.",
      });
      return;
    }

    if (!this.validateEmail(this.form.email)) {

// window.toastBus.emit({ message: "'.$output.'", type: "'.$status.'", duration: '.$seconds.' })';
        window.toastBus.emit({
        status: "error",
        message: "Bitte gib eine gültige Email-Adresse ein.",
      });
      return;
    }
  }

  this.rating = star;
  await this.saveRating(star);
},


    validateEmail(email) {
      if (!email) return false;
      return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    },

    async saveRating(star) {
      try {
        const res = await axios.post("/save-rating", {
            rating: star,
            postId: this.postId,
            table: CleanTable(),
            email: this.form.email,
        });

        // 🔁 andere Komponenten informieren
        ratingBus.emit("rating-updated", {
          postId: this.postId,
          email: this.form.email,
        });

        // 🔥 Parent-Event
        this.$emit("rated", {
          average: res.data.average,
          total: res.data.total,
        });

        // ✅ Erfolg
        window.toastBus.emit({
          status: "points",
          message: "Du hast 1 MCSL Point gesammelt",
        });
      } catch (e) {
        if (e.response?.status === 401) {
          return;
        }

        console.error("Rating speichern fehlgeschlagen", e);

        window.toastBus.emit({
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
