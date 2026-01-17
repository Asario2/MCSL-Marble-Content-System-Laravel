<template>
    <Layout>
        <MetaHeader title="Profil" />
        <PersonalProfile :initialForm="userData" :regi="true" />
    </Layout>
</template>

<script>
import Layout from "@/Application/Auth/Shared/Layout.vue";
import { page } from '@inertiajs/inertia-vue3';
import PersonalProfile from "@/Application/Shared/PersonalProfile.vue";
import MetaHeader from "../Homepage/Shared/MetaHeader.vue";
export default {
    name: "AuthNickName",
    components: {
        Layout,
        PersonalProfile,
        MetaHeader,
    },

    data() {
        return {
            userData: {},
        };
    },

    created() {
       this.userData = page?.props?.user || [];
//        console.log(this.userData);
    },
};
</script>
