<template>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" width="200" height="200" aria-label="Animiertes Verbotsschild">
  <!-- Background (transparent) -->
  <rect width="100%" height="100%" fill="none"/>

  <!-- Ring (circle outline) -->
  <!-- radius = 80, circumference ≈ 2πr = 502.65 -->
  <circle
    cx="100" cy="100" r="80"
    fill="none"
    stroke="#d62828" stroke-width="14" stroke-linecap="round" stroke-linejoin="round"
    stroke-dasharray="503" stroke-dashoffset="503" >
    <!-- draw the circle -->
    <animate attributeName="stroke-dashoffset" from="503" to="0" dur="0.6s" begin="0s" fill="freeze" />
    <!-- subtle pulse glow after draw -->
    <animate attributeName="stroke-width" values="14;18;14" dur="1.6s" begin="0.7s" repeatCount="indefinite" />
    <animate attributeName="stroke-opacity" values="1;0.85;1" dur="1.6s" begin="0.7s" repeatCount="indefinite" />
  </circle>

  <!-- Cross (X) using two lines -->
  <!-- approx line length ~155.6 -> use dasharray 400 so animate looks smooth -->
  <line x1="55" y1="55" x2="145" y2="145"
        stroke="#d62828" stroke-width="14" stroke-linecap="round"
        stroke-dasharray="400" stroke-dashoffset="400" opacity="0.98">
    <!-- draw first diagonal after the circle finished -->
    <animate attributeName="stroke-dashoffset" from="400" to="0" dur="0.35s" begin="0.62s" fill="freeze" />
  </line>

  <line x1="145" y1="55" x2="55" y2="145"
        stroke="#d62828" stroke-width="14" stroke-linecap="round"
        stroke-dasharray="400" stroke-dashoffset="400" opacity="0.98">
    <!-- draw second diagonal slightly later for a nice effect -->
    <animate attributeName="stroke-dashoffset" from="400" to="0" dur="0.35s" begin="0.95s" fill="freeze" />
  </line>

  <!-- optional center dot for visual balance (fades in) -->
  <circle cx="100" cy="100" r="0" fill="#d62828" opacity="0.0">
    <animate attributeName="r" values="0;2;0" dur="1.2s" begin="0.9s" repeatCount="indefinite" />
    <animate attributeName="opacity" values="0;0.6;0" dur="1.2s" begin="0.9s" repeatCount="indefinite" />
  </circle>
</svg>
</template>
<script>
export default {
      name: "ErrorSVG",
};
</script>

