<template>
    <layout title="Profil">
        <template #header>
            <breadcrumb
                :application-name="$page.props.applications.app_admin_name"
                :start-page="false"
                current="Profil"
            ></breadcrumb>
        </template>
        <profile
            :confirmsTwoFactorAuthentication="confirmsTwoFactorAuthentication"
            :sessions="sessions"
        ></profile>
    </layout>
</template>

<script>
import { defineComponent } from "vue";

// import Layout from "@/Application/Admin/Shared/Layout" + SD()  +".vue";
import Breadcrumb from "@/Application/Components/Content/Breadcrumb.vue";
import Profile from "@/Application/Shared/Profile.vue";
import { defineAsyncComponent } from "vue";
import { SD } from "@/helpers";

export default defineComponent({
    name: "Admin_Profile",

    components: {

        Breadcrumb,
        Profile,
        Layout: defineAsyncComponent(() =>
        import(`@/Application/Admin/Shared/${SD()}/Layout.vue`)
    )

    },

    props: {
        sessions: {
            type: Array,
            default: () => [],
        },
        confirmsTwoFactorAuthentication: {
            type: Boolean,
            default: false,
        },
    },
});
</script>

