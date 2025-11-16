<template>
    <div
        class="flex items-center justify-center flex-wrap mt-6 -mb-1 text-xs md:text-base bg-transparent text-layout-sun-700 dark:text-layout-night-700"
    >
        <template v-for="(link, key) in links" :key="key">
            <div
                v-if="link.url === null"
                :key="key"
                class="flex items-center px-3 py-0.5 mx-1 mb-1 rounded-md cursor-not-allowed"
            >
                <span v-if="link.label === 'pagination.previous'"
                    >&laquo; Zurück</span
                >
                <span v-else-if="link.label === 'pagination.next'"
                    >Weiter &raquo;</span
                >
                <span v-else v-html="link.label"></span>
            </div>
            <Link
                v-if="link.url !== null"
                :key="key"
                class="flex items-center px-2.5 py-0.5 mx-1 mb-1 h-7 transition-colors duration-200 transform rounded-md border hover:bg-layout-sun-200 hover:text-layout-sun-800 dark:hover:bg-layout-night-200 dark:hover:text-layout-night-800"
                :class="{
                    'border-primary-sun-500 text-primary-sun-900 dark:border-primary-night-500 dark:text-primary-night-900':
                        link.active,
                }"
                :href="link.url"
                preserve-query
                preserve-state
                preserve-scroll
            >
                <span v-if="link.label === 'pagination.previous'"
                    >&laquo; Zurück</span
                >
                <span v-else-if="link.label === 'pagination.next'"
                    >Weiter &raquo;</span
                >
                <span v-else v-html="link.label" class=""></span>
            </Link>
        </template>
    </div>
</template>
<script>
import { Link } from "@inertiajs/vue3";

import IconChevronDoubleLeft from "@/Application/Components/Icons/ChevronDoubleLeft.vue";
import IconChevronDoubleRight from "@/Application/Components/Icons/ChevronDoubleRight.vue";
//
export default {
    name: "Contents_Lists_Pagination",
    //
    components: {
        Link,
        IconChevronDoubleLeft,
        IconChevronDoubleRight,
    },
    //
    props: {
        links: {
            type: Array,
            default: null,
        },
    },
};
</script>

