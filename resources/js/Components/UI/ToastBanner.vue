<template>
  <transition name="fade">
    <div
      v-if="visible"
      class="fixed top-4 left-1/2 transform -translate-x-1/2 bg-red-600 text-white px-4 py-2 rounded-lg shadow-lg z-50"
    >
      {{ message }}
    </div>
  </transition>
</template>

<script>
import toastbus from "@/helpers/toastbus";

export default {
  data() {
    return {
      visible: false,
      message: "",
      timeout: null,
    };
  },
  created() {
    toastbus.on("toast-error", (msg) => {
      this.showToast(msg);
    });
    toastbus.on("toast-success", (msg) => {
      this.showToast(msg, "success");
    });
  },
  methods: {
    showToast(msg) {
      this.message = msg;
      this.visible = true;
      clearTimeout(this.timeout);
      this.timeout = setTimeout(() => (this.visible = false), 4000);
    },
  },
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>

