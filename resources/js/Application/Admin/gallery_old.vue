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
      <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
    <h1 class="text-3xl font-bold text-layout-title">
        Unused Images
    </h1>

   </div>


      <photoswipe_old :dom="dom" :images="images_container" :basePath="images_container.basepath" :checkable="true">

      </photoswipe_old>
    </section>
  </Layout>
</template>

<script>
// import axios from "axios";
// import { router } from "@inertiajs/vue3";
import photoswipe_old from "@/Application/Components/photoswipe_old.vue";
import Layout from "@/Application/Admin/Shared/ab/Layout.vue";
import Breadcrumb from "@/Application/Components/Content/Breadcrumb.vue";
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";

export default {
  name: "OldGallery",

  components: {
    Layout,
    Breadcrumb,
    MetaHeader,
    photoswipe_old,
  },
  props:{
    images_container:Object,
    im_cont:Object,
  },
  data(){
    return {
       dom: this.dom ?? 'ab',
    }
  },
  mounted(){

  },
  methods:{
    domres()
    {
        alert(this.dom);
       location.href='/admin/get_unused_imgz/' + this.dom;
    }
  }
};

</script>


        <style>
        button { outline: none; }
        .w-fully{min-width:100% !important;
                max-width:100% !important;}
        </style>

