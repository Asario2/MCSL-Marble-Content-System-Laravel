<template>
    <div>
        <button
            v-if="as == 'button'"
            :class="classes"
            class="w-full text-start"
        >
            <slot />
        </button>

        <Link v-else :href="href" :class="classes" :target="target">
            <slot />
        </Link>
    </div>
</template>
<script>
import { Link } from "@inertiajs/vue3";

export default {
    name: "Components_Content_ResponsiveNavLink",

    props: {
        active: {
            type: Boolean,
            default: null,
        },
        href: {
            type: String,
            default: null,
        },
        as: {
            type: String,
            default: null,
        },
        target: {
            type: String,
            default: null,
        },
    },

    components: {
        Link,
    },

    computed: {
        classes() {
            return this.active
                ? "block w-full ps-3 pe-4 py-2 border-l-4 border-sky-400 dark:border-sky-600 text-start text-base font-medium text-sky-700 dark:text-sky-300 bg-sky-50 dark:bg-sky-900/50 focus:outline-none focus:text-sky-800 dark:focus:text-sky-200 focus:bg-sky-100 dark:focus:bg-sky-900 focus:border-sky-700 dark:focus:border-sky-300 transition duration-150 ease-in-out"
                : "block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600 focus:outline-none focus:text-gray-800 dark:focus:text-gray-200 focus:bg-gray-50 dark:focus:bg-gray-700 focus:border-gray-300 dark:focus:border-gray-600 transition duration-150 ease-in-out";
        },
    },
};
</script>

