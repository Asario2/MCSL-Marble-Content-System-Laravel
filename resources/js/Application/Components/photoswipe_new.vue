<template>
    <div id="photoswipe-gallery" class="w-full grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mt-6">
      <a
        v-for="(image, index) in images"
        :key="index"
        :href="`${basePath}/big/${image.filename}`"
        :data-pswp-width="image.width"
        :data-pswp-height="image.height"
        target="_blank"
        rel="noopener noreferrer"
        class="w-full bg-layout-sun-0 border border-layout-sun-1000 dark:border-layout-night-1050 rounded-lg shadow text-center bg-layout-sun-2050 dark:bg-layout-night-0"
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
        <!-- <img
          class="rounded-lg  w-full aspect-[4/3] object-cover cursor-pointer no-bra"
          :src=""
          :alt="image.label"
          :title="image.label"
        /> -->
        <div
        v-if="image.label && image.label.trim() && image.label != 'null'"
        v-html="image.label"
        class="text-sm mt-2 dark:text-layout-night-1050  dark:bg-layout-night-0 rounded-lg pt-0 p-2"
        ></div>
        <div v-else style="margin-bottom: -25px;">
        </div>
      </a>
    </div>
  </template>

  <script>
  import PhotoSwipeLightbox from 'photoswipe/lightbox'
  import 'photoswipe/style.css'
  import { cc } from "@/helpers"
  import ZoomImage from "@/Application/Components/Content/ZoomImage.vue";

  export default {
    name: 'PhotoSwipeGallery',
    components: {
    ZoomImage,
    },
    props: {
      basePath: { type: String, required: true },
      images: { type: Array, required: true }
    },
    mounted() {
      this.initLightbox()
    },
    beforeUnmount() {
      if (this.lightbox) {
        this.lightbox.destroy()
        this.lightbox = null
      }
    },
    methods: {
        cc,
      initLightbox() {
        this.lightbox = new PhotoSwipeLightbox({
          gallery: '#photoswipe-gallery',
          children: 'a',
          pswpModule: () => import('photoswipe')
        })
        this.lightbox.init()
      }
    }
  }
  </script>

  <style>
  /* optional: leichtere Galerie-Styling */
  /* #photoswipe-gallery img {
    transition: transform 0.2s ease;
  }
  #photoswipe-gallery img:hover {
    transform: scale(1.03);
  } */
  </style>

