<template>
  <div
    id="photoswipe-gallery"
    class="w-full grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mt-6"
  >
    <a
      v-for="(image, index) in images"
      :key="index"
      :href="`${basePath}/big/${image.filename}`"
      :data-pswp-width="image.width"
      :data-pswp-height="image.height"
      @click.prevent="openLightbox(index)"
      class="w-full bg-layout-sun-0 border border-layout-sun-1000 dark:border-layout-night-1050 rounded-lg shadow text-center bg-layout-sun-2050 dark:bg-layout-night-0 cursor-pointer"
    >
      <ZoomImage
        awidth="a_320"
        :src="`${basePath}/thumbs/${cc(image.filename)}`"
        :alt="image.label"
        :title="image?.label"
        :width="346"
        :height="346"
        class="imgprev"
      />

      <div
        v-if="image.label && image.label.trim() && image.label !== 'null'"
        v-html="image.label"
        class="text-sm mt-2 dark:text-layout-night-1050 dark:bg-layout-night-0 rounded-lg pt-0 p-2"
      ></div>

      <div v-else style="margin-bottom: -25px;"></div>
    </a>
  </div>
</template>

<script>
import { cc } from "@/helpers";
import ZoomImage from "@/Application/Components/Content/ZoomImage.vue";

// ✔ KORREKTE IMPORTS FÜR PHOTOSWIPE 6 (Vite-kompatibel)
import PhotoSwipeLightbox from 'photoswipe/dist/photoswipe-lightbox.esm.js';
import PhotoSwipe from 'photoswipe/dist/photoswipe.esm.js';
import 'photoswipe/dist/photoswipe.css' 

export default {
  name: "PhotoSwipeGallery",
  components: { ZoomImage },

  props: {
    basePath: { type: String, required: true },
    images: { type: Array, required: true },
  },

  data() {
    return {
      lightbox: null,
    };
  },

  mounted() {
    // ✔ Lightbox EINMAL initialisieren
    this.lightbox = new PhotoSwipeLightbox({
      gallery: "#photoswipe-gallery",
      children: "a",

      // sehr wichtig! sonst open() undefined
      pswpModule: PhotoSwipe,

      showHideAnimationType: "fade",
    });

    this.lightbox.init();
  },

  beforeUnmount() {
    if (this.lightbox) {
      this.lightbox.destroy();
      this.lightbox = null;
    }
  },

  methods: {
    cc,

    openLightbox(index) {
      if (!this.lightbox) return;

      // ✔ loadAndOpen funktioniert in PS6 korrekt
      this.lightbox.open(index);
    },
  },
};
</script>

<style scoped>
</style>
