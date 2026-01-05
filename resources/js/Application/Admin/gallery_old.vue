<!-- EmailSender.vue -->
<template>
  <Layout>
    <MetaHeader title="Unused Images" />

    <template #header>
      <Breadcrumb :breadcrumbs="breadcrumbs" />
    </template>

    <section
      class="block max-w-sm mx-auto sm:max-w-full p-4 bg-layout-sun-100 dark:bg-layout-night-100"
    >
      <h1 class="text-3xl font-bold mb-6 text-layout-title">
        Unused Images
      </h1>
      <span v-for="image in images_container"
        :key="image.id"
        class="col-span-2">
      <photoswipe_new :images="image.data" :basePath="image.basepath" :checkable="true">

      </photoswipe_new>
      </span>
    </section>
  </Layout>
</template>

<script>
import axios from "axios";
import { router } from "@inertiajs/vue3";
import photoswipe_new from "@/Application/Components/photoswipe_new.vue";
import Layout from "@/Application/Admin/Shared/ab/Layout.vue";
import Breadcrumb from "@/Application/Components/Content/Breadcrumb.vue";
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";

export default {
  name: "OldGallery",

  components: {
    Layout,
    Breadcrumb,
    MetaHeader,
    photoswipe_new,
  },
  props:{
    images_container:Object,
  },
};
</script>


        <style>
        button { outline: none; }
        .w-fully{min-width:100% !important;
                max-width:100% !important;}
        </style>

