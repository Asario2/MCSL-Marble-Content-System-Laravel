<template>
    <layout>
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
                <!-- Loading -->
                <input-loading
                    :loading="loading"
                    :loading-text="loadingText"
                ></input-loading>
                <!-- ENDS Loading -->
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
                        @input="handleInput"
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
                            class="max-w-[100px] object-contain"
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
                        :exValue.Number="field.value"
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
                        :exValue.Number="field.value"
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
const id = CleanId();
let tablex = CleanTable();
const table_z = tablex;
const tablex3 = tablex;
let table = tablex;
let xid3 = CleanId();

const routes = {
    getform: (tablex, id) => `/tables/form-data/${tablex3}/${xid3}`,
    getselroute: (name) => `/tables/sort-data/${name}`,
    getselenumroute: (table,name) => `/tables/sort-enum/${table}/${name}`,
    getselenumisroute: (table,name) => `/tables/sort-enumis/${table}/${name}`,
    putdata: (tablex,id) => `admin/tables/update/${tablex}/${id}`,
};

import { defineComponent } from "vue";

import emitter from "@/eventBus";
import { GetSettings } from "@/helpers";
import axios from "axios";

import { CleanTable, CleanId, cc } from '@/helpers';
import ImageUploadModal from '@/Application/Components/ImageUploadModal.vue';
import InputPosition from '@/Application/Components/InputPosition.vue';


import Layout from "@/Application/Admin/Shared/Layout.vue";
import Breadcrumb from "@/Application/Components/Content/Breadcrumb.vue";
import SmoothScroll from "@/Application/Components/SmoothScroll.vue";

import SectionForm from "@/Application/Components/Content/SectionForm.vue";
import InputPWD from "@/Application/Components/Form/InputPWD.vue";
import Addbtn from "@/Application/Components/Form/addbtn.vue"; // KORREKTUR: Import hinzugefügt
import InputFormPoster from "@/Application/Components/Form/InputFormPoster.vue";
import ButtonGroup from "@/Application/Components/Form/ButtonGroup.vue";
import InputButton from "@/Application/Components/Form/InputButton.vue";
import ArtSelect from "@/Application/Components/Form/ArtSelect.vue";
import InputFormText from "@/Application/Components/Form/InputFormText.vue";
import InputFormDateTime from "@/Application/Components/Form/InputFormDateTime.vue";
import InputFormDate from "@/Application/Components/Form/InputFormDate.vue";
import InputFormTextArea from "@/Application/Components/Form/InputFormTextArea.vue";
import InputDangerButton from "@/Application/Components/Form/InputDangerButton.vue";
import InputLoading from "@/Application/Components/Form/InputLoading.vue";
import ErrorList from "@/Application/Components/Form/ErrorList.vue";
import InputSubtitle from "@/Application/Components/Form/InputSubtitle.vue"; // KORREKTUR: Import hinzugefügt
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
import InputError from "@/Application/Components/Form/InputError.vue";

import DialogModal from "@/Application/Components/DialogModal.vue";

import { toastBus } from '@/utils/toastBus';
import { reactive } from "vue";
import PublicRadio from "@/Application/Components/Form/PublicRadio.vue";
import Alert from "@/Application/Components/Content/Alert.vue";
import ImageJsonEditor from "@/Application/Admin/ImageJsonEditor.vue";


