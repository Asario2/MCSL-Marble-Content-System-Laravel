<template>
    <div class="my-2 bg-layout-sun-200 dark:bg-layout-night-200 lg:rounded-lg">
        <div v-if="SD() != 'ab'" class="p-1 md:p-4 border border-layout-sun-1000 dark:border-layout-night-1050 lg:rounded-lg">
            <slot name="content" />
        </div>
        <div v-else class="p-1 md:p-4 lg:rounded-lg">
            <slot name="content" />
        </div>
    </div>
</template>

<script>
import { SD } from "@/helpers";

export default {
    name: "Components_Content_PageContent",
    methods: {
        SD
    }
};
</script>
