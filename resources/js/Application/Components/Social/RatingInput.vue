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
      const res = await axios.post("/save-rating", {
        rating: star,
        postId: this.postId,
        table: CleanTable(),
      });
        ratingBus.emit("rating-updated", {
        postId: this.postId,
        });

      // 🔥 Parent informieren
      this.$emit("rated", {
        average: res.data.average,
        total: res.data.total,
      });

      toastBus.emit("toast", {
        status: "points",
        message: "Du hast 1 MCSL Point gesammelt",
      });
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
