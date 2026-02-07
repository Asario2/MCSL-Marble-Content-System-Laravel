<template>
  <!-- Toast Container -->
  <div class="">
    <transition-group name="toast" tag="div">
      <div
        v-for="toast in toastBus.toasts"
        :key="toast.id"
        v-show="toast.show"

        class="mb-2 flex items-center w-full p-2 rounded-lg shadow text-layout-sun-900 dark:text-layout-night-900 transition-all duration-300"
        :class="determineAlertClass(toast.type)"
        role="alert"
      >
        <!-- Icons -->
        <div
            class="mb-2 flex items-center w-full p-2 rounded-lg shadow text-layout-sun-900 dark:text-layout-night-900"
            :class="alertClass"
            role="alert"
        >
            <div
                v-if="toast.type === 'success'"
                class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg text-layout-sun-900 bg-layout-sun-0 dark:text-layout-night-900 dark:bg-layout-night-0 bg-text-green-500"
            >
                <icon-done class="w-5 h-5"></icon-done>
            </div>
             <div
                v-if="toast.type === 'points'"
                class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg text-layout-sun-900 dark:bg-yellow-1060 text-layout-sun-200 bg-text-yellow-1060  font-bold dark:bg-layout-night-200"
                title="Punkte erhalten"
                aria-label="Punkte erhalten"
            >
                <icon-done class="w-5 h-5"></icon-done>
            </div>

            <div
                v-if="toast.type === 'info'"
                class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg text-layout-sun-900 bg-layout-sun-0 dark:text-layout-night-900  bg-text-blue-500"
            >
                <icon-done class="w-5 h-5" fill="currentColor"></icon-done>
            </div>
            <div
                v-if="toast.type === 'warning'"
                class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg text-layout-sun-900 bg-orange-500 dark:text-layout-night-900 dark:bg-orange-500"
            >
                <icon-exclamation class="w-5 h-5"></icon-exclamation>
            </div>
            <div
                v-if="toast.type === 'error'"
                class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg text-layout-sun-900 bg-red-500 dark:text-layout-night-900 dark:bg-red-500"
            >
                <icon-exclamation class="w-5 h-5"></icon-exclamation>
            </div>
            <div class="ml-3 text-base font-normal">
                <span v-html="toast.message"></span>
            </div>


        <!-- Close Button -->
        <button
          type="button"
          class="ml-auto -mx-1.5 -my-1.5 rounded-full p-1.5 inline-flex h-8 w-8 focus:ring-2 hover:text-white hover:bg-gray-800 border border-gray-400"
          @click="removeToast(toast.id)"
        >
          <span class="sr-only">Close</span>
          <icon-close class="w-5 h-5"></icon-close>
        </button>
      </div>
    </div>
    </transition-group>
  </div>
</template>

<script>
import { reactive, watch } from "vue";
import IconDone from "@/Application/Components/Icons/Done.vue";
import IconExclamation from "@/Application/Components/Icons/Exclamation.vue";
import IconClose from "@/Application/Components/Icons/Close.vue";

// Vollständiger globaler ToastBus
export const toastBus = reactive({
toasts: [],
idCounter: 0,
emit(payload) {
//     console.log("[ToastBus] emit called:", payload);

    const toast = {
    id: this.idCounter++,
    message: payload.message ?? "",
    type: payload.type || payload.status || "info",
    duration: payload.duration ?? 30000,
    show: true,
    };

    this.toasts.push(toast);
//     console.log("[ToastBus] toasts after push:", this.toasts);

    // Auto-Hide
    if (toast.duration > 0) {
    setTimeout(() => {
        this.removeToast(toast.id);
    }, toast.duration);
    }

    window.scrollTo({ top: 0, behavior: "smooth" });
},
removeToast(id) {
    const index = this.toasts.findIndex((t) => t.id === id);
    if (index !== -1) this.toasts[index].show = false;

    // Entferne nach Animation
    setTimeout(() => {
    this.toasts = this.toasts.filter((t) => t.id !== id);
    }, 4000);
},
});

// Alte Emit Calls global verfügbar
window.toastBus = toastBus;

export default {
name: "Toast",
components: { IconDone, IconExclamation, IconClose },
data() {
    return { toastBus };
},
mounted() {
//     console.log("[Toast.vue] mounted, initial toastBus:", this.toastBus);

    // Watch für Debug
    watch(
    () => this.toastBus.toasts,
//       (newVal) => console.log("[Toast.vue] toastBus.toasts changed:", newVal),
    { deep: true }
    );
},
methods: {
    removeToast(id) {
    this.toastBus.removeToast(id);
    },
    determineAlertClass(type) {
    switch (type) {
        case "success":
            return "border border-green-200 dark:border-green-800";
        case "points":
            return "border border-yellow-500 dark:border-yellow-1060 dark:text-yellow-1060"; // <- neu
        case "warning":
            return "border border-orange-200 dark:border-orange-800";
        case "info":
            return "border border-sky-200 dark:border-sky-800";
        case "error":
            return "border border-red-200 dark:border-red-800";
        default:
            return "border border-layout-sun-200 dark:border-layout-night-200";
    }
    },
},
};
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
transition: all 0.2s ease;
}
.toast-enter-from,
.toast-leave-to {
opacity: 0;
transform: translateY(-10px);
}
</style>
