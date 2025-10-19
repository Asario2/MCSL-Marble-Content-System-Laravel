<template>
    <div class="average-rating">
    <!-- Sterne -->
    <div class="stars">
        <Star
        v-for="(star, index) in starsArray"
        :key="index"
        :starValue="index + 1"
        :isFilled="star === 'full'"
        :isHalf="star === 'half'"
        />
    </div>

    <!-- Bewertungstext -->
    <p class="rating-text" v-if="totalVotes > 0">
        Durchschnitt: {{ averageRating.toFixed(1).replace(".0", "") }} Sterne <br />
        ({{ totalVotes }} Bewertungen)
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
    postId: Number,
    av: Number,
    tot: Number,
    ratings:Object,
},

  data() {
    // console.log('Average Rating:', this.av);  // Debugging
    // console.log('Total Votes:', this.tot);    // Debugging


    return {
      averageRating: this.av ?? 0,
      totalVotes: this.tot ?? 0,
    };
  },

  computed: {
    starsArray() {
      if (this.averageRating <= 0) {
        return Array(5).fill("empty");
      }

      const fullStars = Math.floor(this.averageRating);
      const hasHalfStar = this.averageRating % 1 >= 0.5;
      const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);

      return [
        ...Array(fullStars).fill("full"),
        ...(hasHalfStar ? ["half"] : []),
        ...Array(emptyStars).fill("empty"),
      ];
    },
  },
  mounted(){
    // console.log("str:" + this.tot);
  }
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
