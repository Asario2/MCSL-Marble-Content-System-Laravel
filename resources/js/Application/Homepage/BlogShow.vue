<template>
    <layout
        header-title="Asarios Blog"
        :header-description="blog.description"
        :header-url="$page.props.saas_url + 'blogs/show/' + blog.autopslug"
        :header-image="$page.props.saas_url + 'big/' + blog.blog_images?.url"
    >
    <MetaHeader title="Asarios BLog" />
    <blog-show :blog="blog" :blogarticle="blogarticle"></blog-show>
    </layout>
</template>
<script>
import { defineComponent } from "vue";
// import { Link } from "@inertiajs/vue3";
import Layout from "@/Application/Homepage/Shared/Layout.vue";

import BlogShow from "@/Application/Homepage/Shared/BlogShow.vue";
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
export default defineComponent({
    name: "Homepage_BlogShow",

    components: {
        // Link,
        Layout,
        BlogShow,
        MetaHeader,
    },

    props: {
        blog: {
            type: Object,
            default: () => ({}),
        },
        blogarticle: {
            type: String,
            default: null,
        },
    },
});
</script>

