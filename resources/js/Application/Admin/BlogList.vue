<template>
    <layout>
        <template #header>
            <breadcrumb
                :application-name="$page.props.applications.app_admin_name"
                :start-page="false"
                current="Liste der Blogartikel"
            ></breadcrumb>
        </template>
        <!-- Anzeige der Blogartikelliste -->
        <section class="mt-8">
            <list-container
                title="Liste der Blogartikel"
                :datarows="blogs"
                route-index="admin.blog.index"
                :filters="filters"
                :search-filter="true"
                search-text="Gesucht werden alle Blogartikel, die den Suchbegriff im Titel, in der Zusammenfassung bzw. im Artikel selbst enthalten."
                :edit-on="true"
                route-edit="admin.blog.edit"
                :create-on="true"
                route-create="admin.blog.create"
            >
                <template #header>
                    <tr>
                        <th class="np-dl-th-normal">Datum</th>
                        <th class="np-dl-th-normal">Titel</th>
                        <th class="np-dl-th-normal">Autor</th>
                        <th class="np-dl-th-normal">Markdown-Format?</th>
                    </tr>
                </template>
                <template v-slot:datarow="data">
                    <td class="np-dl-td-normal">
                        <display-date
                            :value="data.datarow.blog_date"
                            :time-on="false"
                        ></display-date>
                    </td>
                    <td class="np-dl-td-normal">
                        {{ data.datarow.title }}
                    </td>
                    <td class="np-dl-td-normal">
                        {{ data.datarow.author_name }}
                    </td>
                    <td class="np-dl-td-normal">
                        <display-yes-or-no
                            :value="data.datarow.markdown_on"
                        ></display-yes-or-no>
                    </td>
                </template>
            </list-container>
        </section>
    </layout>
</template>
<script>
import { defineComponent } from "vue";

import Layout from "@/Application/Admin/Shared/Layout.vue";
import Breadcrumb from "@/Application/Components/Content/Breadcrumb.vue";

import ListContainer from "@/Application/Components/Lists/ListContainer.vue";

import DisplayDate from "@/Application/Components/Content/DisplayDate.vue";
import DisplayYesOrNo from "@/Application/Components/Content/DisplayYesOrNo.vue";

export default defineComponent({
    name: "Admin_BlogList",

    components: {
        Layout,
        Breadcrumb,
        ListContainer,
        DisplayDate,
        DisplayYesOrNo,
    },

    props: {
        blogs: {
            type: Object,
            default: () => ({}),
        },
        filters: {
            type: Object,
            default: () => ({}),
        },
    },
});
</script>

