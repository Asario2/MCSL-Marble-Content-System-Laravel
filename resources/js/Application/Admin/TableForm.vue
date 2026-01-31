<template>
    <layout>
        <MetaHeader title="Eintrag Bearbeiten"></MetaHeader>
        <template #header>
            <breadcrumb
                :breadcrumbs="breadcrumbs"
                :current="'Eintrag ' + editstate"
            ></breadcrumb>
        </template>

        <section-form>
            <template #title>{{ItemName}}-Daten</template>
            <template #description
                >Hier kannst du alle Daten {{ItemName_des}} ändern



            </template>
            <template #form>
                <!-- Liste der Fehler -->
                <error-list :errors="errors" />

                <div class="flex justify-between items-center mb-4">
                    <!-- Linker Teil: Subtitle -->
                    <InputSubtitle>Daten</InputSubtitle>

                    <!-- Rechter Teil: Add Button -->
                    <Addbtn :table="CleanTable_alt()" class="mt-5"/>
                </div>
                <template style="display: inline-block">


                        <input-group>

    <form @submit.prevent="submitForm" enctype="multipart/form-data" >
        <div class="textar maxx grid grid-cols-1 lg:grid-cols-2 mb-2 gap-2 lg:gap-x-4 mt-2">
            <template v-for="(field, key) in  localFfo.original" :key="key">
                <input-container v-if="field.name === 'reading_time'">
                    <InputFormText
                        :id="field.name"
                        :name="field.name"
                        v-model="readingTime"
                        :placeholder="field.placeholder || ''"
                        readonly
                        :disabled="true"
                        class="cursor-not-allowed"
                        :required="isRequired(field.required)"
                        @input="handleInput_alt(field.name)"
                        >
                        <template #label>{{ field.label }}</template>
                    </InputFormText>
                </input-container>

                <input-container v-else-if="field.class === 'position'">
                    <InputPosition
                        :id="field.name"
                        :name="field.name"
                        :entries="entries"
                        :currentId="field.id"
                        :currentPosition="field.value"
                        @position-changed="newPosition = $event"
                        :placeholder="field.placeholder || ''"
                        :required="isRequired(field.required)"
                        >
                        <template #label>{{ field.label }}</template>
                    </InputPosition>
                </input-container>

                <input-container v-if="field.type === 'autoSlug'">
                    <InputFormText
                        :id="field.name"
                        :name="field.name"
                        v-model="aslug"
                        :placeholder="field.placeholder || ''"
                        readonly
                        :disabled="true"
                        class="cursor-not-allowed"
                        :value="aslug"
                        :required="isRequired(field.required)"
                        @input="handleInput"
                        >
                        <template #label>{{ field.label }}</template>
                    </InputFormText>
                </input-container>

                <input-container v-else-if="['textarea_nohtml'].includes(field.class)" :full-width="true">
                    <InputTextarea
                        :id="field.name"
                        :name="field.name"
                        v-model="field.value"
                        :rows="field.rows"
                        cols="25"
                        :placeholder="field.placeholder || ''"
                        :class="field.class"
                        @focus="isFocused = true"
                        @validationFailed="() => handleValidationFailed(index, field.required)"
                        @validationPassed="() => handleValidationPassed(index)"
                        :required="isRequired(field.required)"
                    >
                        <template #label>{{ field.label }}</template>
                    </InputTextarea>
                    <div v-if="fieldErrors && fieldErrors[key]" class="text-red-500 text-sm">Dieses Feld ist erforderlich</div>
                </input-container>

                <!-- Textarea für den Inhalt -->
                <input-container v-else-if="['textarea', 'textarea_short'].includes(field.type)" :full-width="true">
                    <Editor ref="editor"
                        :id="field.name"
                        :name="field.name"
                        v-model="field.value"
                        :rows="field.rows"
                        cols="25"


                        :vcol="field.value"
                        :placeholder="field.placeholder || ''"
                        :class="field.class"
                        @focus="isFocused = true"
                        @validationFailed="() => handleValidationFailed(index, field.required)"
                        @validationPassed="() => handleValidationPassed(index)"
                        :required="isRequired(field.required)"
                        @input="handleInput_alt(field.name)"
                        @paste="handleInput_alt(field.name, $event)"
                        @keyup="handleInput_alt(field.name, $event)"
                        @content-updated="handleInput_alt(field.name, $event)"
                    >
                        <template #label>{{ field.label }}</template>
                    </Editor>
                    <div v-if="fieldErrors && fieldErrors[key]" class="text-red-500 text-sm">Dieses Feld ist erforderlich</div>
                </input-container>

                <input-container v-else-if="field.type === 'textarea'">
                    <InputFormTextarea
                        :id="field.name"
                        :name="field.name"
                        v-model="field.value"
                        :placeholder="field.placeholder || ''"
                        :required="isRequired(field.required)"
                        >
                        <template #label>{{ field.label }}</template>
                    </InputFormTextarea>
                </input-container>

                <input-container v-else-if="field.type === 'text'">
                    <InputFormText
                        :id="field.name"
                        :name="field.name"
                        :value="field.value"
                        v-model="field.value"
                        :placeholder="field.placeholder || ''"
                        :required="isRequired(field.required)"
                        :disabled="field.disabled"
                        :class="field.disabled ? 'cursor-not-allowed' : ''"
                        >
                        <template #label>{{ field.label }}</template>
                    </InputFormText>
                </input-container>

                <input-container v-else-if="field.type === 'price'">
                    <InputFormPrice
                        :id="field.name"
                        :name="field.name"
                        v-model="field.value"
                        :placeholder="field.placeholder || ''"
                        :required="isRequired(field.required)"
                        >
                        <template #label>{{ field.label }}</template>
                    </InputFormPrice>
                </input-container>
                <input-container v-else-if="field.class === 'poster_id'">
                    <InputFormPoster
                        :id="field.name"
                        :name="field.name"
                        v-model="field.value"
                        :placeholder="field.placeholder || ''"
                        :required="isRequired(field.required)"
                        >
                        <template #label>{{ field.label }}</template>
                    </InputFormPoster>
                </input-container>
                <input-container v-else-if="field.type ==='imul'">
                    <ImageUploadModal
                        v-if="modals[field.name]"
                        :is-modal-open="modals[field.name]"
                        :column="field.name"
                        :isModalOpen="modals[field.name]"
                        :is_imgdir = "false"
                        @close="modals[field.name] = false"
                        @imageUploaded="handleImageUploaded(field.name, $event)"
                        v-model="field.value"
                        :namee="field.value"
                        :alt_path="'_' + subdomain + '/' + CleanTable_alt() + '/' + field.name"
                        :domain="subdomain"
                        :tablex="table_x"
                        :path="tablex"
                        :ref="field.name"
                        :value="imageId"
                        :image="field.value"
                        :namee2="field.name"
                        :Message="false"
                    />

                    <button type="button" @click="openModal(field.name)">
                        <b>{{ field.label }}</b><br />
                        <img
                            :src="getPreviewSrc(field)"
                            alt="Vorschau1"
                            :id="'com_'+field.name"
                            style="float:left;margin-right:12px;max-height:120px;max-width:100px;"
                        />
                    </button>
                    <input type="hidden" :id="field.name" :name="field.name"  v-model="field.value" />
                </input-container>

                <input-container v-else-if="field.type ==='imgal'">
                    <ImageUploadModal
                        v-if="modals[field.name]"
                        :is-modal-open="true"
                        :column="field.name"
                        :is_imgdir = "true"
                        :jspath="'/images/_mfx/images/imgdir_content/'+field.value+'/'"
                        :path="'/images/_mfx/images/imgdir_content/'+field.value+'/'"
                        :isModalOpen="modals[field.name]"
                        @close="modals[field.name] = false"
                        @reset="console.log('nur reset, nicht schließen')"
                        @imageUploaded="handleImageUploaded(field.name, $event)"
                        v-model="field.value"
                        :namee="field.value"
                        :alt_path="'_' + subdomain + '/' + CleanTable_alt() + '/' + field.name"
                        :domain="subdomain"
                        :tablex="table_x"
                        :ref="field.name"
                        :value="imageId"
                        :image="field.value"
                        :namee2="field.name"
                        :Message="false"
                        @refresh-preview="handleRefreshPreview"
                        @refresh-gallery="handleRefreshPreview"
                    />
                    <ImageJsonEditor
                        v-if="gals?.[field.name]"
                        :column="field.name"

                        ref="editor3"
                        :jspath="'/images/_mfx/images/imgdir_content/'+field.value+'/'"
                        :path="'/images/_mfx/images/imgdir_content/'+field.value+'/'"
                        :isGalOpen="gals[field.name]"
                        :folder="'/images/_mfx/images/imgdir_content/'+field.value+'/'"
                        @close="gals[field.name] = false"
                        @reset="console.log('nur reset, nicht schließen')"
                        @imageUploaded="handleImageUploaded(field.name, $event)"
                        v-model="field.value"
                        :namee="field.value"
                        :alt_path="'_' + subdomain + '/' + CleanTable_alt() + '/' + field.name"
                        :domain="subdomain"
                        :tablex="table_x"
                        :value="imageId"
                        :image="field.value"
                        :namee2="field.name"
                        :Message="false"
                        @refresh-preview="handleRefreshPreview"
                        @refresh-gallery="handleRefreshPreview"
                    />

                    <button type="button" @click="openModal(field.name)">
                        <span style='float:left;'><b>Upload</b><br />
                        <div v-html="previewHtml"></div></span>
                    </button>
                    <button type="button" @click="openGal(field.name)">
                        <span style='float:left;'><b>Galerie</b><br />
                        <div v-html="previewgal"></div></span>
                    </button>
                    <input type="hidden" :name="field.name" :id="field.name"  v-model="field.value" />
                </input-container>

                <input-container v-else-if="field.type === 'pub'">
                    <PublicRadio v-model.number="field.value" :is_created="is_created" :valx="field.value">
                        <template #icon-public>
                            <img :src="'/images/icons/online.png'" alt="" class="w-6 h-6" />
                        </template>
                        <template #icon-private>
                            <img :src="'/images/icons/offline.png'" alt="" class="w-6 h-6" />
                        </template>
                    </PublicRadio>
                </input-container>

                <input-container v-else-if="field.type ==='IID'">
                    <ImageUploadModal
                        :alt_path="field.class === 'profile' ? 'profile_photos' : ''"
                        v-show="modals[field.name]"
                        :isModalOpen="modals[field.name]"
                        :tablex="table_x"
                        :is_imgdir = "false"
                        :column="field.name"
                        :path="tablex"
                        :ref="field.name"
                        :value="imageId"
                        :image="field.value"
                        :namee="field.value"
                        :namee2="field.name"
                        :Message="false"
                        @close="modals[field.name] = false"
                        @imageUploaded="handleImageUploaded(field.name, $event)"
                        v-model="field.value"
                    />

                    <button type="button" @click="openModal(field.name)">
                        <img
                            :src="getPreviewSrc(field)"
                            class="max-w-[160px] min-w-[160px] max-h-[90px] object-contain"
                            alt="Vorschau2"
                            :id="'com_'+field.name"
                        />
                    </button>
                    <input type="hidden" :name="field.name" :id="field.name" v-model="field.value" />
                </input-container>

                <input-container v-else-if="field.type === 'datetime'">
                    <InputFormDateTime
                        :id="field.name"
                        :name="field.name"
                        :ref="field.name"
                        v-model="field.value"
                        :placeholder="field.placeholder || ''"
                        :disabled="field.class !== 'datetime'"
                        :class="field.class !== 'datetime' ? 'cursor-not-allowed' : ''"
                        :required="isRequired(field.required)"
                        >
                        <template #label>{{ field.label }}</template>
                    </InputFormDateTime>
                </input-container>

                <input-container v-else-if="field.type === 'date'">
                    <InputFormDate
                        :id="field.name"
                        :name="field.name"
                        :ref="field.name"
                        v-model="field.value"
                        :placeholder="field.placeholder || ''"
                        :disabled="field.class !== 'date'"
                        :class="field.class !== 'date' ? 'cursor-not-allowed' : ''"
                        :required="isRequired(field.required)"
                        >
                        <template #label>{{ field.label }}</template>
                    </InputFormDate>
                </input-container>

                <input-container v-else-if="field.type === 'password'">
                    <InputPWD
                        :id="field.name"
                        :name="field.name"
                        :ref="field.name"
                        v-model="field.value"
                        :placeholder="field.placeholder || ''"
                        :disabled="field.class !== 'password'"
                        :class="field.class !== 'password' ? 'cursor-not-allowed' : ''"
                        :required="isRequired(field.required)"
                        >
                        <template #label>{{ field.label }}</template>
                    </InputPWD>
                </input-container>

                <input-container v-else-if="field.type === 'checkbox'" :full-width="true">
                    <input-checkbox
                        :name="field.name"
                        v-model="field.value"
                        :exValue="field.value"
                        :value="field.value"
                        :disabled="field.disabled"
                        :class="field.disabled ? 'cursor-not-allowed' : ''"
                        >
                        {{field.label}}
                    </input-checkbox>
                </input-container>

                <input-container v-else-if="field.type === 'isbox'">
                    <input-isbox
                        :id="field.name"
                        :exValue="field.value"
                        :name="field.name"
                        v-model.number="field.value"
                        >
                        {{field.label}}
                    </input-isbox>
                </input-container>

                <input-container v-else-if="field.name === 'users_id'">
                    <InputLabel :name="field.name" :label="field.label"></InputLabel>
                    <InputSelectU
                        @input-change="updateFormData"
                        :id="field.name"
                        :model-value="field.value"
                        :disabled="field.disabled"
                        :class="field.disabled ? 'cursor-not-allowed' : ''"
                        :name="field.name"
                        :xname="field.name"
                        :required="isRequired(field.required)"
                    ></InputSelectU>
                </input-container>

            <input-container
                v-else-if="field.type === 'select_id'"

            >
                <!-- Weitere Inhalte für die select_id-Komponente -->

                <InputLabel
                :name="field.name"
                :label="field.label">

            </InputLabel>
            <InputSelect
            @input-change="updateFormData"
            :id="field.name"
            v-model="field.value"
            :model-value="field.value"
            :disabled="field.disabled"
            :class="field.disabled ? 'cursor-not-allowed' : ''"
            :name="field.name"
            :xname="field.name"
            :required="isRequired(field.required)"
            >

        </InputSelect>

        </input-container>

                <input-container v-else-if="field.type === 'select_itemscope'">
                    <InputLabel :name="field.name" :label="field.label"></InputLabel>
                    <InputSelectEnum
                        @input-change="updateFormData"
                        :id="field.name"
                        :model-value="field.value"
                        v-model="field.name"
                        :options="`options: ${this.xsor_alt[field.name]?.length > 0 ? this.xsor_alt[field.name] : []}`"
                        ref="field.name"
                        :name="field.name"
                        :xval="field.value"
                        :xname="field.name"
                        :tablex="tablex"
                        :required="isRequired(field.required)"
                        :disabled="field.disabled"
                        :class="field.disabled ? 'cursor-not-allowed' : ''"
                    ></InputSelectEnum>
                </input-container>

                <input-container v-else-if="field.type === 'artselect'">
                    <ArtSelect :id="field.id" :table="this.tablex" :form="field"
                        @update:category="form.categorie_id = $event"
                        @update:medium="form.type_id = $event" />
                </input-container>

                <input-container v-else-if="field.type === 'select'">
                    <InputLabel :name="field.name" :label="field.label"></InputLabel>
                    <InputSelectEnum
                        @input-change="updateFormData"
                        :id="field.name"
                        :model-value="field.name"
                        v-model="field.value"
                        :options="`options: ${this.xsor_alt[field.name]?.length > 0 ? this.xsor_alt[field.name] : []}`"
                        ref="field.name"
                        :name="field.name"
                        :xval="field.value"
                        :xname="field.name"
                        :tablex="tablex"
                        :required="isRequired(field.required)"
                        :disabled="field.disabled"
                        :class="field.disabled ? 'cursor-not-allowed' : ''"
                    ></InputSelectEnum>
                </input-container>
            </template>
        </div>

        <!-- <button type="submit" class="bg-blue-500 text-white p-2 rounded">
            Absenden
        </button> -->
    </form>
