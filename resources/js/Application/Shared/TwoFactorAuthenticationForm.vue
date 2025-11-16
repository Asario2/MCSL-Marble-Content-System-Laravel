<template>
    <section-form :withTitle="true">
        <template #title> Zwei-Faktor-Authentifizierung </template>

        <template #description>
            Fügen Sie Ihrem Konto zusätzliche Sicherheit hinzu, indem Sie die
            Zwei-Faktor-Authentifizierung verwenden.
        </template>

        <template #form>
            <h3 v-if="twoFactorEnabled && !confirming" class="form_text_h3">
                Sie haben die Zwei-Faktor-Authentifizierung aktiviert.
            </h3>

            <h3 v-else-if="twoFactorEnabled && confirming" class="form_text_h3">
                Aktivierung der Zwei-Faktor-Authentifizierung abschließen.
            </h3>

            <h3 v-else class="form_text_h3">
                Sie haben die Zwei-Faktor-Authentifizierung nicht aktiviert.
            </h3>

            <div v-if="twoFactorEnabled">
                <div v-if="qrCode">
                    <div class="form_text_info">
                        <p v-if="confirming" class="font-semibold">
                            Um die Aktivierung der Zwei-Faktor-Authentifizierung
                            abzuschließen, scannen Sie den folgenden QR-Code mit
                            der Authentifizierungsanwendung Ihres Smartphones
                            oder geben Sie den Einrichtungsschlüssel ein und
                            geben Sie den generierten OTP-Code ein.
                        </p>

                        <p v-else>
                            Die Zwei-Faktor-Authentifizierung ist jetzt
                            aktiviert. Scannen Sie den folgenden QR-Code mit der
                            Authentifizierungsanwendung Ihres Smartphones oder
                            geben Sie den Einrichtungsschlüssel ein.
                        </p>
                    </div>

                    <div class="form_qr_code" v-html="qrCode" />
                    <div v-if="setupKey" class="form_text_info">
                        <p class="font-semibold">
                            Einrichtungsschlüssel:
                            <span v-html="setupKey"></span>
                        </p>
                    </div>

                    <div v-if="confirming">
                        <input-container :full-width="true" class="mt-4">
                            <input-label name="code" label="Code"></input-label>
                            <input-element
                                type="numeric"
                                name="code"
                                ref="code"
                                v-model="confirmationForm.code"
                            ></input-element>
                            <input-error
                                :message="confirmationForm.errors.code"
                            />
                        </input-container>
                    </div>
                </div>

                <div v-if="recoveryCodes.length > 0 && !confirming">
                    <div class="form_text_info">
                        Speichern Sie diese Wiederherstellungscodes in einem
                        sicheren Passwort-Manager. Sie können verwendet werden,
                        um den Zugang zu Ihrem Konto wiederherzustellen, wenn
                        Ihr Gerät zur Zwei-Faktor-Authentifizierung verloren
                        geht.
                    </div>

                    <div
                        class="grid gap-1 max-w-xl mt-4 px-4 py-4 font-mono text-sm bg-layout-sun-100 dark:bg-layout-night-100 dark:text-gray-100 rounded-lg"
                    >
                        <div v-for="code in recoveryCodes" :key="code">
                            {{ code }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5">
                <div v-if="!twoFactorEnabled">
                    <button-group>
                        <confirms-password
                            @confirmed="enableTwoFactorAuthentication"
                        >
                            <input-button
                                type="button"
                                :class="{ 'opacity-25': enabling }"
                                :disabled="enabling"
                            >
                                Aktivieren
                            </input-button>
                        </confirms-password>
                    </button-group>
                </div>

                <div v-else>
                    <button-group>
                        <confirms-password
                            @confirmed="confirmTwoFactorAuthentication"
                            v-if="confirming"
                        >
                            <input-button
                                type="button"
                                class="me-3"
                                :class="{ 'opacity-25': enabling }"
                                :disabled="enabling"
                            >
                                Bestätigen
                            </input-button>
                        </confirms-password>

                        <confirms-password
                            @confirmed="regenerateRecoveryCodes"
                            v-if="recoveryCodes.length > 0 && !confirming"
                        >
                            <input-button class="me-3"
                                >Wiederherstellungs-Codes
                                regenerieren</input-button
                            >
                        </confirms-password>

                        <confirms-password
                            @confirmed="showRecoveryCodes"
                            v-if="recoveryCodes.length === 0 && !confirming"
                        >
                            <input-button class="me-3"
                                >Wiederherstellungscodes anzeigen</input-button
                            >
                        </confirms-password>

                        <confirms-password
                            @confirmed="disableTwoFactorAuthentication"
                            v-if="confirming"
                        >
                            <input-button
                                :class="{ 'opacity-25': disabling }"
                                :disabled="disabling"
                            >
                                Abbrechen
                            </input-button>
                        </confirms-password>

                        <confirms-password
                            @confirmed="disableTwoFactorAuthentication"
                            v-if="!confirming"
                        >
                            <input-danger-button
                                :class="{ 'opacity-25': disabling }"
                                :disabled="disabling"
                            >
                                Deaktivieren
                            </input-danger-button>
                        </confirms-password>
                    </button-group>
                    <div class="mt-4 text-sm text-gray-400">
                    <!-- [DEBUG] confirming: {{ confirming }}, twoFactorEnabled: {{ twoFactorEnabled }}, qrCode: {{ qrCode ? 'loaded' : 'null' }} -->
                    </div>
                </div>
            </div>
        </template>
    </section-form>
</template>

<script>
import { Link, router, useForm, usePage } from "@inertiajs/vue3";
import axios from "axios";

import SectionForm from "@/Application/Components/Content/SectionForm.vue";

import ConfirmsPassword from "@/Application/Shared/ConfirmsPassword.vue";

import InputGroup from "@/Application/Components/Form/InputGroup.vue";
import InputContainer from "@/Application/Components/Form/InputContainer.vue";
import InputLabel from "@/Application/Components/Form/InputLabel.vue";
import InputElement from "@/Application/Components/Form/InputElement.vue";
import InputError from "@/Application/Components/Form/InputError.vue";
import InputActionMessage from "@/Application/Components/Form/InputActionMessage.vue";

import ButtonGroup from "@/Application/Components/Form/ButtonGroup.vue";
import InputButton from "@/Application/Components/Form/InputButton.vue";
import InputDangerButton from "@/Application/Components/Form/InputDangerButton.vue";

export default {
    name: "Shared_TwoFactorAuthenticationForm",

    components: {
        Link,
        SectionForm,
        ConfirmsPassword,
        InputGroup,
        InputContainer,
        InputLabel,
        InputElement,
        InputError,
        InputActionMessage,
        ButtonGroup,
        InputButton,
        InputDangerButton,
    },

    props: {
        requiresConfirmation: {
            type: Boolean,
            default: false,
        },
    },

    data() {
        return {
            enabling: false,
            confirming: false,
            disabling: false,
            qrCode: null,
            two_factor_enabled: false,
            setupKey: null,
            recoveryCodes: [],
            confirmationForm: useForm({
                code: "",
            }),
            twoFactorEnabled: this.$page.props.auth.user.two_factor_confirmed_at !== null,
        };
    },

    computed: {
        page() {
            return usePage();
        },
    },

    methods: {
        enableTwoFactorAuthentication() {
    this.enabling = true;

    router.post(
        route("two-factor.enable"),
        {},
        {
            preserveScroll: true,
            onSuccess: async () => {
                // console.log("enable success – loading setup data...");

                await Promise.all([
                    this.showQrCode(),
                    this.showSetupKey(),
                    this.showRecoveryCodes(),
                ]);

                this.twoFactorEnabled = true;
                this.confirming = true;
                // console.log("2FA Setup Data loaded – now in confirming mode");
            },
            onFinish: () => {
                this.enabling = false;
            },
        },
    );
},

confirmTwoFactorAuthentication() {
    this.confirmationForm.post(route("two-factor.confirm"), {
        errorBag: "confirmTwoFactorAuthentication",
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            this.confirming = false;
            this.qrCode = null;
            this.setupKey = null;
            this.recoveryCodes = [];
            this.twoFactorEnabled = true;
            this.two_factor_enabled  = "1";
            axios.post('/user/twofactor/enable')
            .then(() => {
//                 console.log('two_factor_enabled updated in database');
            })
            .catch(err => {
                console.error('Error updating two_factor_enabled:', err);
            });
        },
    });

},
        showQrCode() {
            return axios.get(route("two-factor.qr-code")).then((response) => {
                this.qrCode = response.data.svg;
            });
        },
        showSetupKey() {
            return axios
                .get(route("two-factor.secret-key"))
                .then((response) => {
                    this.setupKey = response.data.secretKey;
                });
        },
        showRecoveryCodes() {
            return axios
                .get(route("two-factor.recovery-codes"))
                .then((response) => {
                    this.recoveryCodes = response.data;
                });
        },
        // confirmTwoFactorAuthentication() {
        //     this.confirmationForm.post(route("two-factor.confirm"), {
        //         errorBag: "confirmTwoFactorAuthentication",
        //         preserveScroll: true,
        //         preserveState: true,
        //         onSuccess: () => {
        //             this.confirming = false;
        //             this.qrCode = null;
        //             this.setupKey = null;
        //         },
        //     });
        // },
        regenerateRecoveryCodes() {
            axios.post(route("two-factor.recovery-codes")).then(() => {
                this.showRecoveryCodes();
            });
        },
        disableTwoFactorAuthentication() {
            this.disabling = true;
            router.delete(route("two-factor.disable"), {
                preserveScroll: true,
                onSuccess: () => {
                    this.disabling = false;
                    this.confirming = false;
                    this.twoFactorEnabled = false;
                    this.confirmationForm.reset();
                    this.confirmationForm.clearErrors();
                },
            });
                axios.post('/user/twofactor/disable')
    .then(() => {
//         console.log('two_factor_enabled updated in database');
    })
    .catch(err => {
        console.error('Error updating two_factor_enabled:', err);
    });
        },
    },
};
</script>