export default defineComponent({
    name: "Admin_TableForm",

    components: {
        Layout,
        Addbtn, // KORREKTUR: Komponente registriert
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
        InputLoading,
        ErrorList,
        InputSubtitle, // KORREKTUR: Komponente registriert
        InputContainer,
        InputGroup,
        InputLabel,
        InputCheckbox,
        InputSelect,
        InputSelectEnum,
        InputTextarea,
        InputPWD,
        Editor,
        // InputFormTextArea,
        InputFormDate,
        InputFormPrice,
        ImageJsonEditor,
        // InputError,
        DialogModal,
        // Alert,
        ArtSelect,
        ImageUploadModal,
        InputPosition,
        InputFormPoster,
    },

    props: {
        imageId:String,
        newField:{
            type: String,
        },
        id: {
            type: [String, Number],
            required: true,
        },
        entry: Object,
        modelValue: {
            type: [String, Number],
        },
        input2:{
            type: [Object,Array],
        },
        xval: {
            type: [String, Number],
            default: 1,
        },
        ffo: {
        type: [Object, Array],
        default: () => ({ original: {} })
        },
        editstate: {
            type: String,
            default: "",
        },
        table_authors: {
            type: Object,
            default: () => ({}),
        },
        table_categories: {
            type: Object,
            default: () => ({}),
        },
        table_images: {
            type: Object,
            default: () => ({}),
        },
        errors: {
            type: Object,
            default: () => ({}),
        },
        breadcrumbs: {
            type: Object,
            required: true,
        },
        // id:{
        //     type: [String,Number],
        //     default: '1',
        // },
        users_id:{
            type: [Number,String],
            default:0,
        },
        tablex: {
            type: String,
            default: '',
        },

    },
    // DZ
    data() {
        return {
            gals:[],
            isModalOpen: false,
            ulpath: '',
            GalOpen:false,

            previewHtml: '',
            table: reactive({ id: "1" }),
            formDatas: {},
            oobj:{},
            formData: {},
            localFfo: JSON.parse(JSON.stringify(this.ffo ?? {})),
            sanitizedContent: '',
            uploadedIid: null,
            ItemName: "Beitrag",
            table_x: '',
            aslug: '',
            nf2:'',
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
            csrfToken: document.getElementById('token').value,
            preview_image: {},
            ffo: { ...this.entry },
            options: {},
            options_sel: {},
            sdata: {},
            loading: false,
            entries: [],
            loadingText: null,
            confirmingTableDeletion: false,
            table_alter:'',
            modals: {},
            field:{},
            fieldErrors: {},
            isFocused: false,
        };
    },

    computed: {
        is_created(){
            const path = window.location.pathname;
            return path.includes("create");
        },

        isRightsReady() {
            return this.$isRightsReady;
        },
        hasRight() {
            return this.$hasRight;
        },
        previewgal(){
            return "<img src='/images/icons/gal.jpg' />";
        },
        sortedOptions_sel() {
            let options_sel;
            if (Array.isArray(this.options_sel)) {
                options_sel = [...this.options_sel];
            } else if (typeof this.options_sel === 'object') {
                options_sel = Object.entries(this.options_sel).map(([key, value]) => [key, value]);
            } else {
                options_sel = [];
            }
            return options_sel;
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

        // readingTime() {
        //     return this.field.value || 1;
        // },
    },

    watch:{
        entry(newVal) {
            this.ffo = { ...newVal };
        },
        ffo: {
            handler(newVal) {
                if (newVal && Object.keys(newVal).length) {
                    this.localFfo = JSON.parse(JSON.stringify(newVal));
                }
            },
            immediate: true,
        },

        'field.value': {
            immediate: true,
            handler(newId) {
                if (newId) {
                    this.fetchImage(newId,this.tablex);
                }
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
        CleanTable,
        handleModalClose(fieldName) {
    console.log('Modal close requested, but checking if we should close...');

    // Nur schließen wenn wirklich gewünscht, nicht nach Upload
    // Sie können hier eine Bedingung hinzufügen
    this.modals[fieldName] = false;
  },
        handleRefreshPreview() {
    this.getPreviewImagez(); // Ihre existierende Methode
    // Zusätzlich: Event an die Gallery Komponente weiterleiten falls nötig
  },
        async getPreviewImagez() {
            const field = this.field;
            const ppa = `/images/_${window.subdomain}/images/${field.name}/${field.value}/index.json`;

            if(ppa.includes("undefined/")){
                this.previewHtml = '<img src="/images/icons/upl.jpg" alt="Jetzt Bild Hochladen" width="200" title="Jetzt Bild Hochladen" style="float: left; margin-right: 12px;">';
                return;
            }

            try {
                const response = await axios.get(ppa);
                this.images = response.data;

                if(!this.validJson(this.images)) {
                    this.previewHtml = '<img src="/images/icons/upl.jpg" alt="Jetzt Bild Hochladen" width="200" title="Jetzt Bild Hochladen" style="float: left; margin-right: 12px;">';
                    return;
                }

                let conta = '';
                for (let i = 0; i < Math.min(5, this.images.length); i++) {
                    const filename = this.images[i]['filename'];
                    if(CleanTable() != "users")
                    {
                        this.thumb = "thumbs/";

                    }
                    else{
                        this.thumb ='';
                    }
                    const src = `/images/_${window.subdomain}/images/${field.name}/${field.value}/${this.thumb}/${this.cc(filename)}`;
                    conta += `<img width="100" class='mt-3' alt="Vorschau33" title="Vorschau33" id="comm_${field.name}"
                                style="float:left;margin-right:12px;" src="${src}" />`;
                }

                this.previewHtml = conta;
            } catch (err) {
                console.error('Fehler beim Laden der Vorschau:', err);
                this.previewHtml = '<p style="color:red;">Fehler beim Laden der Vorschau</p>';
            }
        },

        handleImageUploaded(fieldName, fileName) {
            // Aktualisieren Sie den Feldwert
            this.localFfo.original[fieldName].value = fileName;

            // Vorschau aktualisieren (Vue 3 way - kein $set mehr nötig)
            if(CleanTable() != "users")
            {
                this.thumb = "thumbs/";

            }
            else{
                this.thumb ='';
            }
            this.previewImages[fieldName] = `/images/_${this.subdomain}/${this.CleanTable_alt()}/${fieldName}/${this.thumb}${fileName}`;

            // Schließen Sie das Modal
            this.modals[fieldName] = false;
        },
        remom(data) {
            if (!data || typeof data !== 'object') return {};

            const result = {};

            // Nur die originalen Felder durchgehen
            if (data.original && typeof data.original === 'object') {
                Object.entries(data.original).forEach(([key, field]) => {
                    if (field && typeof field === 'object') {
                        result[field.name] = field.value || "";
                        const kk = key;
                        this.kk = kk;
                    }
                });
            }

            return result;
        },

        getPreviewSrc(field) {
            if (this.previewImages[field.name]) {
                return this.previewImages[field.name];
            }

            if(CleanTable() != "users")
            {
                this.thumb = "thumbs/";

            }
            else{
                this.thumb ='';
            }

            if (field.value && field.value !== '008.jpg') {
                return `/images/_${this.subdomain}/${this.CleanTable_alt()}/${field.name}/${this.thumb}${field.value}`;
            }

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

        CleanTable_alt() {
            return CleanTable();
        },

        handleValidationFailed(index, isRequired) {
            if (isRequired) {
                this.fieldErrors[index] = true;

            }
        },

        handleValidationPassed(index) {
            this.fieldErrors[index] = true;

        },

        sanitizeContent(content) {
            if (content.includes('location') || content.includes('ancestorOrigins')) {
                return '';
            }
            return content;
        },

        async getSlug() {
            try {
                const response = await axios.get(`/api/getSlug/${this.xtable}/${this.xid}`);
                return response.data.autoslug || "";
            } catch (error) {
                console.error("No Slug Found");
                return "";
            }
        },

        async getOF() {
        this.xid = CleanId();
        this.xtable = CleanTable();

        try {
            // API call
            const response = await axios.get(`/api/images/${this.xtable}/${this.xid}`);
            let apiValue = response.data;

            // DOM-Wert prüfen
            const domEl = document.getElementById(this.column);
            let domValue = domEl ? domEl.value : null;

            // Default-Wert aus ffo
            let defaultValue = this.ffo?.[this.column]?.['value'] ?? '';

            // Entscheidung: Priorität DOM > API > Default
            if (domValue && domValue !== "008.jpg") {
            this.nf = domValue;
            } else if (apiValue && apiValue !== "[]" && apiValue !== null) {
            this.nf = apiValue;
            } else {
            this.nf = defaultValue;
            }

            return this.nf;
        } catch (error) {
            console.error("Not fetchable", error);
            return this.ffo?.[this.column]?.['value'] ?? '';
        }
        },


        async fetchImage(id,table) {
            try {
                if(!id) id = this.imageId;
                if(!id) return;

                const response = await axios.get(`/api/images/${table}/${this.xid}`);
                if (response.data.url) {
                    this.ulimage = response.data.url;
                } else {
                    console.warn("Keine URL gefunden für ID:", id);
                }
            } catch (error) {
                console.error("Fehler beim Laden des Bildes:", error);
            }
        },

        handleInput(event) {
            this.readingTime = event.target.value;
            this.updateFormData();
        },

        updateReadingTime() {
            if (!Array.isArray(this.ffo) || this.ffo.length === 0) return;

            const textareaField = this.ffo.find(field => ['textarea'].includes(field.type));
            if (textareaField) {
                this.readingTime = this.calculateReadingTime(textareaField.value);
            }
        },

        calculateReadingTime(text) {
            if (!text) return 0;
            const wordsPerMinute = 190;
            const words = text.trim().split(/\s+/).length;
            return Math.round(words / wordsPerMinute,1);
        },

        removeNumericKeys(obj) {
            let newObj = {};
            Object.values(obj).forEach(value => {
                Object.assign(newObj, value);
            });
            return newObj;
        },

        isRequired(value) {
            return value === "required" || value === true;
        },

        updateFormData(value, fieldName) {
            let formDatas = {};
            if (fieldName.includes("_id") || this.fieldtype == "select"){
                this.formDatas[fieldName] = value;
            }
            if(fieldName.includes("_iid")) {
                this.formData[fieldName] = value;
            }
            if(fieldName == "img_x" || fieldName == "img_y") {
                this.formData[fieldName] = this.value;
            }
            if(this.fieldtype == "autoslug") {
                this.formData[fieldName] = value;
            }
            if(this.fieldname == this.column) {
                this.formData[this.column] = value;
            }
        },

        stripslashes(input) {
            if (typeof input === 'string') {
                return input.replace(/\\(.)/g, '$1');
            } else if (Array.isArray(input)) {
                return input.map(item => this.stripslashes(item));
            } else if (typeof input === 'object' && input !== null) {
                let result = {};
                for (let key in input) {
                    if (input.hasOwnProperty(key)) {
                        result[key] = this.stripslashes(input[key]);
                    }
                }
                return result;
            }
            return input;
        },

        setFormField(field,name) {
            if(field.name == "reading_time") {
                this.formData['reading_time'] = this.readingTime;
            }
            if(field.type == "IID") {
                this.formData[field.name] = this.ref2;
            }
            if(field.type === "select_id") {
                this.getsel(field.name);
            }
            if(field.type === "select") {
                this.getsel_enum(field.name,this.tablex);
            }

            if (field?.name?.includes("_id")) {
                this.formData[field.name] = this.formDatas[field.name];
            }
            else if(field.type === "IID") {
                this.formData[field.name] = this.formDatas[field.name];
            }
            else if(field.type === "select") {
                this.formData[field.name] = this.formDatas[field.name];
            }
            else if (field.name == "img_x" || field.name == "img_y" || field.type == "autoslug") {
                this.formData[field.name] = field.value;
            }
            else if (field && field.name) {
                if (!this.formData) this.formData = {};
                this.formData[field.name] = field.value || "";
            }
        },

        debugUpdateTableData() {
            this.updateTableData();
        },

        confirmTableDeletion() {
            this.confirmingTableDeletion = true;
        },

        cleanArray(array) {
            return array.filter(
                (value) =>
                    (typeof value === "string" && value.trim().length > 0) ||
                    typeof value === "number",
            );
        },

        getsel(name) {
            var sortedOptions = this.sortedOptions ?? [];
            let sdata = this.sdata ?? {};
            axios
                .get(routes.getselroute(name))
                .then((response) => {
                    if (!Array.isArray(this.sortedOptions)) {
                        this.sortedOptions = [];
                    }

                    let input = response.data;
                    const output = [];
                    if (typeof input === 'object' && !Array.isArray(input)) {
                        input = Object.entries(input).map(([key, value]) => value);
                    }

                    input.sort((a, b) => Number(a.position) - Number(b.position));
                    this.options2 = this.options2 ?? [];
                    this.options2 = input;

                    Object.entries(input).forEach(([key, value]) => {
                        const cleanedKey = key.replace('.sortedOptions', '');
                        if (typeof cleanedKey === 'string' && cleanedKey.includes('.')) {
                            const parts = cleanedKey.split('.');
                            if (parts.length === 2) {
                                const [prefix, suffix] = parts;
                                const newKey = `${suffix}.${prefix}`;
                                output.push({ id: newKey, name: value });
                            } else {
                                output.push({ id: cleanedKey, name: value });
                            }
                        } else {
                            output.push({ id: cleanedKey, name: value });
                        }
                    });

                    output.sort((a, b) => Number(a.id) - Number(b.id));
                    const sortedObj = output.map(item => ({
                        [item.id]: item.name
                    }));

                    let obj = JSON.stringify(input, null, 2);
                    obj = obj
                        .replace(/"formFields": \[.*\]/, "")
                        .replace(/^\{|\}$/g, "")
                        .replace(/}\s*,\s*\n\s*}/g, "},")
                        .replace(/[[\]]/g, "")
                        .replace(/\[|\]$/, "")
                        .replace(/,\s*\n\s*{/g, ",")
                        .replace(/\s*\}(?!,)\s*/g, "}")
                        .replace(/}},/g, "\n   },")
                        .replace(/}}/g, "\n   }\n  }")
                        .replace(/"\d+"\s*:\s*{/g, '{')
                        .replace(/},\s*{/g, '},')
                        .replace(/},/g, "},{");

                    obj = "[" + obj + "]";
                    obj = obj.replace(/[\u0000-\u001F\u007F]/g, '');
                    input = obj;
                })
                .catch((error) => {
                    console.error("Fehler beim Abrufen der Formulardaten:3", error);
                });
        },

        getsel_enum(name,table,iscope='getselenumroute') {
            var sortedOptions_sel = this.sortedOptions_sel ?? [];
            let sdata_sel = this.sdata_sel ?? {};
            axios
                .get(routes[iscope](table, name))
                .then((response) => {
                    sdata_sel = JSON.stringify(response.data);
                    sdata_sel = JSON.parse(sdata_sel);

                    if (!Array.isArray(this.sortedOptions_sel)) {
                        this.sortedOptions_sel = [];
                    }

                    const input = (sdata_sel);
                    const output = {};

                    Object.entries(input).forEach(([key, value]) => {
                        const cleanedKey = key.replace('.sortedOptions_sel', '');
                        if (typeof cleanedKey === 'string' && cleanedKey.includes('.')) {
                            const parts = cleanedKey.split('.');
                            if (parts.length === 2) {
                                const [prefix, suffix] = parts;
                                const newKey = `${suffix}.${prefix}`;
                                output[newKey] = value;
                            } else {
                                output[cleanedKey] = value;
                            }
                        } else {
                            output[cleanedKey] = value;
                        }
                    });

                    this.sortedOptions_sel.push(output);
                    this.options_sel = this.sortedOptions_sel;
                })
                .catch((error) => {
                    console.error("Fehler beim Abrufen der Formulardaten2:", error);
                });
        },

        async fetchFormData() {
            axios.get(routes.getform(CleanTable(),CleanId()))
            .then((response) => {
                this.formFields = response.data;
                let formFields_old = response.data;

                this.formFields =  JSON.stringify(response.data,null,2);
                let obj = this.formFields;
                obj = obj.replace(/[\u0000-\u001F\u007F]/g, '');

                try {
                    obj = JSON.parse(obj);
                } catch (error) {
                    console.error("❌ JSON-Fehler:", error.message);
                }

                this.oobj = obj;
                this.obj2 = obj;
                this.ffo = obj;

                const formFieldsArray = Object.values(this.formFields_old ?? {});
                formFieldsArray.forEach((field) => {
                    if (typeof field === "object" && field !== null) {
                        Object.entries(field).forEach(([subKey, subValue]) => {
                            this.setFormField(subValue, subValue?.name ?? '');
                        });
                    }
                });
            })
            .catch((error) => {
                console.error("Fehler beim Abrufen der Formulardaten5:", error);
            });
        },

        isValid(editor) {
            const content = (editor.content ?? editor.modelValue ?? '').trim();
            return content.length > 0;
        },

        async submitForm() {
       const editorRef = this.$refs.editor;

let isValid = true;

if (!editorRef) {
  // kein Ref gefunden — falls das nicht möglich sein soll, dann als invalid behandeln
  console.warn('Kein Editor-Ref gefunden (this.$refs.editor ist undefined).');
  // isValid = false; // optional
} else if (Array.isArray(editorRef)) {
  // mehrere Editor-Refs (z.B. v-for) -> alle prüfen
  isValid = editorRef.every(ref => {
    if (ref && typeof ref.validate === 'function') {
      return ref.validate();
    }
    // Falls ein einzelner ref kein validate hat, gehe davon aus, dass er gültig ist
    // oder setze hier false, wenn das ein Fehler ist:
    console.warn('Ein Editor-Ref hat keine validate()-Methode:', ref);
    return true;
  });
} else if (typeof editorRef.validate === 'function') {
  // Normalfall: einzelne Komponente
  isValid = editorRef.validate();
} else {
  // Ref existiert, aber bietet keine validate()-Methode (z.B. DOM node)
  console.warn('this.$refs.editor hat keine validate()-Methode:', editorRef);
  // Optional: versuche eine DOM-basierte Prüfung:
  const el = (editorRef.$el) ? editorRef.$el : editorRef;
  const text = (el?.innerText || el?.textContent || '').replace(/\s+/g, '').trim();
  isValid = text.length > 0;
}

if (!isValid) {
  console.log("Fehler: Feld ist leer");
  return;
}
  try {
    // Editor-Validierung mit Null-Checks
    const editors = this.$refs.editor;
    if (editors) {
      const editorList = Array.isArray(editors) ? editors : [editors];

      for (const editor of editorList) {
        if (!editor) continue;

        // Sicherer Zugriff auf Editor-Inhalt
        const content = editor.content || editor.modelValue || '';
        const isValid = content.trim().length > 0;

        if (editor.required && !isValid) {
          const el = editor.$el?.querySelector?.('[contenteditable], textarea, input');
          if (el) el.focus();
          return false;
        }
    }
}
            // FormData vorbereiten
            this.formData = this.formData || {};

            // localFfo sicher verwenden
            if (this.localFfo?.original) {
                Object.entries(this.localFfo.original).forEach(([key, field]) => {
                    if (field && typeof field === 'object') {
                        const element = document.getElementById(field.name);
                        const element_alt = document.getElementById(field.name + "_alt");

                        if (element_alt?.value) {
                            this.formData[field.name] = element_alt.value
                                .replace(/\[/g, '%5B')
                                .replace(/\]/g, '%5D').replace(/\n/g,"<br />");
                        } else if (element?.value) {
                            this.formData[field.name] = element.value
                                .replace(/\[/g, '%5B')
                                .replace(/\]/g, '%5D').replace(/\n/g,"<br />");
                        } else if (field.value) {
                            this.formData[field.name] = field.value;
                        }
                    }
                });
            }

            // Formular abschicken
            const path = window.location.pathname;
            const segments = path.split("/");
            console.log("Daten, die gesendet werden:",this.formData);

            if(segments[segments.length - 2] == "create") {
                const response = await axios.post(`/admin/tables/store/${CleanTable()}`, {
                    formData: this.formData
                });
                toastBus.emit('toast', response.data);
            } else {
                const xid = CleanId();
                const tablex = CleanTable();
                const response = await axios.post(`/admin/tables/update/${tablex}/${xid}`, {
                    formData: this.formData,
                });
                toastBus.emit('toast', response.data);
            }

        } catch (error) {
            console.error("Fehler beim Absenden:", error);
        }
    },
    async deleteTable() {
        try {
            // if(!hasRight("delete",this.table))
            // {
            //      alert("Sie haben nicht die benötigten Rechet zum löschen des Datensatzes");
            //      return "";
            // }
            // console.log(`aad: admin/tables/delete/${this.table}/${this.id}`);
            // DELETE-Anfrage mit Parametern in der URL
            const response = await axios.delete(`/admin/tables/delete/${CleanTable()}/${CleanId()}`, {
                params: {
                    edit: "blogposts.index",
                }
            });
            // console.log(response.data);
            toastBus.emit('toast', response.data); // ← erwartet { status: "...", message: "..." }
            this.$inertia.reload();
            // Optional: Seite neu laden oder Liste aktualisieren
        } catch (error) {
            console.error("Fehler beim Löschen:", error);
        }
    },
        deleteTable_old() {
            this.confirmingTableDeletion = false;
            this.loading = true;
            this.loadingText = "Der Beitrag wird gelöscht!";

            this.$inertia.delete(
                this.route("admin.tables.delete", [CleanTable(),this.table.id]),
                {
                    onSuccess: () => {
                        this.loading = false;
                    },
                    onError: () => {
                        this.loading = false;
                    },
                },
            );
        },

        close_confirmingTableDeletion() {
            this.confirmingTableDeletion = false;
        },

        createTableData() {
            this.loading = true;
            this.loadingText = "Der neue {{ItemName}} wird gespeichert!";

            this.$inertia.post(this.route("admin.table.store"), this.form, {
                onSuccess: () => {
                    this.loading = false;
                },
                onError: () => {
                    this.loading = false;
                },
            });
        },

        async updateTableData() {
            try {
                this.loading = true;
                this.loadingText = `Die geänderten Daten des ${this.ItemName} werden jetzt gespeichert!`;
                var tablex = CleanTable();
                var id = CleanId();
                const response = await axios.put(`/admin/tables/update/${tablex}/${id}`,{formData: this.formData});

                if (response.data) {
                    this.loading = false;
                }
            } catch (error) {
                this.loading = false;
                console.error("Fehler beim Speichern der Daten:", error);
            }
        },

        updateData() {
            if (!this.formData) {
                this.formData = {};
            }
            if (typeof this.formFields === "object" && !Array.isArray(this.formFields)) {
                const fieldsArray = Object.values(this.formFields);
                fieldsArray.forEach((field) => {
                    if (!field.name?.includes("_id")) {
                        this.formData[field.name] = field.value || "";
                    }
                });
            }
        },

        selectTableImage(id) {
            this.form.table_image_id = id;
        },

        emptyChecker(){
            if(this.formData.length < 1) {
                alert("empty");
            }
        },

        validJson(data) {
            try {
                JSON.stringify(data);
                return typeof data === 'object' && data !== null;
            } catch (e) {
                return false;
            }
        },
        async fetchEntries() {
        try {
            const response = await axios.get(`/api/headlines/${CleanTable()}`);
            this.entries = response.data;
        } catch (error) {
            console.error("Fehler beim Laden der Einträge:", error);
            this.entries = [];
        }
    },

    },

    beforeUnmount() {
        emitter.off('refresh-preview', () => {
        this.getPreviewImagez();
    });
    },

    async mounted() {

        if(!this.ffo || this.ffo.length < 3 || this.ffo === "undefined"){
            alert(this.ffo);

            this.$inertia.visit('/no-rights');
            return;

        }
    this.ulpath = "/images/_"+ this.subdomain + "/"+this.CleanTable_alt()+ "/";

    // Event Listener korrigiert
    emitter.on('refresh-preview', () => {
        this.getPreviewImagez();
    });

    await this.getPreviewImagez();
    await this.fetchEntries(); // await hinzufügen

    this.settings = await GetSettings();
    this.xid = CleanId();
    this.xtable = CleanTable();
    const path = window.location.pathname;
    this.column =  this.settings.impath?.[this.xtable] ?? this.settings.impath?.['default'];

    await this.fetchFormData();
    this.updateData();

    this.table_older = "/images/"+this.xtable+"/thumbs/";
    if(CleanTable() == "users") {
        this.table_older = "/images/";
        this.table_alter = '';
    }

    this.emptyChecker();
    this.updateReadingTime();

    this.aslug = await this.getSlug();
    if(!this.nf) {
        this.nf = await this.getOF();
    }
    this.table_image = "images";
},
})  ;
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
