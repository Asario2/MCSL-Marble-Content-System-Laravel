<!-- EmailSender.vue -->
<template>
  <Layout>
    <MetaHeader title="Email Center" />

    <template #header>
      <Breadcrumb :breadcrumbs="breadcrumbs" />
    </template>

    <section
      class="block max-w-sm mx-auto sm:max-w-full p-4 bg-layout-sun-100 dark:bg-layout-night-100"
    >
      <h1 class="text-3xl font-bold mb-6 text-layout-title">
        Email Center
      </h1>

      <!-- Empfänger -->
      <div class="flex items-center justify-between mb-3">
        <label class="text-sm font-medium">
          <b>Empfänger/innen</b>
        </label>

        <button
          @click="showSelectRecipient = true"
          class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm"
        >
          Empfänger auswählen
        </button>
      </div>

      <form @submit.prevent>
        <SelectRecipient
          :show="showSelectRecipient"
          :user="user"
          :usergroups="usergroups"
          :contacts="contacts"
          @close="showSelectRecipient = false"
          @confirm="handleSelectRecipient"
          @check-news-changed="checkNews = $event"
        />

        <InputTextarea
          v-model="recipientNames"
          placeholder="Ausgewählte Empfänger…"
          class="w-full border rounded p-2"
        />

        <!-- MAILVORLAGE -->
        <InputLabel class="mt-4">
          <b>Vorlage wählen</b>
        </InputLabel>

        <InputSelect
          v-model="selectedMbId"
          :options="mailbodyOptions"
        />

        <!-- BETREFF -->
        <InputFormText v-model="subject">
          <template #label><b>Betreff</b></template>
        </InputFormText>

        <!-- MAILTEXT -->
        <InputHtml
            name="Mailtext"
            v-model="mailbodyText"
            :nosmilies="true"
        />

        <!-- SIGNATUR -->
        <InputLabel>
          <b>E-Mail Signatur</b>
        </InputLabel>

        <InputSelect
          v-model="selectedSigId"
          :options="signaturOptions"
        />

        <InputHtml
          v-if="signatureText"
          v-model="signatureText"
          :nosmilies="true"
          name="Signatur"
        />

        <!-- BUTTONS -->
        <div class="flex gap-2 mt-4">
          <button
            type="button"
            @click="submitPreview"
            class="px-3 py-2 rounded-lg bg-blue-600 text-white"
          >
            Vorschau
          </button>

          <button
            type="button"
            @click="saveMail"
            class="px-3 py-2 rounded-lg bg-green-600 text-white"
          >
            Mail speichern
          </button>

          <button
            type="button"
            @click="saveSignature"
            class="px-3 py-2 rounded-lg bg-purple-600 text-white"
          >
            Signatur speichern
          </button>
        </div>
      </form>
    </section>
  </Layout>
</template>

<script>
import axios from "axios";
import { router } from "@inertiajs/vue3";
import Layout from "@/Application/Admin/Shared/ab/Layout.vue";
import Breadcrumb from "@/Application/Components/Content/Breadcrumb.vue";
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
import SelectRecipient from "./SelectRecipient.vue";
import InputHtml from "@/Application/Components/Form/InputHtml.vue";
import InputTextarea from "@/Application/Components/Form/InputTextarea.vue";
import InputFormText from "@/Application/Components/Form/InputFormText.vue";
import InputSelect from "@/Application/Components/Form/InputSelect.vue";
import InputLabel from "@/Application/Components/Form/InputLabel.vue";

export default {
  name: "EmailSender",

  components: {
    Layout,
    Breadcrumb,
    MetaHeader,
    SelectRecipient,
    InputHtml,
    InputTextarea,
    InputFormText,
    InputSelect,
    InputLabel,
  },

  props: {
    sig: Array,
    mailbody: Array,
    breadcrumbs: Object,
    user: [Array, Object],
    usergroups: [Array, Object],
    contacts: [Array, Object],

  },

  data() {
    return {
      showSelectRecipient: false,
      recipientNames: "",

      selectedMbId: null,
      selectedSigId: null,

      subject: "",
      mailbodyText: "",
      signatureText: "",

      mailbodyOptions: this.mailbody,
      signaturOptions: this.sig,

      newsletterName: "",
      checkNews: false,
    };
  },

  watch: {
    /** 📌 Mailvorlage geändert */
    selectedMbId(id) {
      const mb = this.mailbodyOptions.find(m => m.id === id);

      if (!mb) {
        this.subject = "";
        this.mailbodyText = "";
        this.selectedSigId = null;
        this.signatureText = "";
        return;
      }

      this.subject = mb.subject || "";
      this.mailbodyText = mb.Body || "";

      if (mb.signatur_id) {
        this.selectedSigId = mb.signatur_id;
      }
    },

    /** 📌 Signatur geändert */
    selectedSigId(id) {
      const sig = this.signaturOptions.find(s => s.id === id);
      this.signatureText = sig ? sig.sigtext || "" : "";
    },
  },
  mounted(){
    console.log("th:" + this.comphash);
  },
  methods: {
    submitPreview() {
    router.post("/email/preview", {
        recipients: this.recipientNames,
        subject: this.subject,
        mailbodyText: this.mailbodyText,
        signatureText: this.signatureText,
        mailbodyId: this.selectedMbId,
        signaturId: this.selectedSigId,
        comphash:this.mailbody.comphash,
    });
    },

    saveMail() {
      if (!this.subject || !this.mailbodyText) {
        alert("Betreff und Text sind Pflichtfelder");
        return;
      }

      if (!this.newsletterName) {
        const n = prompt("Newsletter-Name:");
        if (!n) return;
        this.newsletterName = n;
      }

      router.post("/email/save", {
        name: this.newsletterName,
        subject: this.subject,
        Body: this.mailbodyText,
        signatur_id: this.selectedSigId,
      });

      this.newsletterName = "";
    },

    async saveSignature() {
      if (!this.signatureText) {
        alert("Signatur leer");
        return;
      }

      const name = prompt("Name der Signatur:");
      if (!name) return;

      await axios.post("/email/signatur/save", {
        signature_text: this.signatureText,
        signature_name: name,
      });

      alert("Signatur gespeichert");
    },

    handleSelectRecipient(data) {
      const list = [...data.names];
      if (data.extern) list.push(data.extern);
      this.recipientNames = list.join(", ");
      this.showSelectRecipient = false;
    },
  },
};
</script>


        <style>
        button { outline: none; }
        .w-fully{min-width:100% !important;
                max-width:100% !important;}
        </style>

