<template>
    <div v-if="displayType == 'button'">
        <Link
            :href="href"
            :target="target"
            :method="method"
            class="inline-flex items-center px-2 py-1.5 md:px-4 md:py-2 h-6 md:h-8 rounded-md font-medium text-xs tracking-widest disabled:opacity-25 transition cursor-pointer focus:ring focus:outline-none button_bg button_text_case_bg"
        >
            <div class="flex items-center whitespace-nowrap">
                <slot name="icon"></slot>
                <slot></slot>
            </div>
        </Link>
    </div>
    <div v-if="displayType == 'table'">
        <Link
            :href="href"
            :target="target"
            :method="method"
            class="inline-flex items-center px-1.5 py-0.5 h-8 rounded-md font-medium text-xs tracking-widest disabled:opacity-25 transition cursor-pointer focus:ring focus:outline-none button_border button_text_case_border"
        >
            <slot name="icon"></slot>
            <slot></slot>
        </Link>
    </div>
    <div v-if="displayType == 'table_danger'">
        <Link
            :href="href"
            :target="target"
            :method="method"
            class="inline-flex items-center px-1.5 py-0.5 h-8 rounded-md font-medium text-xs tracking-widest disabled:opacity-25 transition cursor-pointer focus:ring focus:outline-none button_danger_border button_danger_text_case_border"
        >
            <slot name="icon"></slot>
            <slot></slot>
        </Link>
    </div>
    <div v-if="displayType == 'display'">
        <Link
            :href="href"
            :target="target"
            :method="method"
            class="inline-flex items-center px-0.5 py-1 h-6 rounded-md font-medium text-xs tracking-widest disabled:opacity-25 transition cursor-pointer focus:ring focus:outline-none border border-transparent button_text_case_border"
        >
            <div class="flex items-center whitespace-nowrap">
                <slot name="icon"></slot>
                <slot></slot>
            </div>
        </Link>
    </div>
    <span v-if="displayType == 'inline'">
        <Link
            :href="href"
            :target="target"
            :method="method"
            class="underline text-sm text-layout-sun-600 hover:text-layout-night-600"
        >
            <slot></slot>
        </Link>
    </span>
</template>

<script>
import { Link } from "@inertiajs/vue3";

export default {
    name: "Components_Content_DisplayHyperlink",

    components: {
        Link,
    },

    props: {
        href: {
            type: String,
            required: true,
        },
        target: {
            type: String,
            default: "",
        },
        displayType: {
            type: String,
            default: "button",
        },
        method: {
            type: String,
            default: "get",
        },
    },
};
</script>

