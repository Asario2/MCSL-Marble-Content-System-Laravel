<!-- <template> -->
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

                <input-subtitle>Daten</input-subtitle>
                <template style="display: inline-block">
                    <div>


                        <!-- <pre>{{localFfo.original }}</pre> -->
                        <a v-if="hasRight('add',tablex)" :href="'/admin/tables/create/' + tablex" target="" class="inline-flex items-center
                        px-1 py-1.5 md:px-2 md:py-2 h-6 md:h-8 rounded-md font-medium text-xs tracking-widest
                         disabled:opacity-25 transition cursor-pointer focus:ring focus:outline-none button_bg
                          button_text_case_bg"><div class="flex items-center whitespace-nowrap"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" class="button_icon"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"></path></svg> Erstelle </div></a>
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
                <input-container v-else-if="field.type ==='imul'">

                <ImageUploadModal
                v-if="modals?.[field.name]"
                :is-modal-open="modals?.[field.name]"
                :column="field.name"
                :isModalOpen="modals[field.name]"
                @close="modals[field.name] = false"
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

                @update:fileName="handleFileNameUpdate"
                @imageUploaded="handleImageUpload"
                @previewUpdated="updatePreviewImage"

                />
                {{ saveValz(field,'1') }}
<button type="button" @click="openModal(field.name)"><b>{{ field.label }}</b><br />
    <p  v-if="(previewImages[field.name] ?? field.value) ">

            <img :src="getPreviewSrc(field)" alt="Vorschau1" title="Vorschau1" :id="'com_'+field.name" style="float:left;margin-right:12px;max-height:120px;max-width:100px;"/></p>
                    <p
        v-else-if="localFfo.original[this.column] && localFfo.original[this.column].value && localFfo.original[this.column].value !== '008.jpg'"
        >
        <!-- Hochgeladenes Bild: -->
        <!-- <img
            :src="`${table_older}${localFfo.original[this.column].value}`"
            width="100"
            alt="Vorschau2"
            title="Vorschau2"
        /> -->
        </p>
    <span v-else><img style="float:left;margin-right: 12px;" :src="'/images/blogs/thumbs/009.jpg'" alt="Jetzt Bild Hochladen" width="100"  title="Jetzt Bild Hochladen" ><span style='display:inline;float:left;'></span></span>

</button>
<!-- <input type="hidden" :name="field.name" :id="field.name" :value="getPreviewSrc(field,'1')" /> -->

<input
type="hidden"
:name="field.name"
:value="nf2?.replace('images//images3/','images/')"
:id="field.name"
/>

</input-container>
<input-container v-else-if="field.type ==='imgal'">

<ImageUploadModal
v-if="modals?.[field.name]"
:is-modal-open="modals?.[field.name]"
:column="field.name"
:is_imgdir="true"
:path="'/images/_mfx/images/imgdir_content/'+field.value+'/'"
:isModalOpen="modals[field.name]"
@close="modals[field.name] = false"
:namee="field.value"
:alt_path="'_' + subdomain + '/' + CleanTable_alt() + '/' + field.name"
:domain="subdomain"
:tablex="table_x"
:ref="field.name"
:value="imageId"
:image="field.value"
:namee2="field.name"
:Message="false"

@update:fileName="handleFileNameUpdate"
@imageUploaded="handleImageUpload"
@previewUpdated="updatePreviewImage"
@refresh-preview="getPreviewImagez" />

<button type="button" @click="openModal(field.name)">
<p  v-if="(previewImages[field.name] ?? field.value) "><span style='float:left;'><b>{{ field.label }}</b><br />
    {{ saveValz(field) }}
    <div v-html="previewHtml"></div></span></p>
    <p
v-else-if="localFfo.original[this.column] && localFfo.original[this.column].value && localFfo.original[this.column].value !== '008.jpg'"
>
<!-- Hochgeladenes Bild: -->
<!-- <img
:src="`${table_older}${localFfo.original[this.column].value}`"
width="100"
alt="Vorschau2"
title="Vorschau2"
/> -->
</p>
<span v-else><img style="float:left;margin-right: 12px;" :src="'/images/blogs/thumbs/009.jpg'" alt="Jetzt Bild Hochladen" width="100"  title="Jetzt Bild Hochladen" ><span style='display:inline;float:left;'>{{ field.label }}</span></span>

