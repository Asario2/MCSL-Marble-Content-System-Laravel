<template>
    <Layout title="Zwei-Faktor-Bestätigung">
        <template #logo>
            <Favicon class="h-16" />
        </template>

        <page-content v-if="loading">
            <template #content>
                <InputLoading
                    :loading="loading"
                    :loadingText="loadingText"
                ></InputLoading
            ></template>
        </page-content>

        <section-form>
            <template #form>
                <display-text>
                    <template v-if="!recovery">
                        Bitte bestätigen Sie den Zugriff auf Ihr Konto, indem
                        Sie den von Ihrer Authentifizierungsanwendung
                        bereitgestellten Authentifizierungscode eingeben.
                    </template>
                    <template v-else>
                        Bitte bestätigen Sie den Zugriff auf Ihr Konto, indem
                        Sie einen Ihrer Notfall-Wiederherstellungscodes
                        eingeben.
                    </template>
                </display-text>

                <!-- Liste der Fehler -->
                <error-list :errors="errors" />

                <input-group>
                    <input-container :full-width="true" v-if="!recovery">
                        <input-label name="code" label="Code"></input-label>
                        <input-element
                            type="text"
                            name="code"
                            v-model="form.code"
                            ref="codeInput"
                        ></input-element>
                        <input-error :message="form.errors.code" />
                    </input-container>

                    <input-container :full-width="true" v-else>
                        <input-label
                            name="recovery_code"
                            label="Wiederherstellungscode"
                        ></input-label>
                        <input-element
                            type="text"
                            name="recovery_code"
                            v-model="form.recovery_code"
                            ref="recoveryCodeInput"
                        ></input-element>
                        <input-error :message="form.errors.recovery_code" />
                    </input-container>
                </input-group>
            </template>
            <template #actions>
                <!-- Befehle -->
                <button-group>
                    <input-white-button
                        type="button"
                        @click.prevent="toggleRecovery"
                    >
                        <template v-if="!recovery">
                            Verwenden Sie einen Wiederherstellungscode
                        </template>

                        <template v-else>
                            Verwenden Sie einen Authentifizierungscode
                        </template>
                    </input-white-button>

                    <input-button type="button" @click.prevent="submit">
                        Anmelden
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
import {route} from 'ziggy-js';

import ButtonGroup from "@/Application/Components/Form/ButtonGroup.vue";
import InputButton from "@/Application/Components/Form/InputButton.vue";
import InputWhiteButton from "@/Application/Components/Form/InputWhiteButton.vue";

export default defineComponent({
    name: "Auth_TwoFactorChallenge",

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
        InputWhiteButton,
    },

    data() {
        return {
            loading: false,
            loadingText: null,
            //
            recovery: false,
            form: useForm({
                code: "",
                recovery_code: "",
            }),
        };
    },

    methods: {
        toggleRecovery() {
            this.recovery = !this.recovery;
            this.$nextTick(() => {
                if (this.recovery) {
                    this.$refs.recoveryCodeInput.focus();
                    this.form.code = "";
                } else {
                    this.$refs.codeInput.focus();
                    this.form.recovery_code = "";
                }
            });
        },
        submit() {
            this.form.post(route("two-factor.login"));
        },
    },
});
</script>
