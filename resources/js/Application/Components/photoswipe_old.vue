<template>
    <button v-if="images && images.length > 0" class="button mt-4 inline-block mr-4 bg-layout-sun-500 dark:bg-layout-night-500 p-3 rounded-lg" @click="zipFiles">
    Alle zippen
    </button>
    <span v-else>

        Keine Ungenutzten Bilder vorhanden
    </span>
    <span v-if="zipUrl" class="mr-3 ml-3">
    <a

    :href="'imageszip://run?file=http://'+ Host + (zipUrl ?? '') + '&md5=' + md5"
    class="button mt-4 inline-block mr-4 bg-layout-sun-500 dark:bg-layout-night-500 p-3 rounded-lg"
    download
    >
    ZIP herunterladen
    </a>
    MD5:&nbsp;&nbsp;
    <code class="dark:bg-gray-200 text-green-700 dark:text-green-100 p-1">{{ md5 }}</code> Datei:&nbsp;&nbsp;<code class="dark:bg-gray-200 p-1 dark:text-green-100">{{ zipUrl.replace("/tmp/",'') }}</code>

    <button class="button bg-red-500 m-3 p-3 rounded-lg" @click="DelFilez">Alle Löschen</button>
    </span>
        <div v-for="(image, index) in images" :key="index">
        <span v-for="(folders,index) in image.folders" :key="index">
        <input type="hidden" name="images[]" :id="'images_' + index" :value="`${folders.path}`"/>
        </span>
    </div>
<div
    id="photoswipe-gallery"
    class="w-full grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mt-6"
  >

    <div   v-for="(image, index) in images"
        :key="index"
        class="w-full bg-layout-sun-0 border border-layout-sun-1000 dark:border-layout-night-1000 rounded-lg shadow text-center bg-layout-sun-2050 dark:bg-layout-night-0 cursor-pointer"
        >


  <a
      :href="`/images/${image.mandant}/${image.table}/${image.column}/big/${image.filename}`"
      :data-pswp-width="image.width"
      :data-pswp-height="image.height"
      @click.prevent="openLightbox(index)"

    >
    {{image.mandant }}/{{ image.table }}/{{ image.column  }}/{{ image.apath }}
      <ZoomImage
        awidth="a_320"
        :src="`/images/${image.mandant}/${image.table}/${image.column}/${image.apath}/${cc(image.filename)}`"
        :alt="image.label"
        :title="image?.label"
        :width="276"
        :height="276"
        class="imgprev"
      />
    </a>
      <div
        v-if="image.label && image.label.trim() && image.label !== 'null'"
        v-html="image.label"
        class="text-sm mt-2 dark:text-layout-night-1050 dark:bg-layout-night-0 rounded-lg pt-0 p-[-5px]"
      ></div>

      <div v-else style="margin-bottom: -25px;"></div>
    </div>
  </div>
</template>

<script>
import { cc } from "@/helpers";
import ZoomImage from "@/Application/Components/Content/ZoomImage.vue";
import axios from "axios";
// ✔ KORREKTE IMPORTS FÜR PHOTOSWIPE 6 (Vite-kompatibel)
import PhotoSwipeLightbox from 'photoswipe/dist/photoswipe-lightbox.esm.js';
import PhotoSwipe from 'photoswipe/dist/photoswipe.esm.js';
import 'photoswipe/dist/photoswipe.css'

export default {
  name: "PhotoSwipeNew",
  components: { ZoomImage },

  props: {
    basePath: { type: String, required: true },
    images: { type: [Object,Array], required: true },
    im_cont: { type:[Object,Array]},
    dom:String,
  },

  data() {

    return {
        lightbox: null,
        zipUrl: null,
        md5:null,
        Host:window.ahost,
        dom_alt:this.dom,
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
    async zipFiles() {
    const files = Array.from(
        document.querySelectorAll("input[name='images[]']")
        ).map(input => input.value)
      const res = await axios.post('/admin/zip-images/' + this.dom, {
        files: files,
        dom:this.dom,
      })

      this.zipUrl = res.data.url;
      this.md5 = res.data.md5;

    },
    async DelFilez() {
    if (confirm("Möchten Sie alle Dateien wirklich löschen?")) {
    // Benutzer hat auf "OK" geklickt
//     console.log("Datei wird gelöscht...");


      const files = Array.from(
        document.querySelectorAll("input[name='images[]']")
        ).map(input => input.value)
      const res = await axios.post('/admin/removeFiles', {
        files: files
      })

      this.zipUrl = res.data.url;
      this.md5 = res.data.md5;
      location.reload();
      }
    },

    openLightbox(index) {
      if (!this.lightbox) return;

      // ✔ loadAndOpen funktioniert in PS6 korrekt
      this.lightbox.loadAndOpen(index);

    },
  },
  computed:{
    bPath(){
        return "/images/" + this.images.mandant + "/" + this.images.table + "/" + this.images.column + "/";
    }
  }
};
</script>

<style scoped>
</style>
