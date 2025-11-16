<template>
    <div class="font-sans">
        <div
            v-html="markdown"
            class="max-w-none min-h-full prose md:prose-md dark:prose-invert prose-headings:font-title prose-pre:bg-layout-sun-100 prose-pre:text-layout-sun-800 dark:prose-pre:bg-layout-night-100 dark:prose-pre:text-layout-night-800"
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

