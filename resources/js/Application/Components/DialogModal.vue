<template>
    <modal
        :show="show"
        :max-width="maxWidth"
        :closeable="closeable"
        @close="close"
    >
        <div class="px-6 py-4">
            <div class="text-lg">
                <slot name="title"> </slot>
            </div>

            <div class="mt-4 text-base">
                <slot name="description"> </slot>
            </div>

            <slot name="loading"> </slot>

            <div class="mt-4">
                <slot name="content"> </slot>
            </div>
        </div>

        <div
            class="px-6 py-4 bg-layout-sun-100 dark:bg-layout-night-100 text-right"
        >
            <slot name="footer"> </slot>
        </div>
    </modal>
</template>

<script>
import Modal from "@/Application/Components/Modal.vue";

export default {
    name: "Components_DialogModal",

    emits: ["close"],

    components: {
        Modal,
    },

    props: {
        show: {
            default: false,
        },
        maxWidth: {
            default: "2xl",
        },
        closeable: {
            default: true,
        },
    },

    methods: {
        close() {
            this.$emit("close");
        },
    },
};
</script>

