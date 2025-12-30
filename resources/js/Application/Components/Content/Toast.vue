<template>
    <!-- Toast -->
    <div v-if="message && show" class="mt-4">
        <div
            class="flex items-center w-full p-4 rounded-lg shadow text-layout-sun-900 dark:text-layout-night-900"
            :class="alertClass"
            role="alert"
        >
            <div
                v-if="type === 'success'"
                class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg text-layout-sun-900 bg-layout-sun-0 dark:text-layout-night-900 dark:bg-layout-night-0"
            >
                <icon-done class="w-5 h-5"></icon-done>
            </div>
             <div
                v-if="type === 'points'"
                class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg text-layout-sun-900 dark:bg-yellow-1060 text-layout-sun-200 bg-text-yellow-1060  font-bold dark:bg-layout-night-200"
                title="Punkte erhalten"
                aria-label="Punkte erhalten"
            >
                <icon-done class="w-5 h-5"></icon-done>
            </div>

            <div
                v-if="type === 'info'"
                class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg text-layout-sun-900 bg-layout-sun-0 dark:text-layout-night-900 dark:bg-layout-night-0"
            >
                <icon-done class="w-5 h-5" fill="currentColor"></icon-done>
            </div>
            <div
                v-if="type === 'warning'"
                class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg text-layout-sun-900 bg-orange-500 dark:text-layout-night-900 dark:bg-orange-500"
            >
                <icon-exclamation class="w-5 h-5"></icon-exclamation>
            </div>
            <div
                v-if="type === 'error'"
                class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg text-layout-sun-900 bg-red-500 dark:text-layout-night-900 dark:bg-red-500"
            >
                <icon-exclamation class="w-5 h-5"></icon-exclamation>
            </div>
            <div class="ml-3 text-base font-normal">
                <span v-html="message"></span>
            </div>
            <button
                type="button"
                class="ml-auto -mx-1.5 -my-1.5 rounded-full p-1.5 inline-flex h-8 w-8 focus:ring-2 text-layout-sun-700 hover:text-layout-sun-0 hover:bg-layout-sun-1000 focus:ring-layout-sun-300 dark:text-layout-night-700 dark:hover:text-layout-night-0 dark:hover:bg-layout-night-1000 dark:focus:ring-layout-night-300 border border-layout-sun-500 dark:border-layout-sun-500"
                @click.prevent="show = false"
            >
                <span class="sr-only">Close</span>
                <icon-close class="w-5 h-5"></icon-close>
            </button>
        </div>
    </div>
    <!-- ENDS Toast -->
</template>

<script>

import IconDone from "@/Application/Components/Icons/Done.vue";
import { toastBus } from '@/utils/toastBus';
import IconExclamation from "@/Application/Components/Icons/Exclamation.vue";
import IconClose from "@/Application/Components/Icons/Close.vue";

export default {
    name: "Components_Content_Toast",

    components: {
        IconDone,
        IconExclamation,
        IconClose,
        toastBus,
    },

    data() {
        return {
            show: false,
            type: "success",
            message: null,
            alertClass: "",
        };
    },

    mounted() {
        toastBus.on('toast', this.showToast);
    },
    beforeUnmount() {
        toastBus.off('toast', this.showToast);
    },

    methods: {
        showToast(payload) {
            // console.log('Toast payload:', payload);  // Prüfe, ob die Daten hier ankommen
            this.type = payload.status || 'info';
            console.log(this.type);
            this.message = payload.message;
            this.alertClass = this.determineAlertClass(this.type);
            this.show = true;
            window.scrollTo({ top: 0, behavior: 'smooth' });
            setTimeout(() => this.show = false, 400000); // auto-hide nach 4 Sekunden
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

