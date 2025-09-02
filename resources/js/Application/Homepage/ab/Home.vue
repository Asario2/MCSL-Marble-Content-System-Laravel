<template>
    <layout header-title="Home" :header-url="$page.props.saas_url + '/'">
    <MetaHeader title="Home" />
        <page-content>
            <template #content>
                <page-title>
                    <template #title>Start-Eleven</template>
                </page-title>
                <page-paragraph>
                    <template #paragraph>
                        Das Template <b>Template Starter-Eleven</b> von Oliver
                        Reinking ist ein möglicher Startpunkt für
                        SaaS-Anwendungen.
                    </template>
                </page-paragraph>
            </template>
        </page-content>
    </layout>
</template>
<script>
import { defineComponent } from "vue";
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
import Layout from "@/Application/Homepage/Shared/Layout.vue";

import PageContent from "@/Application/Components/Content/PageContent.vue";
import PageTitle from "@/Application/Components/Content/PageTitle.vue";
import PageParagraph from "@/Application/Components/Content/PageParagraph.vue";

export default defineComponent({
    name: "Homepage_Home",

    components: {
        Layout,
        PageContent,
        PageTitle,
        PageParagraph,
        MetaHeader
    },
});
</script>