</input-group>

                </template>
            </template>

            <template #actions style="display:inline-block;">
                <!-- Befehle -->
                <button-group>
                    <input-danger-button
                        v-if="table.id > 0"
                        type="button"
                        @click.prevent="confirmTableDeletion"
                    >
                        {{ItemName}} löschen
                    </input-danger-button>
                    <smooth-scroll href="#app-layout-start" v-if="table.id > 0">
                        <input-button
                            type="button"
                            @click="submitForm"

                        >
                            {{ItemName}} Speichern
                        </input-button>
                    </smooth-scroll>
                    <smooth-scroll
                        href="#app-layout-start"
                        v-if="table.id == 0"
                    >
                        <input-button
                            type="button"
                            @click="submitForm"
                        >
                            {{ItemName}} erstellen
                        </input-button>
                    </smooth-scroll>
                </button-group>
                <!-- ENDS Befehle -->
            </template>
        </section-form>

        <!-- Delete Blog Confirmation Modal -->
        <dialog-modal
            :show="confirmingTableDeletion"
            @close="close_confirmingTableDeletion"
        >
            <template #title> {{ItemName}} löschen </template>

            <template #content>
                Bist du sicher, dass du diesen {{ItemName}} löschen willst?
            </template>

            <template #footer>
                <button-group>
                    <input-button @click="close_confirmingTableDeletion">
                        Zurück
                    </input-button>

                    <input-danger-button @click="deleteTable">
                        {{ItemName}} jetzt löschen
                    </input-danger-button>
                </button-group>
            </template>
        </dialog-modal>
    </layout>
