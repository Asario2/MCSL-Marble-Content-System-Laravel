<template>
    <section-form @submitted="updateProfileInformation" :withTitle="true">
        <template #title> Profil-Informationen </template>

        <template #description>
            Aktualisiere die Profilinformationen und E-Mail-Adresse deines Kontos.
        </template>

        <template #form>
            <!-- Profile Photo -->
            <div v-if="$page.props.jetstream.managesProfilePhotos">
                <input-group class="mt-2">
                    <input-container :full-width="true">
                        <input-label name="photo" label="Foto"></input-label>

                        <!-- File Input -->
                        <input
                            id="photo"
                            ref="photoInput"
                            type="file"
                            class="hidden"
                            @change="updatePhotoPreview"
                        />
                    </input-container>

                    <!-- Current Photo -->
                    <div v-show="!photoPreview" class="mt-4">
                        <img
                            :src="computedPhotoUrl"
                            :alt="user.name"
                            class="rounded-full h-20 w-20 object-cover"
                        />
                    </div>

                    <!-- New Preview -->
                    <div v-show="photoPreview" class="mt-4">
                        <span
                            class="block rounded-full w-20 h-20 bg-cover bg-no-repeat bg-center"
                            :style="`background-image: url('${photoPreview}')`"
                        />
                    </div>

                    <input-checkbox_alt
                        v-if="user.profile_photo_path"
                        class="mt-2"
                        name="xis_aiImage"
                        :exValue="user.xis_aiImage"
                        v-model="aiCheckValue"
                        label="Bild wurde mit KI erstellt"
                        @change="AiImage(aiCheckValue)"
                    />
                </input-group>

                <button-group align="justify-start">
                    <input-white-button
                        class="mt-2 me-2"
                        type="button"
                        @click.prevent="selectNewPhoto"
                    >
                        Wähle ein neues Foto aus
                    </input-white-button>

                    <input-button
                        v-if="user.profile_photo_path"
                        type="button"
                        class="mt-2"
                        @click.prevent="deletePhoto"
                    >
                        Entferne Foto
                    </input-button>
                </button-group>

                <input-error :message="form.errors.photo" />
            </div>

            <!-- Name, Vorname, Mail -->
            <input-group class="mt-4">
                <input-container :full-width="true">
                    <input-label name="name" label="Nickname" />
                    <input-element
                        type="text"
                        name="name"
                        v-model="form.name"
                        placeholder="Nickname"
                        ref="name"
                    />
                    <input-error :message="form.errors.name" />
                </input-container>

                <input-container :full-width="true">
                    <input-label name="first_name" label="Vorname" />
                    <input-element
                        type="text"
                        name="first_name"
                        v-model="form.first_name"
                        placeholder="Vorname"
                        ref="first_name"
                    />
                    <input-error :message="form.errors.first_name" />
                </input-container>

                <input-container :full-width="true">
                    <input-label name="email" label="Mailadresse" />
                    <input-element
                        type="email"
                        name="email"
                        v-model="form.email"
                        placeholder="Mailadresse"
                        ref="email"
                    />
                    <input-error :message="form.errors.email" />
                </input-container>

                <!-- Email Verification -->
                <input-container
                    :full-width="true"
                    v-if="$page.props.jetstream.hasEmailVerification && user.email_verified_at === null"
                >
                    <alert type="info">
                        <div>
                            <div class="form_text_info">
                                Deine E-Mail-Adresse ist nicht verifiziert.

                                <Link
                                    :href="route('verification.send')"
                                    method="post"
                                    as="button"
                                    class="form_link"
                                    @click.prevent="sendEmailVerification"
                                >
                                    Klicke hier, um die Verifizierungs-E-Mail erneut zu senden.
                                </Link>
                            </div>

                            <div v-show="verificationLinkSent" class="form_text_info_success">
                                Ein neuer Verifizierungslink wurde an deine E-Mail-Adresse gesendet.
                            </div>
                        </div>
                    </alert>
                </input-container>
            </input-group>
        </template>

        <template #actions>
            <input-action-message :on="form.recentlySuccessful" class="me-3">
                Gespeichert.
            </input-action-message>

            <input-button
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                Speichern
            </input-button>
        </template>
    </section-form>
