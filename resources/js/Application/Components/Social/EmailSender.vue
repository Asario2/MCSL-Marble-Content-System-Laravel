    <template>
        <Layout>
        <MetaHeader title="Email Center" />
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
                Empfänger/innen
            </label>

            <button
                @click="showSelectRecipient = true"
                class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium"
            >
                Empfänger auswählen
            </button>
            </div>

            <!-- Modal -->
            <SelectRecipient
            :show="showSelectRecipient"
            @close="showSelectRecipient = false"
            @confirm="handleSelectRecipient"
            />

            <!-- Textarea -->
            <InputTextarea id="recip" name="recip" />

            <!-- E-Mail Text -->
            <InputHtml id="mail_body" name="Email Text" :nosmilies="true"/>

            <!-- Signatur Select -->
            <InputLabel name="signatur" label="E-Mail Signatur" />

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


        </section>
        </Layout>
    </template>

    <script>
    import { rumLaut,nl2br } from "@/helpers";
    import Layout from "@/Application/Admin/Shared/Layout.vue";
    import SelectRecipient from "./SelectRecipient.vue";
    import InputHtml from "@/Application/Components/Form/InputHtml.vue";
    import InputTextarea from "@/Application/Components/Form/InputTextarea.vue";
    import InputSelect from "@/Application/Components/Form/InputSelect.vue";
    import InputLabel from "@/Application/Components/Form/InputLabel.vue";
    import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";

    export default {
        name: "EmailSender",
        components: {
        Layout,
        SelectRecipient,
        InputHtml,
        InputLabel,
        InputSelect,
        MetaHeader,
        InputTextarea,
        },
        props:{
            sig:String,
            users:[Array, Object],
            usergroups:[Array, Object],
            contacts:[Array, Object],
        },
        data() {
        return {
            showSelectRecipient: false,
            selectedSigId: null,
            signatureText: "", // <- hier wird der aktuelle Text gespeichert
            signaturOptions: this.sig,
        };
        },
        watch: {
        // Sobald eine neue Signatur gewählt wird:
        activeSignatur(newSig) {
            if (newSig) {
            this.signatureText = this.nl2br(this.rumLaut(newSig.sigtext));
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
            handleSelectRecipient() {
            alert("Empfänger übernommen!");
            this.showSelectRecipient = false;
        },
        updateSigData(value) {
            this.selectedSigId = value;
        },
        },
    };
    </script>

    <style scoped>
    button {
        outline: none;
    }
    </style>
