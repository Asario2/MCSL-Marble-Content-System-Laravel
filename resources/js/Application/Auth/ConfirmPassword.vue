<template>
    <Layout title="Kennwort bestätigen">
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

                <display-text>
                    Dies ist ein sicherer Bereich der Anwendung. Bitte geben Sie
                    Ihr Passwort ein, bevor Sie fortfahren.
                </display-text>

                <!-- Liste der Fehler -->
                <error-list :errors="errors" />

                <input-group>
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
                </input-group>
            </template>
            <template #actions>
                <!-- Befehle -->
                <button-group>
                    <input-button
                        type="button"
                        @click.prevent="confirmPasswordUser"
                    >
                        Kennwort bestätigen
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
    name: "Auth_ConfirmPassword",

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
    },

    data() {
        return {
            loading: false,
            loadingText: null,
            //
            form: {
                password: "",
            },
        };
    },

    methods: {
        confirmPasswordUser() {
            let routeConfirmPassword = "password.update";
            //
            this.loading = true;
            this.loadingText = "Das Kennwort wird geprüft!";
            //
            this.$inertia.post(this.route(routeConfirmPassword), this.form, {
                onFinish: () => {
                    this.loading = false;
                    this.form.password = "";
                },
            });
        },
    },
});
</script>

