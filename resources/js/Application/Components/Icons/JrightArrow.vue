<template>
  <svg
    xmlns="http://www.w3.org/2000/svg"
    viewBox="0 0 24 24"
    class="w-8 h-8 text-green-600 animate-bounce-right-fade"
    :class="{ 'animate-bounce-up-fade': true }"
    :style="{ animationIterationCount: iterationCount }"
    fill="none"
    stroke="currentColor"
    stroke-width="2.5"
    stroke-linecap="round"
    stroke-linejoin="round"
  >
    <path d="M5 12h14" />
    <path d="M12 5l7 7-7 7" />
  </svg>
</template>



<script>
export default {
  data() {
    return {
      iterationCount: 6, // default für "/"
    };
  },
  mounted() {
    if (window.location.pathname !== '/') {
      this.iterationCount = 4; // nur 1 Wiederholung, wenn nicht die Startseite
    }
  },
};
</script>

<style scoped>
@keyframes bounce-up-fade {

  0%,
  100% {
    transform: translateX(0);
    opacity: 1;
  }
  20% {
    transform: translateX(6px);
  }
  40% {
    transform: translateX(12px);
  }
  60% {
    transform: translateX(8px);
  }
  80% {
    transform: translateX(4px);
  }
  100% {
    transform: translateX(0);
    opacity: 0;
  }
}

.animate-bounce-up-fade {
  animation: bounce-up-fade 1.2s ease-in-out forwards;
}
</style>

