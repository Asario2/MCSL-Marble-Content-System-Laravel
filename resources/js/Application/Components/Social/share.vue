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
import { faL } from '@fortawesome/free-solid-svg-icons';
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
        sse:{
            type:String,
            required:false,
        }
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
            alert(this.sse);
            // console.log("initShariff aufgerufen");

  nextTick(() => {
    const shariffElement = this.$refs.shariff;
    if (shariffElement) {
    const url = `${window.location.origin}${window.location.pathname}${this.added || ''}${this.sse || ''}`;
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

