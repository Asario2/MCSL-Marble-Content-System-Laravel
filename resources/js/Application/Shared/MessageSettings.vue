<template>
    <section-form @submitted="updateConfig" :withTitle="true">
        <template #title v-if="!nohead"> Nachrichten Einstellungen </template>

        <template #description v-if="!nohead">
            E-Mail, Private Nachrichten und Newsletter Einstellungen<slot></slot>
        </template>

        <template #form>
            <input-group>
            <input-container :full-width="true">
                <label class="text-xl">Private Nachrichten an Email weiterleiten</label>
                        <RadioSet
                        name="xis_pmtoautomail"
                        :options="[
                            { label: 'Ja', value: '1' },
                            { label: 'Nein', value: '0' },

                        ]"
                        v-model="form.xis_pmtoautomail"
                        />
                    <input-error :message="form.errors.xis_pmtoautomail" />

                </input-container>
            <input-container :full-width="true">
                <label class="text-xl">Newsletter</label>
                        <RadioSet
                        name="newsletter"
                        :options="[
                            { label: 'Nein', value: '0' },
                            { label: 'Per Email', value: '      ' },
                            { label: 'Per Privater Nachricht', value: 'to_pm' },
                            { label: 'Per Email & Private Nachrichten', value: 'to_pm_and_mail' },
                        ]"
                        v-model="form.xch_newsletter"
                        />
                    <input-error :message="form.errors.xch_newsletter" />

                </input-container>

                <input-container :full-width="true">
                <label class="text-xl">Anzahl der Zeilen (in Privaten Nachrichten)</label>
                <br />
                <input type="text" name="cnt_numrows" id="cnt_numrows" v-model="form.cnt_numrows" size="4" class="p-2.5 text-sm rounded-lg block border focus:ring-3 focus:ring-opacity-75 bg-layout-sun-0 text-layout-sun-900 border-primary-sun-500 focus:border-primary-sun-500 focus:ring-primary-sun-500 placeholder:text-layout-sun-400 selection:bg-layout-sun-200 selection:text-layout-sun-1000 dark:bg-layout-night-0 dark:text-layout-night-900 dark:border-primary-night-500 dark:focus:border-primary-night-500 dark:focus:ring-primary-night-500 placeholder:dark:text-layout-night-400 dark:selection:bg-layout-night-200 dark:selection:text-layout-night-1000"/>
                    <input-error :message="form.errors.cnt_numrows"  />

                </input-container>
                </input-group>
                </template>
        <template #actions>

                <input-action-message
                    :on="form.recentlySuccessful"
                    class="me-3"
                >
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
import { Link, useForm } from "@inertiajs/vue3";
import SectionForm from "@/Application/Components/Content/SectionForm.vue";
import axios from "axios"
import {route} from 'ziggy-js';
import InputGroup from "@/Application/Components/Form/InputGroup.vue";
import InputContainer from "@/Application/Components/Form/InputContainer.vue";
import InputLabel from "@/Application/Components/Form/InputLabel.vue";
import InputElement from "@/Application/Components/Form/InputElement.vue";
import RadioSet from "@/Application/Components/Form/RadioSet.vue";
import InputError from "@/Application/Components/Form/InputError.vue";
import InputActionMessage from "@/Application/Components/Form/InputActionMessage.vue";

import ButtonGroup from "@/Application/Components/Form/ButtonGroup.vue";
import InputButton from "@/Application/Components/Form/InputButton.vue";

export default {
    name: "Shared_MessageSettings",

    components: {
        Link,
        SectionForm,
        InputGroup,
        InputContainer,
        InputLabel,
        InputElement,
        InputError,
        InputActionMessage,
        ButtonGroup,
        InputButton,
        RadioSet,
    },

    data() {
        return {

            form: useForm({
                xis_pmtoautomail: "0",
                cnt_numrows: "10",
                xch_newsletter: "0",
            }),
        };
    },

    methods: {
        updateConfig()
        {
             this.form.put(route("admin.usconf.save"), {
                errorBag: "updatePassword",
                preserveScroll: true,

                onError: () => {
                alert("failed");
                },
                });

        }
    },
    async mounted()
    {
              const res = await axios.get("/api/GetUsConf");
    const data = res.data.form[0];

    // Nur bestehende Felder von useForm setzen
    this.form.xch_newsletter = data.xch_newsletter ?? "";
    this.form.xis_pmtoautomail = String(data.xis_pmtoautomail ?? "0");
    this.form.cnt_numrows = String(data.cnt_numrows ?? "0");
    }
};
</script>

