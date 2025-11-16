<template>
    <span class="star" :class="{ filled: isFilled, half: isHalf, empty: !isFilled && !isHalf }">
      <icon-star wi='24' he='24' v-if="isFilled"></icon-star>
      <icon-star-half v-if="isHalf && !isFilled"></icon-star-half>
      <!-- <icon-star-empty class="empty" v-else></icon-star-empty> -->
    </span>
  </template>

  <script>
  import IconStarHalf from "@/Application/Components/Icons/IconStarHalf.vue";
  import IconStar from "@/Application/Components/Icons/IconStar.vue";
  import IconStarEmpty from "@/Application/Components/Icons/IconStarEmpty.vue";

  export default {
    components: {
      IconStar,
      IconStarHalf,
      IconStarEmpty,
    },
    props: {
    starValue: {
      type: [Number,String],
      required: true
    },
    isFilled: {
      type: Boolean,
      required: true
    },
    isHalf: {
      type: Boolean,
      required: true
    }
  }
};
  </script>

<style scoped>
.star {
  display: inline-block;
}

.filled {
  color: #facc15; /* Gelb für volle Sterne */
}

.half {
  color: #facc15; /* Gelb für halbe Sterne */
}

.empty {
  color: #d1d5db; /* Graue Farbe für leere Sterne */
}
</style>