</button>
<input type="hidden" :name="field.name" :id="field.name" :value="field.value" />
<!--
<input
type="hidden"
:name="field.name"
:value="nf2?.replace('images//images3/','images/')"
:id="field.name"
/> -->

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
                        v-show="isModalOpen"
                        :tablex="table_x"
                        :column="field.name"
                        :path="tablex"
                        :ref="field.name"
                        :value="imageId"
                        :image="field.value"
                        :namee="field.value"
                        :namee2="field.name"
                        :Message="false"
                        @close="closeModal"
                        @update:fileName="handleFileNameUpdate"
                        @imageUploaded="handleImageUpload"
                />
                {{ saveValz(field,'1') }}
                <button type="button" @click="openModal_alt">
                    <p v-if="this.nf2 && typeof this.nf2 !== 'object' && this.nf2 != '[]' && this.nf2 != '008.jpg' ">Hochgeladenes Bild:

                        <img :src="getPreviewSrc(field)" width="100" alt="Vorschau1" title="Vorschau1"/></p>
                        <p
                        v-else-if="localFfo.original[this.column] && localFfo.original[this.column].value && localFfo.original[this.column].value !== '008.jpg'"
                        >
                        Hochgeladenes Bild:
                        <img
                            :src="`${table_older}${localFfo.original[this.column].value}`"
                            width="100"
                            alt="Vorschau2"
                            title="Vorschau2"
                        />
                        </p>
                    <span v-else><img :src="'/images/blogs/thumbs/009.jpg'" alt="Jetzt Bild Hochladen" width="100"  title="Jetzt Bild Hochladen" ></span>

                </button>
                <input type="hidden" :id="field.name" :value="this.nf2">
                <input
                type="hidden"
                :name="field.name"
                :value="nf2?.replace('images//images3/','images/')"
                :id="field.name"
                />

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
                <input-container
                v-else-if="field.type === 'checkbox'" :full-width="true"
                >
                    <input-checkbox
                        :name="field.name"
                        v-model="field.value"
                        :exValue.Number="field.value"
                        :value="field.value"
                        :disabled="field.disabled"
                        :class="field.disabled ? 'cursor-not-allowed' : ''"
                        >
                        {{field.label}}</input-checkbox
                    >
                </input-container>
                <input-container
                v-else-if="field.type === 'isbox'"
                >
                    <input-isbox
                        :id="field.name"
                        :exValue.Number="field.value"
                        :name="field.name"
                        v-model.number="field.value"


                        >
                        {{field.label}}</input-isbox>

                </input-container>
                <input-container
                                        v-else-if="field.name === 'users_id'"

                                    >
                                        <!-- Weitere Inhalte für die select_id-Komponente -->

                                        <InputLabel
                                        :name="field.name"
                                        :label="field.label">

                                    </InputLabel>
                                    <InputSelectU
                                    @input-change="updateFormData"
                                    :id="field.name"
                                    :model-value="field.value"
                                    :disabled="field.disabled"
                                    :class="field.disabled ? 'cursor-not-allowed' : ''"
                                    :name="field.name"
                                    :xname="field.name"
                                    :required="isRequired(field.required)"
                                    >

                                </InputSelectU>
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
                                    :model-value="field.value"
                                    :disabled="field.disabled"
                                    :class="field.disabled ? 'cursor-not-allowed' : ''"
                                    :name="field.name"
                                    :xname="field.name"
                                    :required="isRequired(field.required)"
                                    >

                                </InputSelect>

                                </input-container>

                                <input-container
                                        v-else-if="field.type === 'select_itemscope'"

                                    >


                                        <InputLabel
                                        :name="field.name"
                                        :label="field.label">
                                    </InputLabel>
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
                                    >

                                </InputSelectEnum>

                                </input-container>
                                <input-container
                                        v-else-if="field.type === 'artselect'"

                                    >
                                <ArtSelect :id="field.id" :table="this.tablex"/>
                                </input-container>
                                <input-container
                                        v-else-if="field.type === 'select'"

                                    >


                                        <InputLabel
                                        :name="field.name"
                                        :label="field.label">
                                    </InputLabel>
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
                                    >

                                </InputSelectEnum>

                                </input-container>
                                </template>
                            </div>


                                <button
                                    type="submit"
                                    class="bg-blue-500 text-white p-2 rounded"
                                >
                                    Absenden
                                </button>
                            </form>
                        </input-group>
                    </div>
                </template>
            </template>
            <template>

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
                            @click.prevent="debugUpdateTableData"
                        >
                            {{ItemName}} ändern
                        </input-button>
                    </smooth-scroll>
                    <smooth-scroll
                        href="#app-layout-start"
                        v-if="table.id == 0"
                    >
                        <input-button
                            type="button"
                            @click.prevent="createTableData"
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
let tablex = CleanTable(); // Muss ein ref sein, wenn es reaktiv sein soll
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
//console.log("RTE " + routes.getselenumroute("images", "status"));

// this.sortOptions = this.sortOptions ?? {}
let sortOptions;
sortOptions = sortOptions ?? {};
// console.log(routes.getselroute("blog_authors"));
import { defineComponent } from "vue";
import { nextTick } from 'vue';
import emitter from "@/eventBus";

import { GetSettings } from "@/helpers";


import axios from "axios";
import $ from "jquery";
import pickBy from "lodash/pickBy";
import { computed } from "vue";
// import { usePage } from '@inertiajs/vue3';
import { ref, watch } from "vue";
// const page2 = usePage();
import { CleanTable, CleanId,cc } from '@/helpers';
import ImageUploadModal from '@/Application/Components/ImageUploadModal.vue';
import InputPosition from '@/Application/Components/InputPosition.vue';
// import { Ziggy } from 'ziggy-js';
// import route from 'ziggy';

import { onMounted, watchEffect } from "vue";
import Layout from "@/Application/Admin/Shared/Layout.vue";
import Breadcrumb from "@/Application/Components/Content/Breadcrumb.vue";

import SmoothScroll from "@/Application/Components/SmoothScroll.vue";

import PageTitle from "@/Application/Components/Content/PageTitle.vue";

import SectionForm from "@/Application/Components/Content/SectionForm.vue";
import SectionBorder from "@/Application/Components/Content/SectionBorder.vue";
import InputPWD from "@/Application/Components/Form/InputPWD.vue";
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
import InputSubtitle from "@/Application/Components/Form/InputSubtitle.vue";
import InputGroup from "@/Application/Components/Form/InputGroup.vue";
import InputContainer from "@/Application/Components/Form/InputContainer.vue";
import InputLabel from "@/Application/Components/Form/InputLabel.vue";
import InputElement from "@/Application/Components/Form/InputElement.vue";
import InputCheckbox from "@/Application/Components/Form/InputCheckbox.vue";
import InputIsbox from "@/Application/Components/Form/InputIsBox.vue";
import InputSelect from "@/Application/Components/Form/InputSelect.vue";
import InputSelectU from "@/Application/Components/Form/InputSelectU.vue";
import InputSelectEnum from "@/Application/Components/Form/InputSelectEnum.vue";
import InputFormPrice from "@/Application/Components/Form/InputFormPrice.vue";
import InputFormSelect from "@/Application/Components/Form/InputFormSelect.vue";
import InputTextarea from "@/Application/Components/Form/InputTextarea.vue";
import Editor from "@/Application/Components/Form/InputHtml.vue";
import InputError from "@/Application/Components/Form/InputError.vue";
import { throttle } from "lodash";
import DialogModal from "@/Application/Components/DialogModal.vue";
import { hasRight,loadAllRights,isRightsReady } from '@/utils/rights';
import { toastBus } from '@/utils/toastBus';
import { reactive } from "vue";
import PublicRadio from "@/Application/Components/Form/PublicRadio.vue";
import Alert from "@/Application/Components/Content/Alert.vue";
// import { console } from "inspector/promises";
onMounted(() => {
  this.fetchFormData();
});

