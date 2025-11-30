<template>
    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div v-if="$page.props.jetstream.canUpdateProfileInformation">
                <update-profile-information-form
                    :user="$page.props.auth.user"
                />
                <section-border />
                <PersonalProfile :initialForm="$page.props.auth.user">
                </PersonalProfile>
                <section-border />
            </div>
            <div v-if="pmdel == '1'">

                <MessageSettings>

                </MessageSettings>
                <section-border />
            </div>
            <span v-else>asdads</span>
            <div v-if="$page.props.jetstream.canUpdatePassword">
                <update-password-form class="mt-10 sm:mt-0" />

                <section-border />
            </div>

            <span v-else></span>

            <div v-if="$page.props.jetstream.canManageTwoFactorAuthentication">
                <two-factor-authentication-form
                    :requires-confirmation="confirmsTwoFactorAuthentication"
                    class="mt-10 sm:mt-0"
                />

                <section-border />
            </div>


            <logout-other-browser-sessions-form
                :sessions="sessions"
                class="mt-10 sm:mt-0"
            />

            <template v-if="$page.props.jetstream.hasAccountDeletionFeatures">
                <section-border />

                <delete-user-form class="mt-10 sm:mt-0" />
            </template>
        </div>
    </div>
</template>

<script>
import { defineComponent } from "vue";
import { CheckTRights } from "@/helpers";
import DeleteUserForm from "@/Application/Shared/DeleteUserForm.vue";
import LogoutOtherBrowserSessionsForm from "@/Application/Shared/LogoutOtherBrowserSessionsForm.vue";
import TwoFactorAuthenticationForm from "@/Application/Shared/TwoFactorAuthenticationForm.vue";
import PersonalProfile from "@/Application/Shared/PersonalProfile.vue";
import UpdatePasswordForm from "@/Application/Shared/UpdatePasswordForm.vue";
import MessageSettings from "@/Application/Shared/MessageSettings.vue";
import UpdateProfileInformationForm from "@/Application/Shared/UpdateProfileInformationForm.vue";
// import ProfileTextfield from "@/Application/Shared/ProfileTextfield.vue";

import SectionBorder from "@/Application/Components/Content/SectionBorder.vue";

export default defineComponent({
    name: "Shared_Profile",

    components: {
        DeleteUserForm,
        LogoutOtherBrowserSessionsForm,
        TwoFactorAuthenticationForm,
        UpdatePasswordForm,
        UpdateProfileInformationForm,
        SectionBorder,
        // ProfileTextfield,
        PersonalProfile,
        MessageSettings,
    },
    data(){
    return {
        pmdel: 0,
    };
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
    async mounted() {
    this.pmdel = await CheckTRights('delete', 'private_messages');
    alert(this.pmdel);
    },
    methods:{
        CheckTRights,
    }
});
</script>

