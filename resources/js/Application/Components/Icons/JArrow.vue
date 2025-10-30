<template>
  <svg
    xmlns="http://www.w3.org/2000/svg"
    viewBox="0 0 24 24"
    class="w-8 h-8 text-green-600 animate-bounce-up-fade"
    fill="none"
    stroke="currentColor"
    stroke-width="2.5"
    stroke-linecap="round"
    stroke-linejoin="round"
  >
    <path d="M12 19V5" />
    <path d="M5 12l7-7 7 7" />
  </svg>
</template>

<style scoped>
@keyframes bounce-up-fade {
  0%, 100% {
    transform: translateY(0);
    opacity: 1;
  }
  20% {
    transform: translateY(-6px);
  }
  40% {
    transform: translateY(-12px);
  }
  60% {
    transform: translateY(-8px);
  }
  80% {
    transform: translateY(-4px);
  }
  100% {
    opacity: 0;
  }
}

.animate-bounce-up-fade {
  animation: bounce-up-fade 1.2s ease-in-out 0s 6 forwards;
  /* ↑ Dauer, Timing, Startverzögerung, Wiederholungen, behält Endzustand */
}
</style>
