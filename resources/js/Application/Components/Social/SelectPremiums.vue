<template>
<layout>
  <meta-header title="MCSL Points gegen Prämien tauschen" />
<div class="space-y-8">

    <div
      v-for="(items, category) in groupedImages"
      :key="category"
      class="bg-layout-sun-100 dark:bg-layout-night-100
             border border-layout-sun-300 dark:border-layout-night-300
             rounded-xl p-4"
    >
      <h2 class="text-lg font-semibold mb-4">
        {{ ucf(category) }}
      </h2>

      <div id="gallery" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
        <div

          v-for="img in items"
          :key="img.id"
          class="border rounded-lg p-2 text-center"
        >
<a v-if="img.preis*3 < MCSL_Total"
  :href="'/images/_ab/images/image_path/big/' + img?.image_path"
  :data-pswp-width="img?.img_x"
  :data-pswp-height="img?.img_y"
>
 <ZoomImage
  :src="'/images/_ab/images/image_path/thumbs/' + img?.image_path"
  :alt="img?.name"
  :title="img?.name"
  :width="400"
  :height="300"
  class="imgprev w-full object-cover object-center rounded mb-2"
/>
</a>

    <a v-else :href="'/images/_ab/images/image_path/big/' + img?.image_path"
  :data-pswp-width="img?.img_x"
  :data-pswp-height="img?.img_y"
>
 <ZoomImage
  :src="'/images/_ab/images/image_path/thumbs/' + img?.image_path"
  :alt="img?.name"
  :title="img?.name"
  :width="400"
  :height="300"
  :grayscale="true"
  class="imgprev w-full object-cover object-center rounded mb-2"
/>
</a>

          <div class="text-sm font-medium">
            {{ img?.name }}
          </div>

          <div v-if="img.preis*3 < MCSL_Total" class="text-sm text-green-600 font-semibold">
            {{ img.preis*3 }} MCSL Points
          </div>
          <div v-if="img.preis*3 >= MCSL_Total" class="text-sm text-red-600 font-semibold">
            {{ img.preis*3 }} MCSL Points
          </div>
          <div class="m-5">
          <a
          v-if="img.preis*3 < MCSL_Total"
            class="button-primary_alt"
            :href="'/SubmitPremiums/' + users_id + '/' + img.id"
            @click.stop
            >
            Jetzt Einlösen
            </a>
          </div>
        </div>
      </div>
</div>

    <p v-if="Object.keys(groupedImages).length === 0"
       class="text-gray-500 italic">
      Keine verkaufbaren Bilder unter 300 MCS Points gefunden.
    </p>

  </div>
  </layout>
</template>

<script>
    import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
    import { ucf } from "@/helpers";
    import Layout from '@/Application/Homepage/Shared/Layout.vue';
    import ZoomImage from "@/Application/Components/Content/ZoomImage.vue";
    import PhotoSwipeLightbox from 'photoswipe/dist/photoswipe-lightbox.esm.js';
    import 'photoswipe/dist/photoswipe.css';

export default {
  name: "SelectPremiums",

    components:{Layout,ZoomImage,MetaHeader},

  props: {
    images: {
      type: Array,
      required: true,
    },
    MCSL_Total:Number,
    users_id:Number,
  },
  methods:{
    ucf,
  },
  computed: {
    groupedImages() {
      const result = {};

      this.images
        .filter(img =>
          img.xis_IsSaleable == 1 &&
          Number(img.preis) < 100
        )
        .forEach(img => {
          const category =
            img.image_category?.name || "Ohne Kategorie";

          if (!result[category]) {
            result[category] = [];
          }

          result[category].push(img);
        });

      return result;
    },
  },
  mounted(){
    this.lightbox = new PhotoSwipeLightbox({
      gallery: "#gallery",
      children: "a:not([href^='/admin/tables'])", // Admin-Links ausschließen
      pswpModule: () => import("photoswipe"),
      zoom: true,
      secondaryZoomLevel: 2,
      maxZoomLevel: 4,
      initialZoomLevel: "fit",
      wheelToZoom: true,
      barsSize: { top: 50, bottom: 50 },
      padding: { top: 30, bottom: 30, left: 30, right: 30 },
      showHideAnimationType: "zoom",
      galleryUID: "photoswipe-gallery",
    });
    const params = new URLSearchParams(window.location.search);
    const search = params.get("search");

    // Wenn search gesetzt ist, verstecke das Loading-Div
    if (search && search.trim() !== "") {

      this.isLoading = false;
      this.loading = false;
    }
    else{
        this.isLoading = true;
    }
    this.lightbox.init();
  },
};
</script>
<style>
      .imgprev {
    cursor: zoom-in;
    max-width: 100%;
    height: auto;
    border-radius: 8px;
  }
</style>
