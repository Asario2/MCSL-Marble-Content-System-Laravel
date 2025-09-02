<template>
<layout>
    <MetaHeader title="EXAMPLE" />

        <img :src="'/images/web/mcsl_logo_only.png'" class="mx-auto" style="max-width:500px;min-width:300px;width:100%" />
        <div class="mx-auto text-center" style="max-width: 800px;">
        <br />
        <br />
            <h2 class="text-4xl">{{texts.headline}}</h2>
        <br />
        <br />
            <p v-html="texts.text"></p>
    </div>
</layout>
</template>
<script>
import Layout from "@/Application/Homepage/Shared/Layout.vue";
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";

export default {
components: {
    Layout, MetaHeader},
props:{
    texts:{
        type: [Array,Object],
        required: true,
    }

}
}
</script>

