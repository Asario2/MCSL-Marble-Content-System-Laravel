<template>
    <Layout title="Login">
        <template #logo>
            <favicon class="h-16" />
        </template>

        <section-form>
            <template #form>
                <display-text>
                    <div>
                        Bitte bestätigen Sie Ihre E-Mail-Adresse mit dem
                        zugesandten Link, bevor Sie fortfahren. Sollten Sie
                        keine E-Mail erhalten haben, senden wir Ihnen diese
                        gerne erneut.
                    </div>

                    <div v-if="verificationLinkSent">
                        Ein neuer Bestätigungslink wurde an die E-Mail-Adresse,
                        die in Ihrem Profil hinterlegt ist, gesendet.
                    </div>
                </display-text>

                <input-group>
                    <input-container :full-width="true">
                        <display-hyperlink
                            :href="route('logout')"
                            method="post"
                            display-type="display"
                        >
                            Abmelden</display-hyperlink
                        >
                    </input-container>
                </input-group>
            </template>
            <template #actions>
                <!-- Befehle -->
                <button-group>
                    <input-button
                        type="button"
                        @click.prevent="submit"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        Bestätigungslink erneut senden
                    </input-button>
                </button-group>
            </template>
        </section-form>
    </Layout>
</template>
<script>
import { defineComponent } from "vue";

import { useForm } from "@inertiajs/vue3";

import Layout from "@/Application/Auth/Shared/Layout.vue";

import SectionForm from "@/Application/Components/Content/SectionForm.vue";

import Favicon from "@/Application/Components/Logo/Favicon.vue";

import DisplayHyperlink from "@/Application/Components/Content/DisplayHyperlink.vue";
import DisplayText from "@/Application/Components/Content/DisplayText.vue";

import InputGroup from "@/Application/Components/Form/InputGroup.vue";
import InputContainer from "@/Application/Components/Form/InputContainer.vue";

import ButtonGroup from "@/Application/Components/Form/ButtonGroup.vue";
import InputButton from "@/Application/Components/Form/InputButton.vue";
import InputWhiteButton from "@/Application/Components/Form/InputWhiteButton.vue";

export default defineComponent({
    name: "Auth_VerifyEmail",

    components: {
        Layout,
        SectionForm,
        Favicon,
        DisplayHyperlink,
        DisplayText,
        InputGroup,
        InputContainer,
        ButtonGroup,
        InputButton,
        InputWhiteButton,
    },

    props: {
        status: {
            type: String,
            default: null,
        },
    },

    data() {
        return {
            form: useForm({}),
        };
    },

    computed: {
        verificationLinkSent() {
            return this.status === "verification-link-sent";
        },
    },

    methods: {
        submit() {
            this.form.post(route("verification.send"));
        },
    },
});
</script>

