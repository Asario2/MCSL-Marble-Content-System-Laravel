<template>
    <span>
        <span @click="startConfirmingPassword">
            <slot />
        </span>

        <dialog-modal :show="confirmingPassword" @close="closeModal">
            <template #title>
                {{ title }}
            </template>

            <template #content>
                {{ content }}

                <input-group class="mt-4">
                    <input-container :full-width="true">
                        <input-element
                            type="password"
                            name="password"
                            v-model="form.password"
                            placeholder="Kennwort"
                            ref="passwordInput"
                        ></input-element>
                        <input-error :message="form.error" />
                    </input-container>
                </input-group>
            </template>

            <template #footer>
                <button-group>
                    <input-white-button @click="closeModal">
                        Abbrechen
                    </input-white-button>

                    <input-button
                        class="ms-3"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="confirmPassword"
                    >
                        {{ button }}
                    </input-button>
                </button-group>
            </template>
        </dialog-modal>
    </span>
</template>

<script>
import axios from "axios";

import DialogModal from "@/Application/Components/DialogModal.vue";
import InputGroup from "@/Application/Components/Form/InputGroup.vue";
import InputContainer from "@/Application/Components/Form/InputContainer.vue";
import InputLabel from "@/Application/Components/Form/InputLabel.vue";
import InputElement from "@/Application/Components/Form/InputElement.vue";
import InputError from "@/Application/Components/Form/InputError.vue";

import ButtonGroup from "@/Application/Components/Form/ButtonGroup.vue";
import InputButton from "@/Application/Components/Form/InputButton.vue";
import InputWhiteButton from "@/Application/Components/Form/InputWhiteButton.vue";

export default {
    name: "Shared_ConfirmPasswird",

    components: {
        DialogModal,
        InputGroup,
        InputContainer,
        InputLabel,
        InputElement,
        InputError,
        ButtonGroup,
        InputButton,
        InputWhiteButton,
    },

    props: {
        title: {
            type: String,
            default: "Passwort bestätigen",
        },
        content: {
            type: String,
            default:
                "Um fortzufahren, bestätigen Sie zu Ihrer Sicherheit bitte Ihr Passwort.",
        },
        button: {
            type: String,
            default: "Bestätigen",
        },
    },

    data() {
        return {
            confirmingPassword: false,
            form: {
                password: "",
                error: "",
                processing: false,
            },
        };
    },

    methods: {
        startConfirmingPassword() {
            axios
                .get(route("password.confirmation"))
                .then((response) => {
                    if (response.data.confirmed) {
                        this.$emit("confirmed");
                    } else {
                        this.confirmingPassword = true;
                        this.$nextTick(() => {
                            this.$refs.passwordInput.focus();
                        });
                    }
                })
                .catch((error) => {
                    console.error(
                        "Error fetching password confirmation",
                        error,
                    );
                });
        },
        confirmPassword() {
            this.form.processing = true;
            axios
                .post(route("password.confirm"), {
                    password: this.form.password,
                })
                .then(() => {
                    this.form.processing = false;
                    this.closeModal();
                    this.$nextTick(() => {
                        this.$emit("confirmed");
                    });
                })
                .catch((error) => {
                    this.form.processing = false;
                    this.form.error = error.response.data.errors.password[0];
                    this.$refs.passwordInput.focus();
                });
        },
        closeModal() {
            this.confirmingPassword = false;
            this.form.password = "";
            this.form.error = "";
        },
    },
};
</script>

