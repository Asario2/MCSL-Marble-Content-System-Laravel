    <template>
        <section-form @submitted="updateProfileInformation" :withTitle="true">
        <template #title> Persönliches Profil</template>

        <template #description>
            Aktualisiere deine Persönlichen Daten
        </template>

        <template #form>
            <input-group class="mt-4">
            <input-container :full-width="true">
            <input-label name="birthday" label="Geburtstag" />
            <input-element
                type="text"
                name="birthday"
                v-model="inputBirthday"
                placeholder="TT.MM.JJJJ"
                maxlength="10"
                @blur="onBirthdayBlur"
                ref="birthday"
                class="dt"
            />
            <!--
                @input="onBirthdayInput"
            -->
            <input type='hidden' name='birthday' id='birthday' :value="inputBirthday">


        <input-error :message="form?.errors?.birthday" />
            </input-container>

            <input-container :full-width="true" v-if="isform?.music">
                <input-label name="music" label="Musik" />
                <input-element
                type="text"
                name="music"
                v-model="form.music"
                placeholder="Lieblingsmusik"
                ref="music"
                />
                <input-error :message="form?.errors?.music" />
            </input-container>

            <input-container :full-width="true" v-if="isform?.occupation">
                <input-label name="occupation" label="Beschäftigung" />
                <input-element
                type="text"
                name="occupation"
                v-model="form.occupation"
                placeholder="Deine Beschäftigung"
                ref="occupation"
                />
                <input-error :message="form?.errors?.occupation" />
            </input-container>
            <input-container :full-width="true" v-if="isform?.headline">
                <input-label name="headline" label="Beschreibung" />
                <input-element
                type="text"
                name="headline"
                v-model="form.headline"
                placeholder="Beschreibung über dich "
                ref="headline"
                />
                <input-error :message="form?.errors?.aufgabe" />
            </input-container>
            <input-container :full-width="true" v-if="isform?.interests">
                <input-label name="interests" label="Interessen" />
                <input-element
                type="text"
                name="interests"
                v-model="form.interests"
                placeholder="Deine Interessen"
                ref="interests"
                />
                <input-error :message="form?.errors?.interests" />
            </input-container>
            <input-container :full-width="true" v-if="isform?.aufgabe">
                <input-label name="tasks" label="Aufgabe" />
                <input-element
                type="text"
                name="aufgabe"
                v-model="form.aufgabe"
                placeholder="Deine Aufgabe bei MarbleFX"
                ref="aufgabe"
                />
                <input-error :message="form?.errors?.aufgabe" />
            </input-container>
            <input-container :full-width="true" v-if="isform?.location">
                <input-label name="location" label="Wohnort" />
                <input-element
                type="text"
                name="location"
                v-model="form.location"
                placeholder="Dein Wohnort/Region"
                ref="location"
                />
                <input-error :message="form?.errors?.location" />
            </input-container>

            <input-container :full-width="true" v-if="isform?.website">
                <input-label name="website" label="Website" />
                <input-element
                type="text"
                name="website"
                v-model="form.website"
                placeholder="Website"
                ref="website"
                />
                <input-error :message="form?.errors?.website" />
            </input-container>

            <input-container :full-width="true" v-if="isform?.fbd">
                <input-label name="fbd" label="Facebook ID" />
                <input-element
                type="text"
                name="fbd"
                v-model="form.fbd"
                placeholder="Facebook"
                ref="fbd"
                />
                <input-error :message="form?.errors?.fbd" />
            </input-container>

            <input-container :full-width="true" v-if="isform?.xch_newsletter">
                <!-- <input-label name="newsl" label="Newsletter" /> -->
                <!-- <input-element
                type="text"
                name="fbd"
                v-model="form.fbd"
                placeholder="Facebook"
                ref="fbd"
                /> -->
                <InputCheckbox
              name="xch_newsletter"
            v-model="form.xch_newsletter"
            ref="newsl"
            label="Newsletter abonnieren"

                >

                </InputCheckbox>
                <input-error :message="form?.errors?.newsl" />

            </input-container>
            <input-container :full-width="true" v-if="isForm?.about">
                <InputHtml
                name="about"
                v-model="form.about"
                placeholder="Über dich"
                ref="about"
                />
                <input-error :message="form?.errors?.about" />
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
import axios from "axios";
import InputGroup from "@/Application/Components/Form/InputGroup.vue";
import InputActionMessage from "@/Application/Components/Form/InputActionMessage.vue";
import InputContainer from "@/Application/Components/Form/InputContainer.vue";
import InputLabel from "@/Application/Components/Form/InputLabel.vue";
import InputElement from "@/Application/Components/Form/InputElement.vue";
import InputHtml from "@/Application/Components/Form/InputHtml.vue";
import InputError from "@/Application/Components/Form/InputError.vue";
import SectionForm from "@/Application/Components/Content/SectionForm.vue";
import InputButton from "@/Application/Components/Form/InputButton.vue";
import IconCal from "@/Application/Components/Icons/IconCal.vue";
import { useForm } from '@inertiajs/inertia-vue3';
import InputCheckbox from "@/Application/Components/Form/InputCheckbox.vue";
import { GetColumns } from "@/helpers";
import { route } from 'ziggy-js';
import dayjs from 'dayjs';
import customParseFormat from 'dayjs/plugin/customParseFormat';
dayjs.extend(customParseFormat);
import { toastBus } from '@/utils/toastBus';

