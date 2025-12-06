<template>
    <Layout>
        <PersonalProfile :initialForm="userData" :regi="true" />
    </Layout>
</template>

<script>
import Layout from "@/Application/Auth/Shared/Layout.vue";
import { page } from '@inertiajs/inertia-vue3';
import PersonalProfile from "@/Application/Shared/PersonalProfile.vue";

export default {
    name: "AuthNickName",
    components: {
        Layout,
        PersonalProfile,
    },

    data() {
        return {
            userData: {},
        };
    },

    created() {
       this.userData = page?.props?.user || [];
       console.log(this.userData);
    },
};
</script>
