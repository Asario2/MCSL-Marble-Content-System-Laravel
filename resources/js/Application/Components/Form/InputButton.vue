<template>
    <button
        :type="type"
        class="inline-flex items-center px-2 py-1.5 md:px-4 md:py-2 h-6 md:h-8 rounded-md font-medium text-xs tracking-widest disabled:opacity-25 transition cursor-pointer focus:ring focus:outline-none button_bg button_text_case_bg"
    >
        <slot></slot>
    </button>
</template>

<script>
export default {
    name: "Contents_Form_InputButton",

    props: {
        type: {
            type: String,
            default: "submit",
        },
    },
};
</script>

