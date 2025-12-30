<template>
  <div class="average-rating">
    <div class="stars">
      <Star
        v-for="(star, index) in starsArray"
        :key="index"
        :starValue="index + 1"
        :isFilled="star === 'full'"
        :isHalf="star === 'half'"
      />
    </div>

    <p class="rating-text" v-if="total > 0">
      Durchschnitt: {{ average.toFixed(1).replace(".0", "") }} Sterne <br />
      ({{ total }} Bewertungen)
    </p>

    <p class="rating-text" v-else>
      Noch keine Sterne vergeben
    </p>
  </div>
</template>

<script>
import Star from "@/Application/Components/Social/Star.vue";

export default {
  name: "AverageRating",
  components: { Star },

  props: {
    average: { type: Number, default: 0 },
    total: { type: Number, default: 0 },
  },

  computed: {
    starsArray() {
      const avg = this.average;
      const full = Math.floor(avg);
      const half = avg % 1 >= 0.5;
      const empty = 5 - full - (half ? 1 : 0);

      return [
        ...Array(full).fill("full"),
        ...(half ? ["half"] : []),
        ...Array(empty).fill("empty"),
      ];
    },
  },
};
</script>

<style scoped>
.stars {
  display: flex;
  gap: 5px;
}
.rating-text {
  margin-top: 5px;
  font-size: 14px;
  color: #facc15;
}
</style>
    