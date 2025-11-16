<template>
    <Layout title="Kennwort zurücksetzen">
        <template #logo>
            <Favicon class="h-16" />
        </template>

        <SectionForm>
            <template #form>
                <PageContent v-if="loading">
                    <template #content>
                        <InputLoading
                            :loading="loading"
                            :loadingText="loadingText"
                        ></InputLoading
                    ></template>
                </PageContent>

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
                            autocomplete="username"
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
                            v-model="form.password"
                            ref="password"
                        ></input-element>
                        <input-error :message="errors.password" />
                    </input-container>

                    <input-container :full-width="true">
                        <input-label
                            name="password_confirmation"
                            label="Kennwortbestätigung"
                        ></input-label>
                        <input-element
                            type="password"
                            name="password_confirmation"
                            v-model="form.password_confirmation"
                            ref="password_confirmation"
                        ></input-element>
                        <input-error :message="errors.password_confirmation" />
                    </input-container>
                </input-group>
            </template>
            <template #actions>
                <!-- Befehle -->
                <button-group>
                    <input-button
                        type="button"
                        @click.prevent="resetPasswordUser"
                    >
                        Kennwort zurücksetzen
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
    name: "Auth_ResetPassword",

    components: {
        Layout,
        PageContent,
        SectionForm,
        Favicon,
        DisplayText,
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
        errors: {
            type: Object,
            default: () => ({}),
        },
        token:{
            type:String,
        }
    },

    data() {
        return {
            loading: false,
            loadingText: null,
            //
            form: {
                email: "",
                password: "",
                password_confirmation: "",
                token: this.token,
            },
        };
    },
    mounted(){
        const pathParts = window.location.pathname.split('/');
        // Beispiel: ["", "reset-password", "d9f8ff59544973ec48f6beed15a672d851540ebca17aca8bd039fb3e99adc6c9"]
        const token = pathParts[2];

    },
    methods: {
        resetPasswordUser() {
            let routeResetPassword = "password.store"
            //

            this.loading = true;
            this.loadingText = "Das neue Kennwort wird gespeichert!";
            //
            // console.log(this.form);
            // return;
            this.$inertia.post(this.route(routeResetPassword), this.form, {
                onFinish: () => {
                    this.loading = false;
                    this.form.password = "";
                    this.form.password_confirmation = "";
                },
            });
        },
    },
});
</script>

