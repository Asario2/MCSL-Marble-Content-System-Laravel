<template>
  <AverageRating
    :average="average"
    :total="total"
  />
</template>

<script>
import axios from "axios";
import { route } from "ziggy-js";
import AverageRating from "@/Application/Components/Social/averageratings.vue";
import { ratingBus } from "@/utils/ratingBus";

export default {
  name: "RatingWrapper",
  components: { AverageRating },

  props: {
    postId: {
      type: Number,
      required: true,
    },
  },

  data() {
    return {
      average: 0,
      total: 0,
    };
  },

  methods: {
    async loadRating() {
      try {
        const { data } = await axios.get(
          route("api.getRating", this.postId)
        );

        this.average = Number(data.average) || 0;
        this.total = Number(data.total) || 0;
      } catch (e) {
        console.error("Rating konnte nicht geladen werden", e);
      }
    },
  },

  mounted() {
  this.loadRating();

  ratingBus.on("rating-updated", ({ postId }) => {
    if (postId === this.postId) {
      this.loadRating(); // ⭐ NEU LADEN
    }
  });
},

beforeUnmount() {
  ratingBus.off("rating-updated");
},

};
</script>
