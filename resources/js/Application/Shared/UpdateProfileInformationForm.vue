<template>
    <section-form @submitted="updateProfileInformation" :withTitle="true">
        <template #title> Profil-Informationen </template>

        <template #description>
            Aktualisiere die Profilinformationen und E-Mail-Adresse deines
            Kontos.
        </template>

        <template #form>
            <!-- Profile Photo -->
            <div v-if="$page.props.jetstream.managesProfilePhotos">
                <input-group class="mt-2">
                    <input-container :full-width="true">
                        <input-label name="photo" label="Foto"></input-label>
                        <!-- Profile Photo File Input -->
                        <input
                            id="photo"
                            ref="photoInput"
                            type="file"
                            class="hidden"
                            @change="updatePhotoPreview"
                        />
                    </input-container>

                    <!-- Current Profile Photo -->
                    <div v-show="!photoPreview" class="mt-4">
                        <img
                            :src="'/images/_' +    SD() + '/users/profile_photo_path/' + user.profile_photo_url.replace('http://localhost/images/','').replace('profile-photos/','')  "
                            :alt="user.name"
                            class="rounded-full h-20 w-20 object-cover"
                        />
                    </div>
                    <!-- New Profile Photo Preview -->
                    <div v-show="photoPreview" class="mt-4">
                        <span
                            class="block rounded-full w-20 h-20 bg-cover bg-no-repeat bg-center"
                            :style="
                                'background-image: url(\'' +
                                photoPreview +
                                '\');'
                            "
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
                    >
                    </input-checkbox_alt>
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

            <input-group class="mt-4">
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
                    <input-error :message="form.errors.name" />
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
                    <input-error :message="form.errors.first_name" />
                </input-container>



                <input-container :full-width="true">
                    <input-label name="email" label="Mailadresse"></input-label>
                    <input-element
                        type="email"
                        name="email"
                        v-model="form.email"
                        placeholder="Mailadresse"
                        ref="email"
                    ></input-element>
                    <input-error :message="form.errors.email" />
                </input-container>
<!--

                <input-container :full-width="true">
                        <div class="relative mb-4">
                        <input-label name="birthday" label="Geburtstag"></input-label>
                        <input-element
                            type="date"
                            name="birthday"
                            v-model="form.birthday"
                            placeholder="Geburtstag"
                            ref="birthday" class="dt"
                        ></input-element>
                        <IconCal v-if="!disabled" class="
                            pointer-events-none absolute DateIcon hidden dark:block       "
                            alt="calendar icon (white)"
                        ></IconCal>
                        <input-error :message="form.errors.birthday" />
                    </div>
                    </input-container>


                    <input-container :full-width="true">
                        <input-label
                            name="music"
                            label="Musik"
                        ></input-label>
                        <input-element
                            type="text"
                            name="music"
                            v-model="form.music"
                            placeholder="Lieblingsmusik"
                            ref="music"
                        ></input-element>
                        <input-error :message="form.errors.music" />
                    </input-container>


                    <input-container :full-width="true">
                        <input-label
                            name="occupation"
                            label="Beschäftigung"
                        ></input-label>
                        <input-element
                            type="text"
                            name="occupation"
                            v-model="form.occupation"
                            placeholder="Deine Beschäftigung"
                            ref="occupation"
                        ></input-element>
                        <input-error :message="form.errors.occupation" />
                    </input-container>

                    <input-container :full-width="true">
                        <input-label
                            name="interests"
                            label="Interessen"
                        ></input-label>
                        <input-element
                            type="text"
                            name="interests"
                            v-model="form.interests"
                            placeholder="Deine Interessen"
                            ref="interests"
                        ></input-element>
                        <input-error :message="form.errors.interests" />
                    </input-container>
                    <input-container :full-width="true">
                        <!-- <input-label
                            name="text"
                            label="Über dich"
                        ></input-label>
                        <InputHtml
                            name="about"
                            v-model="form.about"
                            placeholder="Über dich"
                            ref="about"
                        ></InputHtml>
                        <input-error :message="form.errors.about" />
                    </input-container> -->
                <input-container
                    :full-width="true"
                    v-if="
                        $page.props.jetstream.hasEmailVerification &&
                        user.email_verified_at === null
                    "
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
                                    Klicke hier, um die Verifizierungs-E-Mail
                                    erneut zu senden.
                                </Link>
                            </div>

                            <div
                                v-show="verificationLinkSent"
                                class="form_text_info_success"
                            >
                                Ein neuer Verifizierungslink wurde an deine
                                E-Mail-Adresse gesendet.
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
import { SD } from '@/helpers';
import InputGroup from "@/Application/Components/Form/InputGroup.vue";
import InputContainer from "@/Application/Components/Form/InputContainer.vue";
import InputLabel from "@/Application/Components/Form/InputLabel.vue";
import InputElement from "@/Application/Components/Form/InputElement.vue";
import IconCal from "@/Application/Components/Icons/IconCal.vue";
import InputError from "@/Application/Components/Form/InputError.vue";
import InputActionMessage from "@/Application/Components/Form/InputActionMessage.vue";

import ButtonGroup from "@/Application/Components/Form/ButtonGroup.vue";
import InputButton from "@/Application/Components/Form/InputButton.vue";
import InputWhiteButton from "@/Application/Components/Form/InputWhiteButton.vue";

import Alert from "@/Application/Components/Content/Alert.vue";
import InputHtml from "../Components/Form/InputHtml.vue";
import InputCheckbox_alt from "@/Application/Components/Form/InputCheckbox_alt.vue";
import Users from "../Homepage/Users.vue";

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
        InputHtml,
        IconCal,
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
                music: this.user.music,
                birthday: this.user.birthday,
                interests: this.user.interests,
                occupation: this.user.occupation,
                email: this.user.email,
                photo: null,
                about: this.user.about,
            }),
            verificationLinkSent: false,
            photoPreview: null,
            aiCheckValue: this.user.xis_aiImage,
        };
    },

    methods: {
        SD,
        AiImage(){
            const val = this.aiCheckValue;
            axios.post('/api/AddUserAI/' + val)
            .then(() => {
                this.user.xis_aiImage = val;
                // Optionale Erfolgsmeldung o.Ä.
            })
            .catch(() => {
                // Fehlerbehandlung, z.B. Wert zurücksetzen:
                this.aiCheckValue = this.user.xis_aiImage;
                alert('Fehler beim Speichern.');
            });
        },
        updateProfileInformation() {
            if (this.$refs.photoInput.value) {
                this.form.photo = this.$refs.photoInput.files[0];
            }

            this.form.post(route("user-profile-information.update"), {
                errorBag: "updateProfileInformation",
                preserveScroll: true,
                onSuccess: this.clearPhotoFileInput,
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

            reader.onload = (e) => {
                this.photoPreview = e.target.result;
            };

            reader.readAsDataURL(photo);
        },
        deletePhoto() {
            router.delete(route("current-user-photo.destroy"), {
                preserveScroll: true,
                onSuccess: () => {
                    this.photoPreview = null;
                    this.clearPhotoFileInput();
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
.DateIcon{
    right:-10px;
    top:-35px;
    max-height:24px;
    max-width:24px;
}
.left{
top:30px;
right:74px;
color:white;
}
</style>