</template>


<script>
    let tablex = CleanTable();
const tablex3 = tablex;
let xid3 = CleanId();

const routes = {
    getform: () => `/tables/form-data/${tablex3}/${xid3}`,
    getselroute: (name) => `/tables/sort-data/${name}`,
    getselenumroute: (table, name) => `/tables/sort-enum/${table}/${name}`,
    getselenumisroute: (table, name) => `/tables/sort-enumis/${table}/${name}`,
    putdata: (tablex, id) => `admin/tables/update/${tablex}/${id}`,
};

import {
    defineComponent
} from "vue";
import emitter from "@/eventBus";
import {
    GetSettings
} from "@/helpers";
import axios from "axios";
import {
    CleanTable,
    CleanId,
    CheckTRights
} from '@/helpers';
import ImageUploadModal from '@/Application/Components/ImageUploadModal.vue';
import InputPosition from '@/Application/Components/InputPosition.vue';


import Layout from "@/Application/Admin/Shared/ab/Layout.vue";
import Breadcrumb from "@/Application/Components/Content/Breadcrumb.vue";
import SmoothScroll from "@/Application/Components/SmoothScroll.vue";

import SectionForm from "@/Application/Components/Content/SectionForm.vue";
import InputPWD from "@/Application/Components/Form/InputPWD.vue";
import Addbtn from "@/Application/Components/Form/addbtn.vue";
import InputFormPoster from "@/Application/Components/Form/InputFormPoster.vue";
import ButtonGroup from "@/Application/Components/Form/ButtonGroup.vue";
import InputButton from "@/Application/Components/Form/InputButton.vue";
import ArtSelect from "@/Application/Components/Form/ArtSelect.vue";
import InputFormText from "@/Application/Components/Form/InputFormText.vue";
import InputFormDateTime from "@/Application/Components/Form/InputFormDateTime.vue";
import InputFormDate from "@/Application/Components/Form/InputFormDate.vue";
import InputDangerButton from "@/Application/Components/Form/InputDangerButton.vue";
import ErrorList from "@/Application/Components/Form/ErrorList.vue";
import InputSubtitle from "@/Application/Components/Form/InputSubtitle.vue";
import InputGroup from "@/Application/Components/Form/InputGroup.vue";
import InputContainer from "@/Application/Components/Form/InputContainer.vue";
import InputLabel from "@/Application/Components/Form/InputLabel.vue";
import InputCheckbox from "@/Application/Components/Form/InputCheckbox.vue";
import InputIsbox from "@/Application/Components/Form/InputIsBox.vue";
import InputSelect from "@/Application/Components/Form/InputSelect.vue";
import InputSelectU from "@/Application/Components/Form/InputSelectU.vue";
import InputSelectEnum from "@/Application/Components/Form/InputSelectEnum.vue";
import InputFormPrice from "@/Application/Components/Form/InputFormPrice.vue";
import InputTextarea from "@/Application/Components/Form/InputTextarea.vue";
import Editor from "@/Application/Components/Form/InputHtml.vue";
import DialogModal from "@/Application/Components/DialogModal.vue";
import PublicRadio from "@/Application/Components/Form/PublicRadio.vue";
import {
    reactive,
    nextTick
} from "vue";
import ImageJsonEditor from "@/Application/Admin/ImageJsonEditor.vue";
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";

