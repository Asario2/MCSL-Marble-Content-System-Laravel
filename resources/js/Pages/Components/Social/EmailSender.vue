<!-- EmailSender.vue -->
<template>
  <Layout>
    <MetaHeader title="Email Center" />

    <template #header>
      <breadcrumb :breadcrumbs="breadcrumbs"></breadcrumb>
    </template>

    <section
      class="block max-w-sm mx-auto sm:max-w-full p-4 group hover:no-underline focus:no-underline bg-layout-sun-100 dark:bg-layout-night-100"
    >
      <h1 class="text-3xl font-bold mb-6 text-layout-title">Email Center</h1>

      <!-- Label + Button in einer Zeile -->
      <div class="flex items-center justify-between mb-3">
        <label
          for="mail_body"
          class="text-sm font-medium text-layout-sun-900 dark:text-layout-night-900"
        >
          <b>Empfänger/innen</b>
        </label>

        <button
          @click="showSelectRecipient = true"
          class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium"
        >
          Empfänger auswählen
        </button>
      </div>
    <form @submit.prevent="submitForm">
      <!-- Modal -->
      <SelectRecipient
        :show="showSelectRecipient"
        :user="user"
        @close="showSelectRecipient = false"
        @confirm="handleSelectRecipient"

      />

      <!-- Textarea -->
      <InputTextarea
        id="recip"
        name="recip"
        v-model="recipientNames"
        required="required"
        placeholder="Ausgewählte Empfänger..."
        class="w-full border rounded p-2 dark:bg-gray-800 dark:text-gray-100"
      />

      <!-- Mailbody Select -->
      <InputLabel name="mailbody"><b>Vorlage wählen</b></InputLabel>
      <InputSelect
        id="mailbodysel"
        v-model="selectedMbId"
        :model-value="selectedMbId"
        :options="mailbodyOptions"
        @input-change="updateMailbody"
      />

    <!-- E-Mail Text -->
    <InputHtml
        id="mail_body"
        name="Email Text"
        :nosmilies="true"
        v-model="mailbodyText"

    />

      <!-- Signatur Select -->
      <InputLabel name="signatur"><b>E-Mail Signatur</b></InputLabel>
      <InputSelect
        id="signature"
        v-model="selectedSigId"
        :model-value="selectedSigId"
        :options="signaturOptions"
        @input-change="updateSigData"
      />

      <!-- Signaturanzeige -->
      <div v-if="activeSignatur">
        <InputHtml
          name="Email Signatur"
          id="signatur"
          rows="8"
          :nosmilies="true"
          v-model="signatureText"
        />
      </div>
<!-- Buttons -->
    <button type="submit" class="px-3 py-2 mr-2 ml-2 mt-4 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium">Vorschau</button>
    <button class="px-3 py-2 mt-4 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium">Mail Speichern</button>
    <button class="px-3 py-2 ml-2 rounded-lg mt-4 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium">Signatur Speichern</button>
    </form>
    </section>


  </Layout>
</template>

<script>
import { rumLaut, nl2br } from "@/helpers";
// import { router } from '@inertiajs/vue3';
import { Inertia } from '@inertiajs/inertia'

import Layout from "@/Application/Admin/Shared/Layout.vue";
import SelectRecipient from "./SelectRecipient.vue";
import InputHtml from "@/Application/Components/Form/InputHtml.vue";
import InputTextarea from "@/Application/Components/Form/InputTextarea.vue";
import InputSelect from "@/Application/Components/Form/InputSelect.vue";
import InputLabel from "@/Application/Components/Form/InputLabel.vue";
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
import Breadcrumb from "@/Application/Components/Content/Breadcrumb.vue";

export default {
  name: "EmailSender",
  components: {
    Layout,
    Breadcrumb,
    SelectRecipient,
    InputHtml,
    InputLabel,
    InputSelect,
    MetaHeader,
    InputTextarea,
  },
  props: {
    sig: String,
    mailbody: Array,
    breadcrumbs: { type: Object, required: true },
    user: [Array, Object],
    usergroups: [Array, Object],
    contacts: [Array, Object],
  },
  data() {
    return {
      showSelectRecipient: false,
      recipientNames: [],
      selectedSigId: null,
      signatureText: "",
      signaturOptions: this.sig,
      selectedMbId: null,
      mailbodyText: "",       // Inhalt für InputHtml
      mailbodyOptions: this.mailbody, // Mailbody-Auswahl

    };
  },
  watch: {
    activeSignatur(newSig) {
      if (newSig) {
        this.signatureText = this.nl2br(this.rumLaut(newSig.sigtext));
      }
    },
    mailbodyText(val){
        if(val.trim() != ""){
            document.getElementById("Email Text").style.border='none';
        }
    }
  },
  computed: {
    activeSignatur() {
      return this.signaturOptions.find(sig => sig.id === this.selectedSigId);
    },
  },
  methods: {
    rumLaut,
    nl2br,
    submitForm()
    {
        if(!this.mailbodyText){
            document.getElementById("Email Text").style.border = "1.5px solid Red";
            alert("Bitte füllen sie das Textfeld aus.")
            return;
        }
          const formData = {
            recipients: this.recipientNames,
            mailbodyId: this.selectedMbId,
            mailbodyText: this.mailbodyText,
            signaturId: this.signaturId,
            signatureText: this.signatureText,
        };
          Inertia.post('/email/preview/', formData);

        console.log(formData);
    },
    updateMailbody(value) {
      const selected = this.mailbodyOptions.find(mb => mb.id === value);
      if (selected) {
        this.mailbodyText = selected.Body;
      }
    },
//     handleSelectRecipient(selectedNames) {
//     // selectedNames = Array von Strings
//     this.recipientNames = selectedNames.join(', ');
//   },


  handleSelectRecipient(selectedNames) {
    // selectedNames ist jetzt ein Array von Strings
    if (selectedNames && selectedNames.length > 0) {
      this.recipientNames = selectedNames.join(', ');
    } else {
      this.recipientNames = '';
    }
  },
   handleSelectedNames(names) {
    this.selectedUserNames = names;
    console.log("Ausgewählte Benutzer:", names);
  },
  updateSigData(value) {
    this.selectedSigId = value;
  },

    // handleSelectRecipient(event) {
    //   const newNames = event.names;
    //   if (!this.recipientNames) this.recipientNames = "";
    //   newNames.forEach(name => {
    //     if (!this.recipientNames.includes(name)) {
    //       if (this.recipientNames) {
    //         this.recipientNames += ", " + name;
    //       } else {
    //         this.recipientNames = name;
    //       }
    //     }
    //   });
    //   this.showSelectRecipient = false;
    // },
    // updateSigData(value) {
    //   this.selectedSigId = value;
    // },
  },
};
</script>

<style scoped>
button { outline: none; }
</style>
