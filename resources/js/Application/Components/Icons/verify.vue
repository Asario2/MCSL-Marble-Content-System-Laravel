<template>
<svg xmlns="http://www.w3.org/2000/svg"
     width="120" height="120"
     viewBox="0 0 120 120">

  <circle cx="60" cy="60" r="50"
          stroke="#22c55e"
          stroke-width="6"
          fill="none" />

  <path d="M40 65 L55 80 L85 45"
        fill="none"
        stroke="#22c55e"
        stroke-width="6"
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-dasharray="80"
        stroke-dashoffset="80">
    <animate attributeName="stroke-dashoffset"
             from="80" to="0"
             dur="0.6s"
             fill="freeze"
             begin="0.2s" />
  </path>
</svg>
</template>
<script>
export default {
      name: "VerifySVG",
};
</script>
