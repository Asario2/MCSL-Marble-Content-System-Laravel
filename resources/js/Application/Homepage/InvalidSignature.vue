<template>
    <layout
        header-title="Ungültige Signatur"
        :header-url="$page.props.saas_url + '/home/invalid_signature'"
    >

        <page-content>
            <template #content>
                <page-title>
                    <template #title>Ungültige Signatur</template>
                </page-title>

                <page-paragraph>
                    <template #paragraph>
                        Die Signatur der Anfrage ist ungültig oder die
                        Anfragezeit ist abgelaufen.
                    </template>
                </page-paragraph>

                <display-hyperlink
                    :href="route('verification.notice')"
                    display-type="display"
                >
                    zur Mail-Bestätigung</display-hyperlink
                >
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

import DisplayHyperlink from "@/Application/Components/Content/DisplayHyperlink.vue";

export default defineComponent({
    name: "Homepage_InvalidSignature",

    components: {
        Layout,
        PageContent,
        PageTitle,
        PageParagraph,
        DisplayHyperlink, MetaHeader},
});
</script>

