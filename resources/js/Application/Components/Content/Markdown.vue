<template>
    <div class="font-sans">
        <div
            v-html="markdown"
            class="max-w-none min-h-full"
        ></div>
    </div>
</template>
<script>
export default {
    name: "Components_Content_Markdown",
    //
    props: {
        markdown: {
            type: String,
            required: true,
        },
    },
};
</script>

