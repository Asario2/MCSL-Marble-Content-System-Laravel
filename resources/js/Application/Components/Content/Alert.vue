<template>
    <div
        class="p-2 mb-4 text-sm rounded-lg ease-in-out duration-1000"
        :class="classTypeBackground"
        role="alert"
    >
        <div v-if="description">
            <div class="inline-block font-black mb-2 text-lg" v-if="title">
                {{ title }}
            </div>
            <div v-html="description" class="text-sm"></div>
        </div>
        <div v-else>
            <div class="inline-block font-black mb-2 text-lg" v-if="title">
                {{ title }}
            </div>
            <div class="text-sm">
                <slot></slot>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: "Components_Content_Alert",

    props: {
        title: {
            type: String,
            default: "Achtung!",
        },
        description: {
            type: String,
            default: "",
        },
        type: {
            type: String,
            default: "info",
        },
    },
    //
    computed: {
        classTypeBackground() {
            if (this.type == "error") {
                return "text-red-700 bg-red-100 dark:bg-red-700 dark:text-red-100";
            }
            if (this.type == "success") {
                return "text-green-700 bg-green-100 dark:bg-green-700 dark:text-green-100";
            }
            if (this.type == "warning") {
                return "text-orange-700 bg-orange-100 dark:bg-orange-700 dark:text-orange-100";
            }
            if (this.type == "dark") {
                return "text-layout-sun-700 bg-layout-sun-100 dark:bg-layout-night-100 dark:text-layout-night-700";
            }
            if (this.type == "info") {
                return "text-sky-700 bg-sky-100 dark:bg-sky-900 dark:text-sky-300";
            }
            return "text-sky-700 bg-sky-100 dark:text-sky-700 dark:bg-sky-100";
        },
    },
};
</script>

