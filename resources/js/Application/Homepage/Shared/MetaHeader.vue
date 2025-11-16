<template>
    <Head :title="title">
        <slot name="robots"></slot>
        <slot name="description"></slot>
        <slot name="opengraph"></slot>
        <slot name="twitter"></slot>
    </Head>
</template>
<script>
import { Head } from "@inertiajs/vue3";

export default {
    name: "Homepage_Shared_MetaHeader",
    //
    components: {
        Head,
    },
    //
    props: {
        title: {
            type: String,
            default:
                "Willkommen im Laravel-Template Startet Eleven von Oliver Reinking.",
        },
    },
};
</script>

