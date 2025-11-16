<template>
    <section-form :withTitle="true">
        <template #title> Browsersitzungen </template>

        <template #description>
            Verwalten und Abmelden Ihrer aktiven Sitzungen in anderen Browsern
            und auf anderen Geräten.
        </template>

        <template #form>
            <div class="form_text_info">
                Bei Bedarf können Sie sich von allen anderen Browsersitzungen
                auf allen Ihren Geräten abmelden. Einige Ihrer letzten Sitzungen
                sind unten aufgelistet, diese Liste ist jedoch möglicherweise
                nicht vollständig. Wenn Sie der Meinung sind, dass Ihr Konto
                kompromittiert wurde, sollten Sie auch Ihr Kennwort
                aktualisieren.
            </div>

            <!-- Other Browser Sessions -->
            <div v-if="sessions.length > 0" class="mt-5 space-y-6">
                <div
                    v-for="(session, i) in sessions"
                    :key="i"
                    class="flex items-center"
                >
                    <div>
                        <svg
                            v-if="session.agent.is_desktop"
                            class="w-8 h-8 text-gray-500"
                            xmlns="http://www.w3.org/2000/svg"

                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor" fill="none"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25"
                            />
                        </svg>

                        <svg
                            v-else
                            class="w-8 h-8 text-gray-500"
                            xmlns="http://www.w3.org/2000/svg"

                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor" fill="none"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3"
                            />
                        </svg>
                    </div>

                    <div class="ms-3">
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            {{
                                session.agent.platform
                                    ? session.agent.platform
                                    : "Unbekannt"
                            }}
                            -
                            {{
                                session.agent.browser
                                    ? session.agent.browser
                                    : "Unbekannt"
                            }}
                        </div>

                        <div>
                            <div class="text-xs text-gray-500">
                                {{ session.ip_address }},

                                <span
                                    v-if="session.is_current_device"
                                    class="text-green-500 font-semibold"
                                    >Dieses Gerät</span
                                >
                                <span v-else
                                    >Zuletzt aktiv
                                    {{ session.last_active }}</span
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5">
                <button-group>
                    <input-action-message
                        :on="form.recentlySuccessful"
                        class="ms-3"
                    >
                        Erledigt.
                    </input-action-message>
                    <input-button @click="confirmLogout">
                        Andere Browser-Sitzungen abmelden
                    </input-button>
                </button-group>
            </div>

            <!-- Log Out Other Devices Confirmation Modal -->
            <dialog-modal :show="confirmingLogout" @close="closeModal">
                <template #title> Andere Browser-Sitzungen abmelden </template>

                <template #content>
                    Bitte geben Sie Ihr Passwort ein, um zu bestätigen, dass Sie
                    sich von Ihren anderen Browser-Sitzungen auf allen Ihren
                    Geräten abmelden möchten.

                    <input-group class="mt-4">
                        <input-container :full-width="true">
                            <input-element
                                type="password"
                                name="password"
                                v-model="form.password"
                                placeholder="Password"
                                ref="passwordInput"
                                @keyup.enter="logoutOtherBrowserSessions"
                            ></input-element>
                            <input-error :message="form.errors.password" />
                        </input-container>
                    </input-group>
                </template>

                <template #footer>
                    <input-white-button @click="closeModal">
                        Abbrechen
                    </input-white-button>

                    <input-button
                        class="ms-3"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="logoutOtherBrowserSessions"
                    >
                        Andere Browser-Sitzungen abmelden
                    </input-button>
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
import InputButton from "@/Application/Components/Form/InputButton.vue";
import InputWhiteButton from "@/Application/Components/Form/InputWhiteButton.vue";

import DialogModal from "@/Application/Components/DialogModal.vue";

export default {
    name: "Shared_LogoutOtherBrowserSessionsForm",

    components: {
        SectionForm,
        InputGroup,
        InputContainer,
        InputLabel,
        InputElement,
        InputError,
        InputActionMessage,
        ButtonGroup,
        InputButton,
        InputWhiteButton,
        DialogModal,
    },

    props: {
        sessions: {
            type: Array,
            default: () => [],
        },
    },

    data() {
        return {
            confirmingLogout: false,
            form: useForm({
                password: "",
            }),
        };
    },
    methods: {
        confirmLogout() {
            this.confirmingLogout = true;
            setTimeout(() => this.$refs.passwordInput.focus(), 250);
        },
        logoutOtherBrowserSessions() {
            this.form.delete(route("other-browser-sessions.destroy"), {
                preserveScroll: true,
                onSuccess: () => this.closeModal(),
                onError: () => this.$refs.passwordInput.focus(),
                onFinish: () => this.form.reset(),
            });
        },
        closeModal() {
            this.confirmingLogout = false;
            this.form.reset();
        },
    },
};
</script>

