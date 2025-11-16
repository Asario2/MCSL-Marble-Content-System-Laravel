<template>
    <div>
        <div v-if="displayType == 'button'">
            <a
                :href="href"
                :target="target"
                class="inline-flex tb items-center px-1 py-1.5 md:px-2 md:py-2 h-6 md:h-8 rounded-md font-medium text-xs tracking-widest disabled:opacity-25 transition cursor-pointer focus:ring focus:outline-none button_bg button_text_case_bg"
            >
                <div class="flex items-center whitespace-nowrap">
                    <slot name="icon"></slot>
                    <slot></slot>
                </div>
            </a>
        </div>
        <div v-if="displayType == 'table'">
            <a
                :href="href"
                :target="target"
                class="inline-flex items-center px-1.5 py-0.5 h-8 rounded-md font-medium text-xs tracking-widest disabled:opacity-25 transition cursor-pointer focus:ring focus:outline-none button_border button_text_case_border"
            >
                <slot name="icon"></slot>
                <slot></slot>
            </a>
        </div>
        <div v-if="displayType == 'table_danger'">
            <a
                :href="href"
                :target="target"
                class="inline-flex items-center px-1.5 py-0.5 h-8 rounded-md font-medium text-xs tracking-widest disabled:opacity-25 transition cursor-pointer focus:ring focus:outline-none button_danger_border button_danger_text_case_border"
            >
                <slot name="icon"></slot>
                <slot></slot>
            </a>
        </div>
        <div v-if="displayType == 'display'">
            <a
                :href="href"
                :target="target"
                class="inline-flex items-center px-0.5 py-1 h-6 rounded-md font-medium text-xs tracking-widest disabled:opacity-25 transition cursor-pointer focus:ring focus:outline-none border border-transparent button_text_case_border"
            >
                <slot name="icon"></slot>
                <slot></slot>
            </a>
        </div>
    </div>
</template>

<script>
export default {
    name: "Contents_Form_InputIconHyperlink",

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
    },
};
</script>

