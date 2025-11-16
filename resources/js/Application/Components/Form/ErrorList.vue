<template>
    <div
        v-if="Object.keys(errors).length > 0"
        class="flex items-start p-4 mb-6 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800"
        role="alert"
    >
        <div
            class="h-10 w-10 mr-3 flex items-center justify-center rounded-full bg-gradient-to-b from-red-100 to-red-400 text-red-700 animate-pulse"
        >
            <icon-exclamation-circle class="h-8 w-8"></icon-exclamation-circle>
        </div>
        <div class="flex flex-col">
            <div class="text-lg font-semibold mb-2">Fehler</div>
            <div v-for="(error, index) in errors" :key="index">
                <p class="text-red-700 dark:text-red-300 leading-tight">
                    {{ error }}
                </p>
            </div>
        </div>
    </div>
</template>
<script>
import IconExclamationCircle from "@/Application/Components/Icons/ExclamationCircle.vue";

export default {
    name: "Contents_Form_ErrorList",
    components: {
        IconExclamationCircle,
    },
    props: {
        errors: {
            type: Object,
            default: () => [],
        },
    },
};
</script>

