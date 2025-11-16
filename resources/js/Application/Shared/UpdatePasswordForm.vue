<template>
    <section-form @submitted="updatePassword" :withTitle="true">
        <template #title> Passwort aktualisieren </template>

        <template #description>
            Stellen Sie sicher, dass Ihr Konto ein langes, zufälliges Passwort
            verwendet, um die Sicherheit zu gewährleisten.
        </template>

        <template #form>
            <input-group>
                <input-container :full-width="true">
                    <input-label
                        name="current_password"
                        label="Derzeitiges Passwort"
                    ></input-label>
                    <input-element
                        type="password"
                        ref="currentPasswordInput"
                        name="current_password"
                        autocomplete="current-password"
                        v-model="form.current_password"
                    ></input-element>
                    <input-error :message="form.errors.current_password" />
                </input-container>

                <input-container :full-width="true">
                    <input-label
                        name="password"
                        label="Neues Passwort"
                    ></input-label>
                    <input-element
                        type="password"
                        ref="currentPasswordInput"
                        autocomplete="new-password"
                        name="password"
                        v-model="form.password"
                    ></input-element>
                    <input-error :message="form.errors.password" />
                </input-container>

                <input-container :full-width="true">
                    <input-label
                        name="password_confirmation"
                        label="Passwort bestätigen"
                    ></input-label>
                    <input-element
                        type="password"
                        ref="currentPasswordInput"
                        autocomplete="new-password"
                        name="password_confirmation"
                        v-model="form.password_confirmation"
                    ></input-element>
                    <input-error :message="form.errors.password_confirmation" />
                </input-container>
            </input-group>
        </template>

        <template #actions>
            <button-group>
                <input-action-message
                    :on="form.recentlySuccessful"
                    class="me-3"
                >
                    Gespeichert.
                </input-action-message>
                <input-button
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Speichern
                </input-button>
            </button-group>
        </template>
    </section-form>
</template>
<script>
import { Link, useForm } from "@inertiajs/vue3";
import SectionForm from "@/Application/Components/Content/SectionForm.vue";

import InputGroup from "@/Application/Components/Form/InputGroup.vue";
import InputContainer from "@/Application/Components/Form/InputContainer.vue";
import InputLabel from "@/Application/Components/Form/InputLabel.vue";
import InputElement from "@/Application/Components/Form/InputElement.vue";
import InputError from "@/Application/Components/Form/InputError.vue";
import InputActionMessage from "@/Application/Components/Form/InputActionMessage.vue";

import ButtonGroup from "@/Application/Components/Form/ButtonGroup.vue";
import InputButton from "@/Application/Components/Form/InputButton.vue";

export default {
    name: "Shared_UpdatePasswordForm",

    components: {
        Link,
        SectionForm,
        InputGroup,
        InputContainer,
        InputLabel,
        InputElement,
        InputError,
        InputActionMessage,
        ButtonGroup,
        InputButton,
    },

    data() {
        return {
            form: useForm({
                current_password: "",
                password: "",
                password_confirmation: "",
            }),
        };
    },

    methods: {
        updatePassword() {
                this.form.put(route("user-password.update"), {
                    errorBag: "updatePassword",
                    preserveScroll: true,
                    onSuccess: () => this.form.reset(),
                    onError: () => {
                    if (this.form.errors.password) {
                        this.form.reset("password", "password_confirmation");
                        this.$refs.passwordInput.focus();
                    }
                    if (this.form.errors.current_password) {
                        this.form.reset("current_password");
                        this.$refs.currentPasswordInput.focus();
                    }
                },
            });
        },
    },
};
</script>

