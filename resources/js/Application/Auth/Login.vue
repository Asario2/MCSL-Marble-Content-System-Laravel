<template>
    <Layout title="Login">
        <template #logo>
            <favicon class="h-16" />
        </template>

        <page-content v-if="status">
            <template #content>
                <alert type="info" title="Hinweis" :description="status"></alert
            ></template>
        </page-content>

        <page-content v-show="loading">
            <template #content>
                <InputLoading
                    :loading="loading"
                    :loadingText="loadingText"
                ></InputLoading
            ></template>
        </page-content>

        <section-form>
            <template #form>
                <!-- Liste der Fehler -->
                <error-list :errors="errors" />

                <input-group>
                    <input-container :full-width="true">
                        <input-label
                            name="email"
                            label="Mailadresse oder Nickname"
                        ></input-label>
                        <input-element
                            type="email"
                            name="email"
                            v-model="form.email"
                            placeholder="Mailadresse / Nickname"
                            autofocus
                            ref="email"
                        ></input-element>
                        <input-error :message="errors.email" />
                    </input-container>

                    <input-container :full-width="true">
                        <input-label
                            name="password"
                            label="Kennwort"
                        ></input-label>
                        <input-element
                            type="password"
                            name="password"
                            autocomplete="current-password"
                            v-model="form.password"
                            ref="password"
                        ></input-element>
                         <input-error :message="errors.password" />
                    </input-container>
                    <input-container :full-width="true">
                   <a href="/auth/google" class="flex items-center gap-2"><GoogleSVG he="22" wi="22"/>Mit Google anmelden</a>


                    </input-container>
                    <input-container :full-width="true">
                        <input-checkbox name="remember" v-model="form.remember">
                            Erinnere Dich an mich
                        </input-checkbox>
                    </input-container>

                    <input-container :full-width="true"  style='display:inline;'>
                        <display-hyperlink
                            :href="route('password.request')"
                            display-type="display"
                        >
                            Kennwort vergessen?</display-hyperlink
                        >
                    </input-container>
                    <input-container style='display:inline;'>
                        <display-hyperlink
                            :href="route('register')"
                            display-type="display"
                        >
                            Noch kein Nutzer?</display-hyperlink
                        >
                    </input-container>
                </input-group>
            <input type="hidden" name="redirect" value="{{ request('redirect') }}">
            </template>
            <template #actions>
                <!-- Befehle -->
                <button-group>
                    <input-button type="button" @click.prevent="loginUser">
                        Anmelden
                    </input-button>
                </button-group>
            </template>
        </section-form>
    </Layout>
</template>
<script>
import { defineComponent } from "vue";

import Layout from "@/Application/Auth/Shared/Layout.vue";

import PageContent from "@/Application/Components/Content/PageContent.vue";
import SectionForm from "@/Application/Components/Content/SectionForm.vue";
import GoogleSVG from "@/Application/Components/Icons/GoogleSVG.vue";
import Favicon from "@/Application/Components/Logo/Favicon.vue";
import Alert from "@/Application/Components/Content/Alert.vue";

import DisplayHyperlink from "@/Application/Components/Content/DisplayHyperlink.vue";

import InputLoading from "@/Application/Components/Form/InputLoading.vue";
import ErrorList from "@/Application/Components/Form/ErrorList.vue";
import InputGroup from "@/Application/Components/Form/InputGroup.vue";
import InputContainer from "@/Application/Components/Form/InputContainer.vue";
import InputLabel from "@/Application/Components/Form/InputLabel.vue";
import InputElement from "@/Application/Components/Form/InputElement.vue";
import InputCheckbox from "@/Application/Components/Form/InputCheckbox.vue";
import InputError from "@/Application/Components/Form/InputError.vue";

import ButtonGroup from "@/Application/Components/Form/ButtonGroup.vue";
import InputButton from "@/Application/Components/Form/InputButton.vue";

export default defineComponent({
    name: "Auth_Login",

    components: {
        Layout,
        PageContent,
        SectionForm,
        Favicon,
        DisplayHyperlink,
        Alert,
        InputLoading,
        ErrorList,
        GoogleSVG,
        InputGroup,
        InputContainer,
        InputLabel,
        InputElement,
        InputCheckbox,
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
                remember: false,
            },
        };
    },

    methods: {
        loginUser() {
            if (!this.form.email || !this.form.password) {
                alert("Bitte fülle alle Felder aus.");
                return;
            }
            let routeLogin = "login";
            // Vorbereitung der Daten, einschließlich der Transformation des `remember`-Feldes
            const formData = {
                ...this.form,
                remember: this.form.remember ? "on" : "",
                 redirect: $_GET['redirect'],
            };
            //
            localStorage.setItem('reload_dashboard', '1');
            this.loading = true;
            this.loadingText = "Die Anmeldung wird durchgeführt!";
            //s
            this.$inertia.post(this.route(routeLogin), formData, {
                onFinish: () => {
                    this.loading = false;
                    this.form.password = ""; // Passwort zurücksetzen nach dem Absenden

                },
            });
        },
    },
});
</script>

