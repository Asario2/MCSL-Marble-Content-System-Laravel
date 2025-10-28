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
            :usergroups="usergroups"
            :contacts="contacts"
            @close="showSelectRecipient = false"
            @confirm="handleSelectRecipient"
            @check-news-changed="handleCheckNews"
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
        <InputLabel name="mailbody" class="mt-4"><b>Vorlage wählen</b></InputLabel>
        <InputSelect
            id="mailbodysel"
            v-model="selectedMbId"
            :model-value="selectedMbId"
            :options="mailbodyOptions"
            @input-change="updateMailbody"
        />
            <inputFormText id="subject" v-model="subject">
                <template #label><b>Betreff</b></template>
            </inputFormText>
        <!-- E-Mail Text -->
        <InputHtml
            id="mail_body"
            name="EmailText"
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
            name="EmailSignatur"
            id="signatur"
            rows="8"
            :nosmilies="true"
            v-model="signatureText"
            />
        </div>
        <!-- Buttons -->
        <div class="flex flex-wrap gap-2 mt-4">
        <button
            type="submit"
            @click="submitPreview"
            class="px-3 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium"
        >
            Vorschau
        </button>

        <button
            type="button"
            @click="saveMail"
            class="px-3 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white text-sm font-medium"
        >
            Mail speichern
        </button>

        <button
            type="button"
            @click="saveSignature"
            class="px-3 py-2 rounded-lg bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium"
        >
            Signatur speichern
        </button>
        </div>
    </form>
    </section>


    </Layout>
    </template>

    <script>
    import { rumLaut, nl2br } from "@/helpers";
    import { router } from '@inertiajs/vue3';
    //import { Inertia } from '@inertiajs/inertia'
    import axios from "axios";
    import Layout from "@/Application/Admin/Shared/Layout.vue";
    import SelectRecipient from "./SelectRecipient.vue";
    import InputHtml from "@/Application/Components/Form/InputHtml.vue";
    import InputTextarea from "@/Application/Components/Form/InputTextarea.vue";
    import InputFormText from "@/Application/Components/Form/InputFormText.vue";
    import InputSelect from "@/Application/Components/Form/InputSelect.vue";
    import InputLabel from "@/Application/Components/Form/InputLabel.vue";
    import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
    import Breadcrumb from "@/Application/Components/Content/Breadcrumb.vue";


    export default {
  name: "EmailSender",
  components: { Layout, Breadcrumb, SelectRecipient, InputHtml, InputLabel, InputSelect, InputFormText, MetaHeader, InputTextarea },
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
      recip: '',
      selectedSigId: null,
      signatureText: "",
      signaturOptions: this.sig,
      selectedMbId: null,
      subject: "",
      mailbodyText: "",
      mailbodyOptions: this.mailbody,
      checkNews: false,
      newsletterName: "", // 👈 hier neu

    };
  },
  watch: {
    activeSignatur(newSig) {
      if (newSig) {
        this.signatureText = this.nl2br(this.rumLaut(newSig.sigtext));
      }
    },
    mailbodyText(val) {
      if (val.trim() != "") {
        document.getElementById("EmailText").style.border = "none";
      }
    },
  },
  computed: {
    activeSignatur() {
      return this.signaturOptions.find(sig => sig.id === this.selectedSigId);
    },
  },
  methods: {
    rumLaut,
    nl2br,

    // Vorschau
    submitPreview() {
      const formData = {
        recipients: this.recipientNames,
        mailbodyId: this.selectedMbId,
        mailbodyText: this.mailbodyText,
        signaturId: this.selectedSigId,
        signatureText: this.signatureText,
        subject: this.subject,
      };
      if(formData.recipients.trim() === '')
      {
        alert("Bitte Empfänger angeben");
        return;
      }
      router.post("/email/preview", formData, {
        onSuccess: (page) => console.log("✅ Vorschau geladen", page),
        onError: (errors) => console.error("❌ Fehler:", errors),
      });
    },

    // Mail speichern
    saveMail() {
        if(document.getElementById("subject").value.trim() === ""){
            alert("Bitte Betreff Angeben");
            return;
        }
        if(document.getElementById("EmailText")?.value?.trim() === "" || !this.mailbodyText)
        {
        alert("Bitte Text Angeben");
        return;
        }

       if (!this.newsletterName) {
    const name = prompt("Bitte einen Namen für den Newsletter eingeben:");
    if (!name || name.trim() === "") {
      alert("Newsletter-Name ist erforderlich.");
      return;
    }
    this.newsletterName = name.trim();
  }

  // 🔹 Dann speichern
  const data = {
    name: this.newsletterName, // 👈 wird mitgeschickt
    subject: this.subject,
    Body: this.mailbodyText,
    signatur_id: this.selectedSigId,
  };

  router.post("/email/save", data, {
  onSuccess: () => {
    alert("✅ Mail erfolgreich gespeichert");
  },
  onError: (errors) => {
    // Wenn Laravel eine JSON-Response mit "message" zurückgibt:
    if (errors.response && errors.response.data && errors.response.data.message) {
      alert("❌ Fehler beim Speichern der Mail: " + errors.response.data.message);
    } else if (typeof errors === 'object') {
      alert("❌ Fehler beim Speichern der Mail: " + Object.values(errors));
    } else {
      alert("❌ Fehler beim Speichern der Mail: " + errors);
    }
  },
});
   this.newsletterName = '';
    },

    // Signatur speichern
    async saveSignature() {
        if(document.getElementById("EmailSignatur")?.value?.trim() === '' || !this.selectedSigId)
        {
            alert("Bitte Signatur ausfüllen");
            return;
        }
      let sname = null;

  // Nur fragen, wenn keine bestehende Signatur vorhanden ist
  if (!this.signatur_id) {
    sname = prompt("Wie soll die neue Signatur heißen?");
    if (!sname) return; // Abbrechen, wenn nichts eingegeben wurde
  }
try {
  const res = await axios.post('/email/signatur/save', {
    signatur_id: this.selectedSigId,
    signature_text: this.signatureText,
    signature_name: sname,
  });

  alert(res.data.message);
} catch (err) {
  console.error(err.response?.data || err);
  alert('❌ Fehler beim Speichern der Signatur: ' + (err.response?.data?.message || err.message));
}
    // await axios.post('/email/signatur/save', {


    // });


    },

    // Standard-Submit (kann ggf. entfernt werden)
    submitForm() {
      console.log("Sende Daten:", {
        recipients: this.recipientNames,
        mailbodyText: this.mailbodyText,
        signatureText: this.signatureText,
        subject: this.subject,
      });

      const formData = {
        recipients: this.recipientNames,
        mailbodyId: this.selectedMbId,
        mailbodyText: this.mailbodyText,
        signaturId: this.signaturId,
        signatureText: this.signatureText,
        subject: this.subject,
      };

      router.post("/email/preview", formData);
    },

    updateMailbody(value) {
      const selected = this.mailbodyOptions.find(mb => mb.id === value);
      if (selected) {
        this.mailbodyText = selected.Body || "";
        this.subject = selected.subject || "";
      } else {
        this.mailbodyText = "";
        this.subject = "";
      }
    },

    handleSelectRecipient(data) {
      const allNames = [...data.names];
      if (data.extern) allNames.push(data.extern);
      this.recipientNames = allNames.join(", ");
      this.showSelectRecipient = false;
    },

    handleCheckNews(value) {
      console.log("Newsletter-Checkbox:", value);
      this.checkNews = value;
    },

    updateSigData(value) {
      this.selectedSigId = value;
    },
  },
};

</script>
    <style scoped>
    button { outline: none; }
    </style>
