<template>
    <div>


      <div v-if="showShareBox" class="share-box">
        <div style="width: 100%; margin-top: 10px;">
        <div ref="shariff" id="shariff-container" class="shariff" data-button-style="icon"></div>
        </div>
      </div>
    </div>
  </template>

  <script>
  import { nextTick } from 'vue';
  import { parse } from 'url';
  import IconShare from "@/Application/Components/Icons/IconShare.vue";
  export default {
    components: {
            IconShare,

        },
    data() {
      return {
        showShareBox: false,
      };
    },
    props:{
        added:{
            type:String,
        },
    },
    methods: {
        toggleShareBox() {
            // console.log("toggleShareBox aufgerufen");

            this.showShareBox = !this.showShareBox;
            if (this.showShareBox) {
            this.initShariff();
            }
        },
        initShariff()
        {
            // console.log("initShariff aufgerufen");

  nextTick(() => {
    const shariffElement = this.$refs.shariff;
    if (shariffElement) {
    const url = `${window.location.origin}${window.location.pathname}${this.added || ''}`;
    //   console.log("Shariff-URL:", url);

      shariffElement.setAttribute('data-url', url);

      new Shariff(shariffElement, {
        services: ['facebook', 'twitter', 'xing', 'whatsapp'],
        theme: 'classic',
        orientation: 'horizontal',
      });
    }
  });
},



  },

};
  </script>