export default defineComponent({
    name: "Admin_TableForm",

    components: {
        Layout,
        Breadcrumb,
        SmoothScroll,
        PublicRadio,
        InputSelectU,
        toastBus,
        InputFormDateTime,
        InputFormText,
        SectionForm,
        // SectionBorder,
        ButtonGroup,
        InputButton,
        InputIsbox,
        InputDangerButton,
        InputLoading,
        ErrorList,
        InputSubtitle,
        InputContainer,
        InputGroup,
         InputLabel,
         InputElement,
        InputCheckbox,
         InputSelect,
         InputSelectEnum,
         InputTextarea,
         InputPWD,
         Editor,
        InputFormTextArea,
        InputFormDate,
        InputFormPrice,
        InputFormSelect,
        InputError,
        DialogModal,
         Alert,
         ArtSelect,
         ImageUploadModal,
         InputPosition,
    },

    props: {
        isModalOpen: {
            type: Boolean,
            default: false,
        },
        newField:{
            type: String,
        },
        id: {
            type: [String, Number],
            required: true,
        },
        ffo: {
           type: [String, Array, Object, Number],
            default: () => ({}),
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
    type: [Object,Array],

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
        id:{
            type: [String,Number],
            default: '1',
        },
        field: Object,
        users_id:{
            type: [Number,String],
            default:0,
        },
        tablex: {
            type: String,
            default: '', // oder: () => 'comments'
            },
            formData:{
                type: [Array,Object],
                default: {},
            },

    },
    emit: ['update:isOpen', 'close'],

    // DZ
    data() {
        return {
            isModalOpen: false,
            ulpath: '',
            previewHtml: '',
            table: reactive({ id: "1" }),// Standardwert setzen, falls leer
        formDatas: {},
        oobj:{},
        formData: {},
        localFfo: JSON.parse(JSON.stringify(this.ffo)),
        sanitizedContent: '',
        uploadedIid: null,
        ItemName: "Beitrag",
        table_x: '',
        aslug: '',
        nf2:'',
        previewImages: {},
        newPosition: null,
        subdomain: window.subdomain || '',

        fieldtype: "", // Oder ein sinnvoller Standardwert

        // nf: this.getOF(),
        readingTime: "",
        fileName: '' ,
        sortedOptions: "",
            so: [],
            xsor_alt: {},

        isOpen: true,
        uploadedImageUrl: null,
        csrfToken: document.getElementById('token').value,
        preview_image: {},
            // ffo: {},
            ulimage: null,
            ffo: { ...this.entry },


            options:    {},
            options_sel: {},
            sdata: {},
            loading: false,
            entries: [], // deine geladenen Daten
            loadingText: null,
            //
            confirmingTableDeletion: false,
            //
            table_alter:'',
            modals: {},
            field:{},
        };
    },
    computed: {
        is_created(){


    const path = window.location.pathname;
    return path.includes("create");
  },
           nf2(){

            console.log("NF:" + this.nf2);
            if(CleanTable == "pictures")
            {
                this.nf2 = "/images/" + this.nf2;
            }
        },

        xsor_alt(){
            try {
          const res = axios.get(`/tables/sort-data/${field.name}`);
          const key = `${field.name}.sortedOptions`;
                alert("yes");
        //   console.log(`[${field.name}] API-Rohdaten:`, res.data);

          if (Array.isArray(res.data)) {
            this.fetchedOptions = res.data;
          } else if (res.data && Array.isArray(res.data[key])) {
            this.fetchedOptions = res.data[key];
          } else if (res.data && typeof res.data[key] === "object") {
            this.fetchedOptions = res.data[key]; // wird später konvertiert
          } else {
            console.error(`[${field.name}] Keine passenden Daten im Response:`, res.data);
          }
        } catch (error) {
          console.error(`[${field.name}] Fehler beim Abrufen der Daten:`, error);
        }
        return this.fetchedOptions;
        },


                sortedOptions() {


                },

    isRightsReady() {
      return this.$isRightsReady; // Zugriff auf globale Methode
    },
    hasRight() {
      return this.$hasRight; // Zugriff auf globale Methode
    },
    sortedOptions_sel() {
        let options_sel;
        if (Array.isArray(this.options_sel)) {
            options_sel = [...this.options_sel];  // Kopiere das Array
        } else if (typeof this.options_sel === 'object') {
            options_sel = Object.entries(this.options_sel).map(([key, value]) => [key, value]);
        } else {
            options_sel = [];
        }
        //console.log('sortedOptions_sel:',options_sel); // Überprüfe den Inhalt von sortedOptions
        return options_sel;
    },
    fieldValue() {
        // return this.field.value || "";
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
        // formData() {
        //     return this.$page.props.formData;
        // },
        dynamicFormData() {
            return this.formFields.reduce((acc, field) => {
                if (!field.name.includes("_idxx") && !field.name.includes("_iid"))
                {
                    acc[field.name] = this.formData[field.name] || field.value;
                }

                return acc;
            }, {});
        },
        readingTime() {
            return this.field.value || 1; // Falls field.value leer ist, dann 0 setzen
        },
    },
            watch:{
                entry(newVal) {
        this.ffo = { ...newVal }; // falls props async oder bei reload
        },
        ffo: {
            handler(newVal) {
            if (newVal && Object.keys(newVal).length) {
                this.localFfo = JSON.parse(JSON.stringify(newVal));
            }
            },
            immediate: true,
        },

               // ffo: {
                //     deep: true,
                //     handler(newFields) {
                //         const textareaField = Object.values(newFields).find(field =>
                //             ["textarea"].includes(field.type)
                //         );
                //         this.textareaField = textareaField;
                //         if (textareaField &&  document.getElementById("reading_time")) {

                //             this.readingTime = this.calculateReadingTime(textareaField.value);
                //             this.readingTime = this.readingTime  < 1 ? "1" : this.readingTime;
                //         // console.log("RT:"+this.readingTime);
                //         }
                //         let fod = {};

                //     }

                // },
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
            deep: true, // Falls field ein Objekt ist
            immediate: true // Setzt den Wert direkt nach dem Mounten

                // handler(newField) {
                //     if (Array.isArray(newField) && newField.length > 0) {
                //         this.fieldValue = newField[0]?.value || "";
                //     }
                // },
                // immediate: true, // Führt den Watcher direkt beim Mounten aus

            },



                fieldValue(newValue) {
            this.readingTime = Math.ceil(newValue.trim().split(/\s+/).length / 190);

            if(this.readingTime == "0")
            {
                this.readingTime = 1;
            }
            //console.log("Berechnete Lesezeit:", this.readingTime);
            },
                updateData() {
                    if (Object.keys(this.formData).length < 1) {
                        this.formData = {};
                    }
                    if (
                        typeof this.formFields === "object" &&
                        !Array.isArray(this.formFields)
                    ) {
                        const fieldsArray = Object.values(this.formFields);
                        fieldsArray.forEach((field) => {
                            if (!field.name.includes("_id")){
                                this.formData[field.name] = field.value || "";
                            }
                        });
                    }
                },
                formFields(newVal) {
                    console.log("formFields geändert:", newVal);
                },
                form: {
                    handler: throttle(function () {
                        let query = pickBy(this.form);
                        let paramName = "table";
                        // const path = window.location.pathname; // Gibt "/admin/tables/show/Example" zurück
                        // const segments = path.split("/"); // Teilt den Pfad in Segmente auf
                        // let lastSegment = segments[segments.length - 1];
                        lastSegment = CleanTable();
                        let paramValue = lastSegment;


                        //   let paramValue = document.getElementById("tb_alt").value;

                        // Dynamische Parameter für die Route
                        if (this.routeParamName && this.routeParamValue) {
                            paramName = this.routeParamName; // z.B. 'table_alt'
                            paramValue = this.routeParamValue; // Der Wert, z.B. der Tabellenname
                        }

                        // Wenn ein Filter für die Suche existiert
                        if (this.searchFilter) {
                            // Wenn der dynamische Parameter existiert, Route mit dem Parameter aufrufen
                            if (paramName && paramValue) {
                                this.$inertia.get(
                                    this.route(this.routeIndex, {
                                        [paramName]: paramValue,
                                    }), // Ziggy erstellt die Route
                                    {
                                        search: this.form.search,
                                        [paramName]: paramValue, // Dynamischer Parameter
                                        page: 1,
                                    },
                                    {
                                        preserveState: true,
                                        replace: true,
                                    },
                                );
                            }
                            // Wenn kein dynamischer Parameter existiert, normale Route ohne Parameter
                            if (!paramName || !paramValue) {
                                this.$inertia.get(
                                    this.route(
                                        this.routeIndex,
                                        Object.keys(query).length
                                            ? query
                                            : { remember: "forget" }, // Query-Parameter für Filter
                                    ),
                                    {
                                        search: this.form.search,
                                        page: 1,
                                    },
                                    {
                                        preserveState: true,
                                        replace: true,
                                    },
                                );
                            }
                        }
                    }, 150),
                    deep: true,
                },
                modelValue(newValue) {
                this.selectedValue = newValue;
                this.xval = newValue;
            }
            },
            defineProps() {
                ffo: [String, Array, Object, Number];
            },
            setup() {
                // const ffo = ref(localStorage.getItem("ffo") || "Standardwert");


        var $_GET = {};
        if(document.location.toString().indexOf('?') !== -1) {
            var query = document.location
                        .toString()
                        // get the query string
                        .replace(/^.*?\?/, '')
                        // and remove any existing hash string (thanks, @vrijdenker)
                        .replace(/#.*$/, '')
                        .split('&');

            for(var i=0, l=query.length; i<l; i++) {
            var aux = decodeURIComponent(query[i]).split('=');
            $_GET[aux[0]] = aux[1];
            }
        }
        const path = window.location.pathname; // Gibt "/admin/tables/show/Example" zurück
        const segments = path.split("/"); // Teilt den Pfad in Segmente auf
        let seg = segments[segments.length - 2];
        if(!$_GET['rl'] && seg != "create")
            {
                //location.href = location.href + "?rl=2";
            //    $inertia.reload();
            //    this.isLoading = false;

            }
            if($_GET['rl']  == "2")
            {
                let url = location.href.replace(/\?rl=2/g, '');
                history.pushState(null, "", url);
            }



        // return { ffo };


        //         return { ffor };

                const formFields = reactive({
                    idField: {
                        name: "id",
                        type: "text",
                        label: "ID",
                        value: "2",
                        id: "2",
                        class: "disabled",
                        rows: "8",
                    },
                    nameField: {
                        name: "name",
                        type: "text",
                        label: "Name",
                        value: "Devlog",
                        id: "2",
                        class: "text",
                        rows: "8",
                    },
                    summaryField: {
                        name: "summary",
                        type: "textarea_short",
                        label: "Zusammenfassung",
                        value: "short description of this one",
                        id: "2",
                        class: "textarea_short",
                        rows: "4",
                    },
                    is_created_atField: {
                        name: "is_created_at",
                        type: "datetime",
                        label: "Erstellt am:",
                        value: "2025-01-22 09:02:27",
                        id: "2",
                        class: "datetime",
                        rows: "8",
                    },
                });
               // this.formData = reactive({});

        //         // Instead of using $set, just update directly
        //         // formData.field1 = 'newvalue';

                onMounted(() => {
                    //   Object.keys(formFields).forEach((key) => {
                    //     formData[formFields[key].name] = formFields[key].value;
                    //   });
                });

                // return {
                //     formFields,
                //     // formData
                // };
            },
            methods: {
                async updatePosition(newPosition) {
                    console.log("Neue Position:", newPosition);

                    await axios.post("/api/entries/update-position" + CleanTable(), {
                        id: this.entry.id,
                        position: newPosition
                    });

                    // optional: lokal gleich updaten
                    this.entry.position = newPosition;

                    // oder komplett reloaden:
                    // this.fetchEntries();
                    },
                    async fetchEntries() {
                        const { data } = await axios.get("/api/headlines/" + CleanTable());
                    this.entries = data;
                    },



                getPreviewSrc(field,num='') {
                    const blobPrefix = "blob:"+window.ahost;
                    const blobUrl = this.previewImages[field.name];


                    if (blobUrl && blobUrl.startsWith(blobPrefix)) {
                        // Lokale Blob-Vorschau
                        const url = this.ulpath + blobUrl.replace(blobPrefix, field.name) + field.name + "/" + field.value;
                        //  document.getElementById('com_' + field.name).src = ulpath + field.name + "/thumbs/" + document.getElementById(field.name).value;
                        const separator = url.includes('?') ? '&' : '?';
                        //return url + separator + 'time=' + Math.floor(Date.now() / 1000);
                    }
                    // Fallback auf gespeicherten Pfad
                    // alert(this.ulpath);

                    //
                    if(!num){
                        return this.ulpath + field.name + "/thumbs/" + field.value;
                    }
                    if(this.field2.value.includes("images/"))
                    {

                        return this.field2.value;
                    }
                    else
                    {
                        return this.ulpath + field.name + "/sdasss" + 'thumbs/' + field.value;
                    }


                },
                cc,
                updatePreviewImage({ column, url }) {
                    this.previewImages[column] = url;

                },
                saveValz(field,num=''){
                    if(!num){
                        this.field = field;
                    }
                    else{
                        this.field2 = field;
                    }

                },
                async getPreviewImagez() {
                    const field = this.field;
                const ppa = `/images/_${window.subdomain}/images/${field.name}/${field.value}/index.json`;
                    if(ppa.includes("undefined/")){
                        return '<img src="/images/blogs/thumbs/009.jpg" alt="Jetzt Bild Hochladen" width="100" title="Jetzt Bild Hochladen" style="float: left; margin-right: 12px;">';
                    }

                try {
                    const response = await axios.get(ppa);
                    this.images = response.data;
                    console.log("TI: " + this.images);
                    if(!this.validJson(this.images))
                    {
                        this.previewHtml = '<img src="/images/blogs/thumbs/009.jpg" alt="Jetzt Bild Hochladen" width="100" title="Jetzt Bild Hochladen" style="float: left; margin-right: 12px;">';
                        return "";
                    }
                    let conta = '';
                    for (let i = 0; i < Math.min(5, this.images.length); i++) {

                        const filename = this.images[i]['filename'];
                        const src = `/images/_${window.subdomain}/images/${field.name}/${field.value}/thumbs/${cc(filename)}`;
                        conta += `<img width="100" class='mt-3' alt="Vorschau33" title="Vorschau33" id="comm_${field.name}"
                                    style="float:left;margin-right:12px;" src="${src}" />`;
                    }

                    this.previewHtml = conta; // 👉 speichern


                } catch (err) {
                    console.error('Fehler beim  Laden der Vorschau:', err);
                    this.previewHtml = '<p style="color:red;">Fehler beim Laden der Vorschau</p>';
                }

            },
                openModal(name) {
                    this.modals[name] = true;
                },
                closeModal(name) {
                    this.modals[name] = false;
                },


                CleanTable_alt() {
                    return CleanTable();
                    },
                openModal_alt() {
            // console.log("Vor dem Setzen: " + this.isModalOpen);

            this.isModalOpen = !this.isModalOpen;
            if(this.Message)
            {


            this.$nextTick(() => {
            let editor = this.$refs.editor;

            // Falls es ein Array ist (z. B. bei dynamischen Komponenten)
            if (Array.isArray(editor)) {
                editor = editor[0];
            }

            if (!editor || !(editor instanceof HTMLElement)) {
                // console.log("Editor nicht gefunden oder kein HTML-Element:", editor);
                return;
            }

            editor.focus();

            const selection = window.getSelection();
            const range = document.createRange();

            // Cursor ans Ende des Inhalts setzen
            range.selectNodeContents(editor);
            range.collapse(false);

            selection.removeAllRanges();
            selection.addRange(range);
            });
            }
            // console.log("Nach dem Setzen: " + this.isModalOpen);
        },




        handleValidationFailed(index, isRequired) {
            if (isRequired) {
            this.$set(this.fieldErrors, index, true);
            }
        },
        handleValidationPassed(index) {
            this.$set(this.fieldErrors, index, false);
        },
            sanitizeContent(content) {
                if (content.includes('location') || content.includes('ancestorOrigins')) {
                    return ''; // Oder einen Standardwert
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
                const path = window.location.pathname; // Gibt "/admin/tables/show/Example" zurück
                    this.xid = CleanId();
                    this.xtable = CleanTable();
            try {
                const response = await axios.get(`/api/images/${this.xtable}/${this.xid}`);
                // alert(this.xid);

                this.nf = response.data;
            //  console.log(`/api/images/${this.xtable}/${this.xid}`);
                if(document.getElementById(this.column) && document.getElementById(this.column).value != "008.jpg"){

                    this.nf = document.getElementById(this.column).value;
                }
                else if(this.nf === "[]")
                {
                    this.nf = this.ffo[this.column]['value'];
                }
                else
                {
                    this.nf = this.ffo[this.column]['value'];
                }
                return this.nf;
            } catch (error){
                console.error("Not fetchable");
            }
            },
                async fetchImage(id,table) {
            try {
                if(!id){
                id = this.imageId;
                }
                if(!id){
                    return;
                }
                const response = await axios.get(`/api/images/${table}/${this.xid}`); // Laravel API-Route
                //console.log(response);
                if (response.data.url) {

                this.ulimage = response.data.url;
                } else {
                console.warn("Keine URL gefunden für ID:", id);
                }
            } catch (error) {
                console.error("Fehler beim Laden des Bildes:", error);
            }

        },
            closeModal() {
            this.isModalOpen = false;
            let previewImage = null;
            this.previewImage = null;
            // Schließe das Modal
            },
            handleInput(event) {
            this.readingTime = event.target.value;
            this.updateFormData();
        },
            handleImageUpload(imageUrl) {
                imageUrl = imageUrl.replace("images/blogs/big/",'').replace("images/images/big/",'').replace("undefined",'');
            // console.log("Bild-URL:", imageUrl);
            this.uploadedImageUrl = imageUrl;
            this.nf2 = imageUrl;
            const ima_new = this.nf2;
            //    this.imageId = iid;
            },

        updateReadingTime() {
            if (!Array.isArray(this.ffo) || this.ffo.length === 0) {
                //console.log("FFO ist leer oder kein Array:", this.ffo);
                return;
            }
            if (!this.ffo || this.ffo.length === 0) return;
            // console.log("ft:" + field.type);
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
      return value === "required" || value === true; // Falls Laravel `true` statt `"required"` sendet
    },
        updateFormData(value, fieldName) {
            let formDatas = {};
            if (fieldName.includes("_id") || this.fieldtype == "select"){
            this.formDatas[fieldName] = value;
            }
            if(fieldName.includes("_iid"))
            {
                this.formData[fieldName] = value;
            }
            if(fieldName == "img_x" || fieldName == "img_y")
            {
                this.formData[fieldname] = this.value;
            }
            if(this.fieldtype == "autoslug")
            {
                this.formData[fieldname] = value;
            }
            if(this.fieldname == this.column)
            {
                this.formData[this.column] = value;
            }
            // console.log("aaaaaaaa" + JSON.stringify(this.formDatas,null,2));
        },
        stripslashes(input) {
  if (typeof input === 'string') {
    // Entfernt Backslashes aus dem String
    return input.replace(/\\(.)/g, '$1');
  } else if (Array.isArray(input)) {
    // Falls es ein Array ist, wende die Funktion auf jedes Element an
    return input.map(item => stripslashes(item));
  } else if (typeof input === 'object' && input !== null) {
    // Falls es ein Objekt ist, wende die Funktion rekursiv auf alle Werte an
    let result = {};
    for (let key in input) {
      if (input.hasOwnProperty(key)) {
        result[key] = stripslashes(input[key]);
      }
    }
    return result;
  }
  return input; // Falls es weder ein String noch ein Array noch ein Objekt ist, gebe den Wert unverändert zurück
},
        setFormField(field,name) {

            // Directly setting the value in formData
            // console.log("f:" + JSON.stringify(field,null,2));
            // console.log("f.n:" + field.name);
            // console.log("f.t:" + field.type);
            if(field.name == "reading_time")
            {
                this.formData['reading_time'] = this.readingTime;
            }
            if(field.type == "IID")
            {
                this.formData[field.name] = this.ref2;
            }
            if(field.type === "select_id")
            {

                this.getsel(field.name);

            }
            if(field.type === "select")
            {
                this.getsel_enum(field.name,this.tablex);
               // console.log("sso:" + JSON.stringify(this.options));
            }

            if (field?.name?.includes("_id")) {
                this.formData[field.name] = this.formDatas[field.name];
            }
            else if(field.type === "IID")
            {
                this.formData[field.name] = this.formDatas[field.name];
            }
            else if(field.type === "select")
            {
                this.formData[field.name] = this.formDatas[field.name];
            }
            else if (field.name == "img_x" || field.name == "img_y" || field.type == "autoslug") {
                this.formData[field.name] = field.value;
            }

            else if (field && field.name) {
            if (!this.formData) this.formData = {}; // absichern, falls undefined
              this.formData[field.name] = field.value || "";
            }

        },
        debugUpdateTableData() {
            // console.log("updateTableData wird aufgerufen!");
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
                this.sortedOptions = []; // Leeres Array initialisieren
            }

            let input = response.data;

            const output = [];
            if (typeof input === 'object' && !Array.isArray(input)) {
                input = Object.entries(input).map(([key, value]) => value);
            }

            // Korrigierte Sortierung nach position (numerisch)
            input.sort((a, b) => Number(a.position) - Number(b.position));

            this.options2 = this.options2 ?? [];
            this.options2 = input;

            // Zuerst das Objekt in ein Array umwandeln und die ID/Name-Paare speichern
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
            console.log("Vor Sortierung:", input.map(i => i.position));
            // Korrigierte Sortierung nach name (alphabetisch)
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



        getsel_enum(name,table,iscope='getselenumroute')
        {
            var sortedOptions_sel = this.sortedOptions_sel ?? [];
            let sdata_sel = this.sdata_sel ?? {};
            axios
                .get(routes[iscope](table, name))
                .then((response) => {

                    sdata_sel = JSON.stringify(response.data);
                    // console.log(sdata_sel);
                    //sdata = sdata.replace(new RegExp(name, "g"), '');
                    sdata_sel = JSON.parse(sdata_sel);
                    // 1. JSON-String in Objekt umwandeln
                    // const parsedData = JSON.parse(jsonString);

                    // 2. Nur das ".sortedOptions" Objekt extrahieren
                    // const sortedOptionsData = parsedData[".sortedOptions"];

                    // 3. In `sortedOptions` speichern
                    if (!Array.isArray(this.sortedOptions_sel)) {
                        this.sortedOptions_sel = []; // Leeres Array initialisieren
                    }
                    // this.sortedOptions[name] = sortedOptionsData;

                                       // sortedOptions[name] = sortedOptionsData;


                    // Die Struktur umkehren
                    // Object.entries(this.sortedOptions).forEach(([key, value]) => {
                    //     if (value.sortedOptions) {
                    //         sortedOptions[key] = value.sortedOptions;
                    //     }
                    // });

                    const input = (sdata_sel);
                    const output = {};

                    // Durch alle Einträge iterieren und die Keys umwandeln
                    Object.entries(input).forEach(([key, value]) => {
                        // Entfernen von "sortedOptions" aus dem Key, falls vorhanden
                        const cleanedKey = key.replace('.sortedOptions_sel', '');

                        // Überprüfen, ob der Key ein String ist und ob er einen Punkt enthält
                        if (typeof cleanedKey === 'string' && cleanedKey.includes('.')) {
                            const parts = cleanedKey.split('.'); // Key in zwei Teile aufteilen

                            if (parts.length === 2) {
                                const [prefix, suffix] = parts;
                                const newKey = `${suffix}.${prefix}`; // Reihenfolge umdrehen
                                output[newKey] = value;
                            } else {
                                output[cleanedKey] = value;
                            }
                        } else {
                            // Falls der Key keinen Punkt enthält, oder nicht als String vorliegt, Key unverändert lassen
                            output[cleanedKey] = value;
                        }
                    });
                    // output = (JSON.stringify(output));

                    this.sortedOptions_sel.push(output);
                    this.options_sel = this.sortedOptions_sel;

                })
                .catch((error) => {
                    console.error(
                        "Fehler beim Abrufen der Formulardaten2:",
                        error,
                    );

                });

        },
        getdefnf(){

             if(document.getElementById(this.column) && document.getElementById(this.column) != "008.jpg")
             {
                 this.nf = document.getElementById(this.column).value;
             }



        },
        fetchFormData() {
            console.log("CID:" + CleanId());
        axios.get(routes.getform(CleanTable(),CleanId()))
        .then((response) => {
            this.formFields = response.data;
            let formFields_old = response.data;
            console.log(response.data);
            // JSON bereinigen
            //console.log("Rohdaten:", JSON.stringify(response.data,null,2));
            this.formFields =  JSON.stringify(response.data,null,2);
            let obj = this.formFields;

            //  obj = obj.replace(/"formFields": \[.*\]/, "")
            //     .replace(/^\{|\}$/g, "")
            //     .replace(/}\s*,\s*\n\s*}/g, "},")
            //     .replace(/[[\]]/g, "")
            //     .replace(/\[|\]$/, "")
            //     .replace(/,\s*\n\s*{/g, ",")
            //     .replace(/\s*\}(?!,)\s*/g, "}")
            //     .replace(/}},/g, "\n   },")
            //     .replace(/}}/g, "\n   }\n  }")
            //     .replace(/"\d+"\s*:\s*{/g, '{')
            //     .replace(/},\s*{/g, '},');
            // obj = "[" + obj + "]";
            // console.log("OBJ: " + (obj));
            obj = obj.replace(/[\u0000-\u001F\u007F]/g, '');
            try {
                obj = JSON.parse(obj);

            } catch (error) {
                console.error("❌ JSON-Fehler:", error.message);
            }
            // obj = JSON.parse(obj);
            this.oobj = obj;
            this.obj2 = obj;
            // console.log(obj);
            this.ffo = obj;
            // console.log(JSON.stringify([obj],null,2));
            // this.formData = obj;

            const ffo = this.ffo;
            // 💡 Konvertiere das Objekt in ein Array
            console.log("FFA: " + JSON.parse(JSON.stringify(formFields_old)));
            const formFieldsArray = Object.values(this.formFields_old ?? {});
;

            // console.log("FormFields als Array:", formFieldsArray);

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
quotebrace(obj){
    obj = typeof obj !== String ? JSON.stringify(obj,null,2) : obj;
                obj.replace(/^\{|\}$/g, "")
                .replace("[{|}]")
                .replace(/}\s*,\s*\n\s*}/g, "},")
                .replace(/[[\]]/g, "")
                .replace(/\[|\]$/, "")
                .replace(/,\s*\n\s*{/g, ",")
                .replace(/\s*\}(?!,)\s*/g, "}")
                .replace(/}},/g, "\n   },")
                .replace(/}}/g, "\n   }\n  }");
                return JSON.parse(obj);
},
remom(str){
// Variable, um die Anzahl der Vorkommen von ","
let commaCount = 0;

str = Object.entries((str));
str.forEach(([key, value]) => {

    if (!key.includes("_id")){
    //this.formData[value['name']] = value['value'];
    // console.log("NO ID@" + key);
    // console.log(`${key}: ${value['value']}`);

    }
    else{
        this.formData[key] = this.formDatas[key];
    }
});
var modifiedStr;
// str = this.objectToCSVArray(str);
modifiedStr  = this.formData;
// str.replace(/,/g, (match) => {
//   commaCount++;
//   return commaCount % 2 !== 0 ? ':' : ',';
// });
return modifiedStr;
},
// getData($obje){
//     this.obje.find(obj => obj[field.value])?.[field.value];
//     console.log("obj: " + obje);
// },
objectToCSVArray(obj) {
    let csvArray = [];

    // Überschriften (Header)
    let headers = Object.keys(obj);
    csvArray.push(headers.join(',')); // Als CSV-String speichern

    // Werte extrahieren
    let values = Object.values(obj);
    csvArray.push(values.join(',')); // Als CSV-String speichern

    return csvArray; // Gibt das CSV als Array zurück
},
isValid(editor) {
  // Prüfe content oder modelValue als Textinhalt
  const content = (editor.content ?? editor.modelValue ?? '').trim();
  return content.length > 0;
},

async submitForm() {
// Positions

try{
    await axios.post("/api/entries/update-position/" + CleanTable(), {
        id: this.entry.id,
        position: this.newPosition
      });

      this.entry.position = this.newPosition; // lokal anpassen

}
catch (error) {
            console.error("Fehler beim Positionieren:", error);
        }

    console.log('submitForm wurde aufgerufen');

    try {

            const editors = this.$refs.editor;
            if(editors){


            const editorList = Array.isArray(editors) ? editors : [editors];

            for (const editor of editorList) {

                const content = editor?.content ?? editor?.modelValue ?? '';
                const valid = content?.trim()?.length > 0;
                if (!editor.required) {
                    editor.hasError = false;
                    continue;
                }
                // Reset error state vorher
                if (editor.hasError !== undefined) editor.hasError = false;

                if (!valid) {
                    // Fehlerzustand setzen
                    if (editor.hasError !== undefined) editor.hasError = true;

                    // Fokus auf Editor setzen (z. B. contenteditable oder input innerhalb)
                    const el = editor.$el.querySelector('[contenteditable], textarea, input');
                    if (el) el.focus();

                    return;
                }
            }
        }






            // console.log("ref: " + this.$refs.blog_images_iid.value);
                       this.formData2 = JSON.stringify(this.remom(this.ffo));

            // const path = window.location.pathname; // Gibt "/admin/tables/show/Example" zurück
            // const segments = path.split("/"); // Teilt den Pfad in Segmente auf
            this.xid = CleanId();
            xid = CleanId();
            tablex = CleanTable();
            let response;
            this.formData = this.formData ?? {};
            // var inputRef = this.$refs.this.column;
            if(this.textareaField &&  document.getElementById("reading_time"))
            {
                this.formData['reading_time'] = this.readingTime;

            }

            Object.keys(this.localFfo.original).forEach(name => {

                const element = document.getElementById(name);
                const element_alt = document.getElementById(name + "_alt");

                if (element_alt?.value) {
                    this.formData[name] = element_alt.value
                        .replace(/\[/g, '%5B')
                        .replace(/\]/g, '%5D').replace(/\n/g,"<br />");

                }

                else if (element?.value) {
                    this.formData[name] = element.value
                        .replace(/\[/g, '%5B')
                        .replace(/\]/g, '%5D').replace(/\n/g,"<br />");
                }
                else if(element_alt?.innerHTML)
                {
                    this.formData[name] = element.innerHTML
                        .replace(/\[/g, '%5B')
                        .replace(/\]/g, '%5D').replace(/\n/g,"<br />");

                }
                else if(element?.innerHTML)
                {
                    this.formData[name] = element.innerHTML
                        .replace(/\[/g, '%5B')
                        .replace(/\]/g, '%5D').replace(/\n/g,"<br />");


                }

            });

            if(this.ffo['profile_photo_path'] || this.formData['profile_photo_path'])
                {
                    this.formData["profile_photo_path"] = document.getElementById("profile_photo_path").value;
                }
                if(this.ffo['image_path'] || this.formData['image_path'])
                {
                    this.formData["image_path"] = document.getElementById("image_path").value;
                }
                // console.log("ffo:" + JSON.stringify(this.formData,null,2));
              const path = window.location.pathname; // Gibt "/admin/tables/show/Example" zurück
            const segments = path.split("/"); // Teilt den Pfad in Segmente auf
            console.log("Daten, die gesendet werden:",this.formData);
            if(segments[segments.length - 2] == "create")
            {

                response = await axios.post(`/admin/tables/store/${CleanTable()}`, {
                formData: this.formData


            });
            toastBus.emit('toast', response.data);
            }

            else{

                var xid = CleanId();
                var tablex = CleanTable();
                // const formData = {...this.formData };
                response = await axios.post(`/admin/tables/update/${tablex}/${xid}`, {
                formData : this.formData,
            });
            toastBus.emit('toast', response.data);

        }

        } catch (error) {
            console.error("Fehler beim Absenden:", error);
        }

        // location.href='/admin/tables/show/'+this.tablex;
    },
        deleteTable() {
            this.confirmingTableDeletion = false;
            //
            this.loading = true;
            this.loadingText = "Der Beitrag wird gelöscht!";
            //
            this.$inertia.delete(
                this.route("admin.table.delete", this.table.id),
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
            //
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
            // console.log("Daten erfolgreich gespeichert!", response.data);
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
            if (
                typeof this.formFields === "object" &&
                !Array.isArray(this.formFields)
            ) {
                const fieldsArray = Object.values(this.formFields);
                fieldsArray.forEach((field) => {
                    if (!field.name?.includes("_id")) {
                        this.formData[field.name] = field.value || "";
                    }
                });
            }
        },
        selectTableImage(id) {
            // console.log("selectTableImage id:", id);
            this.form.table_image_id = id;
        },
        emptyChecker(){
            if(this.formData.length < 1)
            {
                alert("empty");
            }
        },
        validJson(data) {
  try {
    JSON.stringify(data); // wird fehlschlagen, wenn z. B. zirkuläre Referenzen vorhanden sind
    return typeof data === 'object' && data !== null;
  } catch (e) {
    return false;
  }
},
            },
    beforeUnmount() {
    // Listener aufräumen
    emitter.off('refresh-preview', this.getPreviewImagez);
  },
    async mounted() {
         //this.fetchDataX();
        //  console.log("Initial ffo:", JSON.stringify(this.ffo, null, 2));
        this.ulpath = "/images/_"+ this.subdomain + "/"+this.CleanTable_alt()+ "/";
        emitter.on('refresh-preview', this.getPreviewImagez);
        this.getPreviewImagez();
        this.fetchEntries();


        for (const [key, value] of Object.entries(this.ffo)) {
            console.log("Field:", key, "→", value);
        }

         this.settings = await GetSettings();
         this.xid = CleanId();
        this.xtable = CleanTable();
        const path = window.location.pathname;
        this.column =  this.settings.impath?.[this.xtable] ?? this.settings.impath?.['default'];
        this.ffo = this.fetchFormData();

        this.updateData();
        console.log("col:" + CleanTable());
        // this.table_alter = "/images/"+this.xtable+"/thumbs/";
        // this.table_alter = CleanTable() == "pictures" ? '/images/images/thumbs/' : "/images/"+CleanTable()+"/thumbs/";
        // this.table_alter = this.settings.impath?.[this.xtable] && tablex === "users" ? '' : "/images/"+this.tablex+"/thumbs/";
        this.table_older = "/images/"+this.xtable+"/thumbs/";
        if(CleanTable() == "users")
        {
            this.table_older = "/images/";
            this.table_alter = '';
        }
        //  this.getImageUrl();



        this.emptyChecker();
        this.updateReadingTime();

        this.aslug = await this.getSlug();
        if(!this.nf)
        {
            this.nf = this.getdefnf();
        }
        this.table_image = "images";
        // this.fetchImage(this.ref3,this.table_image);
        //      console.log("ffo" + JSON.stringify(this.localFfo,null,2));
    //    this.localFfo = this.fetchFormData();

    this.nf2 = this.localFfo.original[this.column]?.value;
    this.getPreviewImagez();
    },
    is_created() {
        this.updateReadingTime();

        // console.log("FIELD:", this.field); // 👀 Prüfen, ob es existiert
    },
});
</script>
<style>
select,datetime-local   {
    max-width:560px !important;
}
.editor a:link,.editor a:visited,.editor a:active,.editor a {
  color: #2563eb !important; /* z. B. Tailwind blue-600 */
  text-decoration: underline;
}
@media (min-width: 1024px) {
    /* .w-full {
        width: 100% !important;
    } */
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
