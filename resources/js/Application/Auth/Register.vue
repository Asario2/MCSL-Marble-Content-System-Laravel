<template>
    <Layout title="Registrierung">
        <template #logo>
            <Favicon class="h-16" />
        </template>

        <PageContent v-if="loading">
            <template #content>
                <InputLoading
                    :loading="loading"
                    :loadingText="loadingText"
                ></InputLoading
            ></template>
        </PageContent>

        <SectionForm>
            <template #form>
                <!-- Liste der Fehler -->
                <error-list :errors="errors" />

                <input-group>
                <input-container :full-width="true">
                        <input-label
                            name="name"
                            label="Nickname"
                        ></input-label>
                        <input-element
                            type="text"
                            name="name"
                            v-model="form.name"
                            placeholder="Nickname"
                            ref="name"
                        ></input-element>
                        <input-error :message="errors.name" />
                    </input-container>
                <input-container :full-width="true">
                        <input-label
                            name="first_name"
                            label="Vorname"
                        ></input-label>
                        <input-element
                            type="text"
                            name="first_name"
                            v-model="form.first_name"
                            placeholder="Vorname"
                            ref="first_name"
                        ></input-element>
                        <input-error :message="errors.first_name" />
                    </input-container>

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

                    <input-container :full-width="true">
                        <input-checkbox name="terms" v-model="form.terms">
                            <div class="ml-2">
                                Ich akzeptiere die
                                <input-display
                                    target="_blank"
                                    :href="route('home.terms')"
                                    display_type="inline"
                                >
                                    Nutzungsbedingungen</input-display
                                >

                                und die
                                <input-display
                                    target="_blank"
                                    :href="route('home.privacy')"
                                    display_type="inline"
                                >
                                    Datenschutzerklärung</input-display
                                >
                            </div>
                        </input-checkbox>
                    </input-container>
                </input-group>
            </template>
            <template #actions>
                <!-- Befehle -->
                <button-group>
                    <input-button type="button" @click.prevent="registerUser">
                        Registrieren
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
    name: "Auth_Register",

    components: {
        Layout,
        PageContent,
        SectionForm,
        Favicon,
        DisplayHyperlink,
        InputLoading,
        ErrorList,
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
                first_name: "",
                name: "",
                email: "",
                password: "",
                password_confirmation: "",
                terms: false,
            },
        };
    },

    methods: {
        registerUser() {
            let routeRegister = "register";
            //
            this.loading = true;
            this.loadingText = "Die Registierung wird durchgeführt!";
            //
            this.$inertia.post(this.route(routeRegister), this.form, {
                onFinish: () => {
                    this.loading = false;
                    //
                    this.form.password = null;
                    this.form.password_confirmation = null;
                },
            });
        },
    },
});
</script>

