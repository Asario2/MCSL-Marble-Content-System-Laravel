<template>
    <label
        :for="name"
        class="w-full block text-sm font-medium text-layout-sun-900 dark:text-layout-night-900"
        v-tippy
    >
        <div class="inline-flex items-center">
            <div v-if="label">{{ label }}</div>
            <div v-else><slot></slot></div>
            <icon-exclamation-circle
                v-if="helptext"
                class="inline-block ml-1 w-5 h-5 mt-4"
            ></icon-exclamation-circle>
        </div>
    </label>
    <tippy v-if="helptext" v-html="helptext" placement="left"></tippy>
</template>

<script>
import IconExclamationCircle from "@/Application/Components/Icons/ExclamationCircle.vue";

export default {
    name: "InputLabel",

    components: {
        IconExclamationCircle,
    },

    props: {
        name: {
            type: String,
            default: "xxx",
        },
        label: {
            type: String,
            default: "",
        },
        helptext: {
            type: String,
            default: "",
        },
    },
};
</script>
