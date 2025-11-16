<template>
    <span
        class="z-50 flex items-right font-bold ckoo flex-nowrap cursor-pointer w-full px-4 py-2 text-sm leading-4 font-medium transition focus:outline-none hover:underline dark:bg-layout-night-50 text-layout-sun-700 hover:text-layout-sun-900 dark:text-layout-night-700 dark:hover:text-layout-night-900"
    >
        <template v-if="withRoute">
            <a
                :href="routeName"
                :target="withTarget ? '_blank' : '_self'"
                v-tippy
            >
                <span class="font-bold">
                    <slot></slot>
                </span>
                <span v-if="withIcon">
                    <icon-chevron-down class="ml-2 h-4 w-4" />
                </span>
            </a>
            <tippy v-if="tippyText">{{ tippyText }}</tippy>
        </template>
        <template v-else>
            <span>
                <slot></slot>
            </span>
            <span v-if="withIcon">
                <icon-chevron-down class="ml-2 h-4 w-4" />
            </span>
        </template>
    </span>
</template>

<script>
import { Link } from "@inertiajs/vue3";

import IconChevronDown from "@/Application/Components/Icons/ChevronDown.vue";

export default {
    name: "Components_Content_DropdownLink",

    components: {
        Link,
        IconChevronDown,
    },

    props: {
        routeName: {
            type: String,
            default: "",
        },
        withIcon: {
            type: Boolean,
            default: false,
        },
        withRoute: {
            type: Boolean,
            default: false,
        },
        withTarget: {
            type: Boolean,
            default: false,
        },
        tippyText: {
            type: String,
            default: null,
        },
    },
};
</script>