export default {
  name: "PersonalProfile",
  props: {
    initialForm: {
      type: Object,
      required: false,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
    user: {
      type: Object,
      required: false,
    },
  },
  components: {
    SectionForm,
    InputGroup,
    InputContainer,
    InputLabel,
    InputElement,
    InputHtml,
    InputError,
    IconCal,
    InputButton,
    InputCheckbox,
    InputActionMessage,
  },
  data() {
    return {
        isform: {},
        inputBirthday: this.initialForm?.birthday
      ? dayjs(this.initialForm.birthday).format('DD.MM.YYYY')
      : '',
      form: this.$inertia.form({
        birthday: this.initialForm?.birthday || '',
        xch_newsletter: this.initialForm?.xch_newsletter || '0',
        music: this.initialForm?.music || '',
        occupation: this.initialForm?.occupation || '',
        interests: this.initialForm?.interests || '',
        fbd: this.initialForm?.fbd || '',
        about: this.initialForm?.about || '',
        headline: this.initialForm?.headline || '',
        website: this.initialForm?.website || '',
        aufgabe: this.initialForm?.aufgabe || '',
        location: this.initialForm?.location || '',
      }),
    };
  },
  computed: {
    // formattedBirthday: {
    //   get() {
    //     return this.form.birthday
    //       ? dayjs(this.form.birthday).format('DD.MM.YYYY')
    //       : '';
    //   },
    //   set(value) {
    //     if (value.length === 10) {
    //       const parsed = dayjs(value, 'DD.MM.YYYY', true);
    //       if (parsed.isValid()) {
    //         this.form.birthday = parsed.format('YYYY-MM-DD');
    //         this.form.errors.birthday = null;
    //       } else {
    //         this.form.errors.birthday = 'Ungültiges Datum';
    //       }
    //     }
    //   },
    // },
  },
  methods: {
    syncBirthday() {
  const parsed = dayjs(this.inputBirthday, 'DD.MM.YYYY', true);
  if (parsed.isValid()) {
    this.form.birthday = parsed.format('YYYY-MM-DD');
    this.form.errors.birthday = null;
  } else {
    this.form.errors.birthday = 'Ungültiges Datum';
    this.form.birthday = '';
  }
},
async updateProfileInformation() {
  this.syncBirthday();

  // Payload NUR mit echten Spalten aufbauen
  const payload = {};
  Object.keys(this.isform).forEach((col) => {
    if (this.form[col] !== undefined) {
      payload[col] = this.form[col] ?? null;
    }
  });

  // Birthday separat einfügen, falls er gesetzt ist
  if (this.form.birthday) {
    payload.birthday = this.form.birthday;
  }

  try {
    const response = await axios.post(route('personal.update'), payload);

    this.inputBirthday = this.form.birthday
      ? dayjs(this.form.birthday).format('DD.MM.YYYY')
      : '';

    window.scrollTo(window.pageXOffset, window.pageYOffset);
    toastBus.emit('toast', response.data);
  } catch (error) {
//     console.log("Fehler", error.response?.data || error.message);
    if (error.response?.data?.errors) {
      this.form.errors = error.response.data.errors;
    }
  }
}
,

    validateDateInput(event) {
      // Logik aktuell deaktiviert
    },

    onBirthdayBlur() {
        this.syncBirthday();
        //   const parsed = dayjs(this.inputBirthday, 'DD.MM.YYYY', true);
//   if (parsed.isValid()) {
//     this.form.birthday = parsed.format('YYYY-MM-DD');
//     this.form.errors.birthday = null;
//   } else {
//     this.form.errors.birthday = 'Ungültiges Datum';
//     this.form.birthday = '';
//   }
},
  },
  async mounted() {
//     console.log("form object:", this.form);
//     console.log("Birthday bei Submit:", this.form.birthday);
  if (this.form.birthday) {
    this.inputBirthday = dayjs(this.form.birthday).format('DD.MM.YYYY');
  }

//     console.log("RAW birthday:", this.initialForm.birthday);
    if (this.form.birthday) {
//       console.log("Formatted birthday:", this.initialForm.birthday);
    }
    this.isform = await GetColumns("users");
    },
    watch: {
          'form.xch_newsletter'(val) {
//     console.log("Newsletter:", val);
  },
  initialForm: {
    handler(newVal) {
      if (newVal) {
        this.form.music = newVal.music || '';
        this.form.occupation = newVal.occupation || '';
        this.form.interests = newVal.interests || '';
        this.form.fbd = newVal.fbd || '';
        this.form.website = newVal.website || '';
        this.form.about = newVal.about || '';
        this.form.aufgabe = newVal.aufgabe || '';
        this.form.location = newVal.location || '';
        this.form.birthday = newVal.birthday || '';
        this.form.headline = newVal.headline || '';
        this.form.xch_newsletter = newVal.xch_newsletter || '';
        this.inputBirthday = newVal.birthday
          ? dayjs(newVal.birthday).format('DD.MM.YYYY')
          : '';
      }
    },
    deep: true,
    immediate: true,
  },
},

};
</script>


    <style>
    .DateIcon {
        position: relative;
        top: -104px !important;
        left: 695px;
    }
    </style>
