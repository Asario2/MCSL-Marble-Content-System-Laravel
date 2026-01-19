<template>
    <div
      class="relative overflow-hidden rounded"
      :style="{ width: width + 'px', height: height + 'px' }"
    >
      <img
        :src="src"
        :alt="alt"
        :title="title"
        :width="width"
        :height="height"
        :class="imageClasses">
    </div>
  </template>

  <script>
  export default {
    name: "ZoomImage",
    props: {
      src: String,
      alt: String,
      title: String,
      width: {
        type: [String, Number],
        default: 320,
      },
      height: {
        type: [String, Number],
        default: 320,
      },
      awidth: {
    type: String,
    required: false,
  },
  grayscale:{
    default:false,
    },
    },
      computed: {
    imageClasses() {
      return [
        this.awidth,
        'w-full object-cover object-center transition-transform duration-300 ease-in-out hover:scale-110',
        {
          'grayscale opacity-50': this.grayscale,
        },
      ];
    },
  },
  };
  </script>

<style>
.w-x{
    min-width:300px !important;
    max-width:300px !important;
    width:300px !important;
    min-height:300px !important;
    max-height:300px !important;
    height:300px !important;

    margin:0px !important;
    padding:0px !important;
}
.a_320{
    min-width:360px !important;
    max-width:360px !important;
    /* width:360px !important; */
    min-height:360px !important;
    max-height:360px !important;
    height:360px !important;
    width:100% !important;
    margin:0px !important;

    padding:0px !important;
    margin-right:-10px !important;
}

</style>

