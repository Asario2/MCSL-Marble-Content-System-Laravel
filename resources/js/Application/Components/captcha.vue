<template>
    <div>
      <img
        :src="captchaUrl"
        alt="CAPTCHA"
        width="200"
        height="70"
        @click="refreshCaptcha"
        style="cursor:pointer"
        class="mt-[8px]"
      >
      <input
        type="text"
        name="captcha"
        id="captcha"
        class="block p-2 mb-[8px] mt-[8px]  w-full rounded-lg border text-sm text-layout-sun-900 bg-layout-sun-50 border-layout-sun-300 focus:ring-primary-sun-500 focus:border-primary-sun-500 dark:text-layout-night-900 dark:bg-layout-night-50 dark:border-layout-night-300 dark:focus:ring-primary-night-500 dark:focus:border-primary-night-500"
        maxlength="6"
        style="width:200px;"
        v-model="captchaInput"
        @input="emitValue"
        placeholder="Captcha"
      />
    </div>
  </template>

  <script>
  export default {
    name: "captcha",
    data() {
      return {
        captchaInput: '',
        timestamp: Date.now()
      }
    },
    computed: {
      captchaUrl() {
        return `/images/captcha.php?time=${this.timestamp}`
      }
    },
    methods: {
      emitValue() {
        this.$emit('update', this.captchaInput)
      },
      refreshCaptcha() {
        this.timestamp = Date.now() // neues Bild erzwingen
      },
      resetCaptcha() {
        this.captchaInput = ''      // Input leeren
        this.refreshCaptcha()       // Bild erneuern
        this.emitValue()            // Main-Komponente updaten
      }
    }
  };
  </script>


<style scoped>
.bgo{
    background-color: rgb(39 39 42);
}
</style>

