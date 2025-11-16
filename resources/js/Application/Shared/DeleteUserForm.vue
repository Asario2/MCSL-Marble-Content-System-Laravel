<template>
    <section-form :withTitle="true">
        <template #title> Account löschen </template>

        <template #description> Löschen Sie Ihren Account dauerhaft. </template>

        <template #form>
            <div class="form_text_info">
                Möchten Sie Ihr Konto wirklich löschen? Sobald Ihr Konto
                gelöscht wurde, werden alle Ressourcen und Daten dauerhaft
                gelöscht. Bitte geben Sie Ihr Passwort ein, um zu bestätigen,
                dass Sie Ihr Konto dauerhaft löschen möchten.
            </div>

            <div class="mt-5">
                <button-group>
                    <input-danger-button @click="confirmUserDeletion">
                        Account löschen
                    </input-danger-button>
                </button-group>
            </div>

            <!-- Delete Account Confirmation Modal -->
            <dialog-modal :show="confirmingUserDeletion" @close="closeModal">
                <template #title> Account löschen </template>

                <template #content>
                    Möchten Sie Ihr Konto wirklich löschen? Sobald Ihr Konto
                    gelöscht wurde, werden alle Ressourcen und Daten dauerhaft
                    gelöscht. Bitte geben Sie Ihr Passwort ein, um zu
                    bestätigen, dass Sie Ihr Konto dauerhaft löschen möchten.

                    <input-group class="mt-4">
                        <input-container :full-width="true">
                            <input-element
                                type="password"
                                name="password"
                                v-model="form.password"
                                placeholder="Password"
                                ref="passwordInput"
                                @keyup.enter="deleteUser"
                            ></input-element>
                            <input-error :message="form.errors.password" />
                        </input-container>
                    </input-group>
                </template>

                <template #footer>
                    <button-group>
                        <input-white-button @click="closeModal">
                            Abbrechen
                        </input-white-button>

                        <input-danger-button
                            class="ms-3"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                            @click="deleteUser"
                        >
                            Account löschen
                        </input-danger-button>
                    </button-group>
                </template>
            </dialog-modal>
        </template>
    </section-form>
</template>
<script>
import { useForm } from "@inertiajs/vue3";

import SectionForm from "@/Application/Components/Content/SectionForm.vue";

import InputGroup from "@/Application/Components/Form/InputGroup.vue";
import InputContainer from "@/Application/Components/Form/InputContainer.vue";
import InputLabel from "@/Application/Components/Form/InputLabel.vue";
import InputElement from "@/Application/Components/Form/InputElement.vue";
import InputError from "@/Application/Components/Form/InputError.vue";
import InputActionMessage from "@/Application/Components/Form/InputActionMessage.vue";

import ButtonGroup from "@/Application/Components/Form/ButtonGroup.vue";
import InputDangerButton from "@/Application/Components/Form/InputDangerButton.vue";
import InputWhiteButton from "@/Application/Components/Form/InputWhiteButton.vue";

import DialogModal from "@/Application/Components/DialogModal.vue";

export default {
    name: "Shared_DeleteUserForm",

    components: {
        SectionForm,
        ButtonGroup,
        InputGroup,
        InputContainer,
        InputLabel,
        InputElement,
        InputError,
        InputActionMessage,
        InputDangerButton,
        InputWhiteButton,
        DialogModal,
    },

    data() {
        return {
            confirmingUserDeletion: false,
            form: useForm({
                password: "",
            }),
            passwordInput: null,
        };
    },
    methods: {
        confirmUserDeletion() {
            this.confirmingUserDeletion = true;
            setTimeout(() => this.$refs.passwordInput.focus(), 250);
        },
        deleteUser() {
            this.form.delete(route("current-user.destroy"), {
                preserveScroll: true,
                onSuccess: () => this.closeModal(),
                onError: () => this.$refs.passwordInput.focus(),
                onFinish: () => this.form.reset(),
            });
        },
        closeModal() {
            this.confirmingUserDeletion = false;
            this.form.reset();
        },
    },
};
</script>

