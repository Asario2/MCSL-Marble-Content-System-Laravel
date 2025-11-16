<template>
    <div class="md:col-span-1 flex justify-between">
        <div class="px-4 sm:px-0">
            <h3
                class="text-lg font-medium text-layout-sun-900 dark:text-layout-night-900"
            >
                <slot name="title" />
            </h3>

            <p
                class="mt-1 text-sm text-layout-sun-600 dark:text-layout-night-600"
            >
                <slot name="description" />
            </p>
        </div>
    </div>
</template>

<script>
export default {
    name: "Components_Content_SectionTitle",
};
</script>

