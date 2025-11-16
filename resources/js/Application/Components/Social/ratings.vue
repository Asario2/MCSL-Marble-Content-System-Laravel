<template>
    <div>
      <button @click="toggleShareBox" class="flex items-center justify-start gap-2 px-4 py-2 rounded-lg font-semibold bg-blue-500 text-white hover:bg-blue-600
dark:bg-blue-600 dark:hover:bg-blue-700 dark:text-white">
       <icon-share /> Bewerten
      </button>

      <div v-if="showShareBox" class="share-box">
        <div ref="shariff" id="shariff-container" class="shariff-widget"></div>
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
    methods: {
        toggleShareBox() {
            this.showShareBox = !this.showShareBox;
            if (this.showShareBox) {
            this.initShariff();
            }
        },
        initShariff() {
      nextTick(() => {
        if (this.$refs.shariff) {
            // console.log('Shariff Element:', this.$refs.shariff);
            const shariffElement = document.getElementById('shariff-container');

        if (shariffElement) {
        new Shariff(shariffElement, {
            services: ['facebook', 'twitter', 'xing', 'whatsapp'],  // Deine gewünschten Dienste
            theme: 'classic', // Oder ein anderes Thema
            orientation: 'horizontal', // Oder 'vertical'
        });
        }
    }
        else {
          console.error('Shariff Element nicht gefunden');
        }
      });
    },

    toggleShareBox() {
      this.showShareBox = !this.showShareBox;
      if (this.showShareBox) {
        this.initShariff();
      }
    },
  },
  };
  </script>