</template>

<script>
import { Link, router, useForm } from "@inertiajs/vue3";
import SectionForm from "@/Application/Components/Content/SectionForm.vue";
import { SD,GetProfileImagePath } from "@/helpers";
import InputGroup from "@/Application/Components/Form/InputGroup.vue";
import InputContainer from "@/Application/Components/Form/InputContainer.vue";
import InputLabel from "@/Application/Components/Form/InputLabel.vue";
import InputElement from "@/Application/Components/Form/InputElement.vue";
import InputError from "@/Application/Components/Form/InputError.vue";
import InputActionMessage from "@/Application/Components/Form/InputActionMessage.vue";
import ButtonGroup from "@/Application/Components/Form/ButtonGroup.vue";
import InputButton from "@/Application/Components/Form/InputButton.vue";
import InputWhiteButton from "@/Application/Components/Form/InputWhiteButton.vue";
import Alert from "@/Application/Components/Content/Alert.vue";
import InputCheckbox_alt from "@/Application/Components/Form/InputCheckbox_alt.vue";

export default {
    name: "Shared_UpdateProfileInformationForm",

    components: {
        Link,
        SectionForm,
        InputGroup,
        InputContainer,
        InputLabel,
        InputElement,
        InputCheckbox_alt,
        InputError,
        InputActionMessage,
        ButtonGroup,
        InputButton,
        InputWhiteButton,
        Alert,
    },

    props: {
        user: {
            type: Object,
            default: () => ({}),
        },
    },

    data() {
        return {
            form: useForm({
                _method: "PUT",
                first_name: this.user.first_name,
                name: this.user.name,
                email: this.user.email,
                photo: null,
            }),
            verificationLinkSent: false,
            photoPreview: null,
            aiCheckValue: this.user.xis_aiImage,
        };
    },

    computed: {
        computedPhotoUrl() {
            if (this.user.profile_photo_path) {
                // Benutzerpfad im Subdomain-Verzeichnis
                const subdomain = SD();
                return `/images/_${subdomain}/users/profile_photo_path/${this.user.profile_photo_path}`;
            }
            return this.user.profile_photo_url; // fallback jetstream
        },
    },

    methods: {
        GetProfileImagePath,
        SD,
        AiImage() {
            const val = this.aiCheckValue;
            axios.post("/api/AddUserAI/" + val)
                .then(() => { this.user.xis_aiImage = val; })
                .catch(() => {
                    this.aiCheckValue = this.user.xis_aiImage;
                    alert("Fehler beim Speichern.");
                });
        },
        updateProfileInformation() {
            if (this.$refs.photoInput.value) {
                this.form.photo = this.$refs.photoInput.files[0];
            }

            this.form.post(route("user-profile-information.update_alt"), {
                errorBag: "updateProfileInformation",
                preserveScroll: true,
                forceFormData: true,
                onSuccess: () => {
                    this.clearPhotoFileInput();
                    this.photoPreview = null;
                    this.$inertia.reload({ only: ["auth"] });
                },
            });
        },
        sendEmailVerification() {
            this.verificationLinkSent = true;
        },
        selectNewPhoto() {
            this.$refs.photoInput.click();
        },
        updatePhotoPreview() {
            const photo = this.$refs.photoInput.files[0];
            if (!photo) return;
            const reader = new FileReader();
            reader.onload = (e) => { this.photoPreview = e.target.result; };
            reader.readAsDataURL(photo);
        },
        deletePhoto() {
            router.delete(route("current-user-photo.destroy"), {
                preserveScroll: true,
                onSuccess: () => {
                    this.photoPreview = null;
                    this.clearPhotoFileInput();
                    this.$inertia.reload({ only: ["auth"] });
                },
            });
        },
        clearPhotoFileInput() {
            if (this.$refs.photoInput?.value) {
                this.$refs.photoInput.value = null;
            }
        },
    },
};
</script>

<style>
.DateIcon {
    right: -10px;
    top: -35px;
    max-height: 24px;
    max-width: 24px;
}
.left {
    top: 30px;
    right: 74px;
    color: white;
}
</style>
