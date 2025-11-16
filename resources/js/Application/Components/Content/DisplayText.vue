<template>
    <div
        class="mt-4 mb-2 text-sm text-layout-sun-600 dark:text-layout-night-600"
    >
        <span v-if="value">{{ value }}</span>
        <span v-else><slot></slot></span>
    </div>
</template>
<script>
export default {
    name: "Components_Content_DisplayText",

    props: {
        value: {
            type: String,
        },
    },
};
</script>

