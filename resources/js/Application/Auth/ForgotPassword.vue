<template>
    <Layout title="Kennwort vergessen">
        <template #logo>
            <Favicon class="h-16" />
        </template>

        <SectionForm>
            <template #form>
                <PageContent v-if="status">
                    <template #content>
                        <Alert
                            type="info"
                            title="Hinweis"
                            :description="status"
                        ></Alert
                    ></template>
                </PageContent>

                <PageContent v-if="loading">
                    <template #content>
                        <InputLoading
                            :loading="loading"
                            :loadingText="loadingText"
                        ></InputLoading
                    ></template>
                </PageContent>

                <display-text>
                    Haben Sie Ihr Passwort vergessen? Kein Problem. Teilen Sie
                    uns einfach Ihre E-Mail-Adresse mit und wir senden Ihnen per
                    E-Mail einen Link zum Zurücksetzen des Passworts, über den
                    Sie ein Neues auswählen können.
                </display-text>

                <!-- Liste der Fehler -->
                <error-list :errors="errors" />

                <input-group>
                    <input-container :full-width="true">
                        <input-label
                            name="email"
                            label="Mailadresse"
                        ></input-label>
                        <input-element
                            type="email"
                            name="email"
                            v-model="form.email"
                            placeholder="Mailadresse"
                            ref="email"
                        ></input-element>
                        <input-error :message="errors.email" />
                    </input-container>
                </input-group>
            </template>
            <template #actions>
                <!-- Befehle -->
                <button-group>
                    <input-button
                        type="button"
                        @click.prevent="forgotPasswordUser"
                    >
                        Link zum Zurücksetzen des Passwortes zusenden
                    </input-button>
                </button-group>
            </template>
        </SectionForm>
    </Layout>
</template>
<script>
import { defineComponent } from "vue";

import Layout from "@/Application/Auth/Shared/Layout.vue";

import PageContent from "@/Application/Components/Content/PageContent.vue";
import SectionForm from "@/Application/Components/Content/SectionForm.vue";

import Favicon from "@/Application/Components/Logo/Favicon.vue";
import Alert from "@/Application/Components/Content/Alert.vue";

import DisplayText from "@/Application/Components/Content/DisplayText.vue";

import InputLoading from "@/Application/Components/Form/InputLoading.vue";
import ErrorList from "@/Application/Components/Form/ErrorList.vue";
import InputGroup from "@/Application/Components/Form/InputGroup.vue";
import InputContainer from "@/Application/Components/Form/InputContainer.vue";
import InputLabel from "@/Application/Components/Form/InputLabel.vue";
import InputElement from "@/Application/Components/Form/InputElement.vue";
import InputError from "@/Application/Components/Form/InputError.vue";

import ButtonGroup from "@/Application/Components/Form/ButtonGroup.vue";
import InputButton from "@/Application/Components/Form/InputButton.vue";

export default defineComponent({
    name: "Auth_ForgotPassword",

    components: {
        Layout,
        PageContent,
        SectionForm,
        Favicon,
        DisplayText,
        Alert,
        InputLoading,
        ErrorList,
        InputGroup,
        InputContainer,
        InputLabel,
        InputElement,
        InputError,
        ButtonGroup,
        InputButton,
    },

    props: {
        status: {
            type: String,
            default: null,
        },
        errors: {
            type: Object,
            default: () => ({}),
        },
    },

    data() {
        return {
            loading: false,
            loadingText: null,
            //
            form: {
                email: "",
                password: "",
            },
        };
    },

    methods: {
        forgotPasswordUser() {
            let routeForgotPassword = "password.email";
            //
            this.loading = true;
            this.loadingText =
                "Die Mail wird vorbereitet und an Sie versendet!";
            //
            this.$inertia.post(this.route(routeForgotPassword), this.form, {
                onFinish: () => {
                    this.loading = false;
                },
            });
        },
    },
});
</script>

