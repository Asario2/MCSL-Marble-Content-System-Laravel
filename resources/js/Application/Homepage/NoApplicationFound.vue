<template>
    <layout

        :header-url="$page.props.saas_url + '/home/no_application_found'"
    >
    <MetaHeader title="Keine Anwendung" />
        <page-content>
            <template #content>
                <page-title>
                    <template #title>Keine Anwendung</template>
                </page-title>
                <page-paragraph>
                    <template #paragraph>
                        Es konnte keine Anwendung gefunden werden.
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
    name: "Homepage_NoApplicationFound",

    components: {
        Layout,
        PageContent,
        PageTitle,
        PageParagraph,MetaHeader},
});
</script>