export default defineComponent({
            name: "Admin_TableForm",
            components: {
                Layout,
                Addbtn,
                Breadcrumb,
                SmoothScroll,
                PublicRadio,
                InputSelectU,
                InputFormDateTime,
                InputFormText,
                SectionForm,
                ButtonGroup,
                InputButton,
                InputIsbox,
                InputDangerButton,
                ErrorList,
                InputSubtitle,
                InputContainer,
                InputGroup,
                InputLabel,
                InputCheckbox,
                InputSelect,
                InputSelectEnum,
                InputTextarea,
                InputPWD,
                Editor,
                MetaHeader,
                InputFormDate,
                InputFormPrice,
                ImageJsonEditor,
                DialogModal,
                ArtSelect,
                ImageUploadModal,
                InputPosition,
                InputFormPoster
            },
            props: {
                imageId: String,
                newField: {
                    type: String
                },
                id: {
                    type: [String, Number],
                    required: true
                },
                entry: Object,
                modelValue: {
                    type: [String, Number]
                },
                input2: {
                    type: [Object, Array]
                },
                xval: {
                    type: [String, Number],
                    default: 1
                },
                ffo: {
                    type: [Object, Array],
                    default: () => ({
                        original: {}
                    })
                },
                editstate: {
                    type: String,
                    default: ""
                },
                table_authors: {
                    type: Object,
                    default: () => ({})
                },
                table_categories: {
                    type: Object,
                    default: () => ({})
                },
                table_images: {
                    type: Object,
                    default: () => ({})
                },
                errors: {
                    type: Object,
                    default: () => ({})
                },
                breadcrumbs: {
                    type: Object,
                    required: true
                },
                users_id: {
                    type: [Number, String],
                    default: 0
                },
                tablex: {
                    type: String,
                    default: ''
                },
            },
            data() {
                return {
                    isLoading: false,
                    loadingText: "",
                    gals: [],
                    isModalOpen: false,
                    ulpath: '',
                    GalOpen: false,
                    rights: {
                        add: null,
                        view: null,
                        pub: null,
                        edit: null,
                        delete: null
                    },
                    previewHtml: '',
                    table: reactive({
                        id: "1"
                    }),
                    formDatas: {},
                    oobj: {},
                    formData: {},
                    localFfo: JSON.parse(JSON.stringify(this.ffo ?? {})),
                    sanitizedContent: '',
                    uploadedIid: null,
                    ItemName: "Beitrag",
                    table_x: '',
                    aslug: '',
                    nf2: '',
                    previewImages: {},
                    newPosition: null,
                    subdomain: window.subdomain || '',
                    fieldtype: "",
                    readingTime: "",
                    fileName: '',
                    sortedOptions: "",
                    so: [],
                    xsor_alt: {},
                    isOpen: true,
                    uploadedImageUrl: null,
                    csrfToken: document.getElementById('token')?.value,
                    preview_image: {},
                    ffo: {
                        ...this.entry
                    },
                    options: {},
                    options_sel: {},
                    sdata: {},
                    entries: [],
                    loadingText: null,
                    confirmingTableDeletion: false,
                    table_alter: '',
                    modals: {},
                    field: {},
                    fieldErrors: {},
                    isFocused: false,
                    loadingPreview: false,
                };
            },
            computed: {
                is_created() {
                    return window.location.pathname.includes("create");
                },
                previewgal() {
                    return "<img src='/images/icons/gal.jpg' />";
                },
                sortedOptions_sel() {
                    if (Array.isArray(this.options_sel)) return [...this.options_sel];
                    if (typeof this.options_sel === 'object') return Object.entries(this.options_sel).map(([k, v]) => [k, v]);
                    return [];
                },
                filters() {
                    return this.$page.props.filters;
                },
                datarows() {
                    return this.$page.props.datarows;
                },
                rows() {
                    return this.$page.props.rows;
                },
                table_alt() {
                    return this.$page.props.table_alt;
                },
                tablez() {
                    return this.$page.props.tablez;
                },
                tables() {
                    return this.$page.props.tables;
                },
            },
            watch: {
                entry(newVal) {
                    this.ffo = {
                        ...newVal
                    };
                },
                ffo: {
                    handler(newVal) {
                        if (newVal && Object.keys(newVal).length) this.localFfo = JSON.parse(JSON.stringify(newVal));
                    },
                    immediate: true
                },
                'field.value': {
                    immediate: true,
                    handler(newId) {
                        if (newId) this.fetchImage(newId, this.tablex);
                    }
                },
                field: {
                    handler(newField) {
                        this.readingTime = newField?.value || 1;
                    },
                    deep: true,
                    immediate: true
                },
            },
            methods: {
                startLoading(text = "Laden...") {
                    this.isLoading = true;
                    this.loadingText = text;
                },
                stopLoading() {
                    this.isLoading = false;
                    this.loadingText = "";
                },
                CleanTable,
                CleanTable_alt() {
                    return CleanTable();
                },
                handleModalClose(fieldName) {
                    this.modals[fieldName] = false;
                },
                handleRefreshPreview() {
                    this.getPreviewImagez();
                },
                async getPreviewImagez() {
                    // Spinner aktivieren
                    this.loadingPreview = true;

                    try {
                        const field = this.field;
                        const subdomain = window.subdomain || '';
                        const tableFolder = CleanTable() !== "users" ? "thumbs/" : "";
                        const ppa = `/images/_${subdomain}/images/${field.name}/${field.value}/index.json`;

                        // Wenn kein gültiger Pfad, Standard-Icon anzeigen
                        if (!field.value || ppa.includes("undefined/")) {
                            this.previewHtml = '<img src="/images/icons/upl.png" alt="Jetzt Bild Hochladen" width="122" title="Jetzt Bild Hochladen" style="float: left; margin-right: 12px;">';
                            return;
                        }

                        // API-Abfrage
                        const response = await axios.get(ppa);
                        this.images = response.data;

                        // Prüfen, ob JSON gültig ist
                        if (!this.validJson(this.images) || !Array.isArray(this.images) || this.images.length === 0) {
                            this.previewHtml = '<img src="/images/icons/upl.jpg" alt="Jetzt Bild Hochladen" width="200" title="Jetzt Bild Hochladen" style="float: left; margin-right: 12px;">';
                            return;
                        }

                        // Vorschau der ersten 5 Bilder generieren
                        let conta = '';
                        for (let i = 0; i < Math.min(5, this.images.length); i++) {
                            const filename = this.images[i]['filename'];
                            const src = `/images/_${subdomain}/images/${field.name}/${field.value}/${tableFolder}${this.cc(filename)}`;
                            conta += `<img width="100" class='mt-3' alt="Vorschau" title="Vorschau" id="comm_${field.name}" style="float:left;margin-right:12px;" src="${src}" />`;
                        }

                        this.previewHtml = conta;

                    } catch (err) {
                        console.error('Fehler beim Laden der Vorschau:', err);
                        this.previewHtml = '<p style="color:red;">Fehler beim Laden der Vorschau</p>';
                    } finally {
                        // Spinner deaktivieren
                        this.loadingPreview = false;
                    }
                },

                handleImageUploaded(fieldName, fileName) {
                    this.localFfo.original[fieldName].value = fileName;
                    this.thumb = CleanTable() != "users" ? "thumbs/" : '';
                    this.previewImages[fieldName] = `/images/_${this.subdomain}/${this.CleanTable_alt()}/${fieldName}/${this.thumb}${fileName}`;
                    this.modals[fieldName] = false;
                },
                remom(data) {
                    if (!data || typeof data !== 'object') return {};
                    const result = {};
                    if (data.original && typeof data.original === 'object') {
                        Object.entries(data.original).forEach(([key, field]) => {
                            if (field && typeof field === 'object') {
                                result[field.name] = field.value || "";
                                this.kk = key;
                            }
                        });
                    }
                    return result;
                },
                getPreviewSrc(field) {
                    if (this.previewImages[field.name]) return this.previewImages[field.name];
                    this.thumb = CleanTable() != "users" ? "thumbs/" : '';
                    if (field.value && field.value !== '008.jpg') return `/images/_${this.subdomain}/${this.CleanTable_alt()}/${field.name}/${this.thumb}${field.value}`;
                    return '/images/icons/upl.jpg';
                },
                openModal(name) {
                    this.modals[name] = true;
                },
                openGal(name) {
                    this.gals[name] = true;
                },
                closeModal(name) {
                    this.modals[name] = false;
                },
                handleValidationFailed(index, isRequired) {
                    if (isRequired) this.fieldErrors[index] = true;
                },
                handleValidationPassed(index) {
                    this.fieldErrors[index] = true;
                },
                sanitizeContent(content) {
                    return content.includes('location') || content.includes('ancestorOrigins') ? '' : content;
                },
                async getSlug() {
                    try {
                        const response = await axios.get(`/api/getSlug/${this.xtable}/${this.xid}`);
                        return response.data.autoslug || "";
                    } catch (e) {
                        console.error("No Slug Found", e);
                        return "";
                    }
                },
                async getOF() {
                    this.xid = CleanId();
                    this.xtable = CleanTable();
                    try {
                        const response = await axios.get(`/api/images/${this.xtable}/${this.xid}`);
                        let apiValue = response.data;
                        const domEl = document.getElementById(this.column);
                        let domValue = domEl ? domEl.value : null;
                        let defaultValue = this.ffo?.[this.column]?.['value'] ?? '';
                        if (domValue && domValue !== "008.jpg") this.nf = domValue;
                        else if (apiValue && apiValue !== "[]" && apiValue !== null) this.nf = apiValue;
                        else this.nf = defaultValue;
                        return this.nf;
                    } catch (error) {
                        console.error("Not fetchable", error);
                        return this.ffo?.[this.column]?.['value'] ?? '';
                    }
                },
                async fetchImage(id, table) {
                    if (!id) id = this.imageId;
                    if (!id) return;
                    try {
                        const response = await axios.get(`/api/images/${table}/${this.xid}`);
                        if (response.data.url) this.ulimage = response.data.url;
                        else console.warn("Keine URL gefunden für ID:", id);
                    } catch (error) {
                        console.error("Fehler beim Laden des Bildes:", error);
                    }
                },
                handleInput(event) {
                    this.readingTime = event.target.value;
                    this.updateFormData();
                },
                async handleInput_alt(fieldName, content) {
                    await nextTick();
                    this.updateReadingTime("content_alt");
                },
                updateReadingTime() {
                    const element = Array.isArray(this.$refs.editor) ? this.$refs.editor[0] : this.$refs.editor;
                    if (!element) {
                        console.error("Element not found (Ref): editor");
                        return;
                    }
                    const value = element?.value ?? '';
                    this.readingTime = this.calculateReadingTime(value);
                },
                calculateReadingTime(text) {
                    text = text.replace(/<[^>]+>/g, "").trim();
                    const words = text?.trim().split(/\s+/).length;
                    const minutes = words / 200;
                    return Math.ceil(minutes);
                },
                removeNumericKeys(obj) {
                    let newObj = {};
                    Object.values(obj).forEach(v => Object.assign(newObj, v));
                    return newObj;
                },
                isRequired(value) {
                    return value === "required" || value === true;
                },
                updateFormData(value, fieldName) {
                    if (fieldName.includes("_id") || this.fieldtype == "select") this.formDatas[fieldName] = value;
                    if (fieldName.includes("_iid")) this.formData[fieldName] = value;
                    if (fieldName == "img_x" || fieldName == "img_y") this.formData[fieldName] = this.value;
                    if (this.fieldtype == "autoslug") this.formData[fieldName] = value;
                    if (this.fieldname == this.column) this.formData[this.column] = value;
                },
                stripslashes(input) {
                    if (typeof input === 'string') return input.replace(/\\(.)/g, '$1');
                    else if (Array.isArray(input)) return input.map(i => this.stripslashes(i));
                    else if (typeof input === 'object' && input !== null) {
                        let r = {};
                        for (let k in input) {
                            if (Object.prototype.hasOwnProperty.call(input, k)) r[k] = this.stripslashes(input[k]);
                        }
                        return r;
                    }
                    return input;
                },
                setFormField(field) {
                    if (field.name == "reading_time") this.formData['reading_time'] = this.readingTime;
                    if (field.type == "IID") this.formData[field.name] = this.ref2;
                    if (field.type === "select_id") this.getsel(field.name);
                    if (field.type === "select") this.getsel_enum(field.name, this.tablex);
                    if (field?.name?.includes("_id")) this.formData[field.name] = this.formDatas[field.name];
                    else if (field.type === "IID") this.formData[field.name] = this.formDatas[field.name];
                    else if (field.type === "select") this.formData[field.name] = this.formDatas[field.name];
                    else if (field.name == "img_x" || field.name == "img_y" || field.type == "autoslug") this.formData[field.name] = field.value;
                    else if (field && field.name) {
                        if (!this.formData) this.formData = {};
                        this.formData[field.name] = field.value || "";
                    }
                },
            },
        });
</script>
<style>
select,datetime-local   {
    max-width:560px !important;
}
.editor a:link,.editor a:visited,.editor a:active,.editor a {
  color:rgb(14 165 233) !important;
  text-decoration: underline;
}
@media (min-width: 1024px) {
}
.editor-error {
  border: 2px solid red;
  border-radius: 4px;
}
.editor-error-message {
  color: red;
  font-size: 0.9em;
  margin-top: 4px;
}
.smbg SPAN{
    display:inline !important;
}
</style>

