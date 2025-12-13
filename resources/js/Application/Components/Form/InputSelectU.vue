<template>
<!-- <label :for="name">Benutzer</label> -->
<div>
      <select
        v-bind="$attrs"
        class="w-fully wf_2 wff p-2.5 text-sm rounded-lg block border border-layout-sun-300 text-layout-sun-900 bg-layout-sun-50 placeholder-layout-sun-400 focus:ring-primary-sun-500 focus:border-primary-sun-500 dark:border-layout-night-300 dark:text-layout-night-900 dark:bg-layout-night-50 dark:placeholder-layout-night-400 dark:focus:ring-primary-night-500 dark:focus:border-primary-night-500"
        v-model="internalValue"
        @change="handleChange"
        :name="xname"
        :id="name"
        :required="required"
        :disabled="disabled"
        >
        <option disabled value="0">Bitte wählen</option>
        <option
          v-for="option in optionsList"
          :key="option.id"
          :value="option.id"
        >
          {{ option.name }}
        </option>
      </select>
    </div>
    <input type="hidden" :name="xname" :id="name + '_alt'" :value="resolvedValue">
 </template>

  <script>

  import axios from "axios";



  export default {
    name: "Contents_Form_InputSelect",
    emits: ["update:modelValue", "input-change"],
    inheritAttrs: false,          // verhindert doppelte :class‑Warnung (optional)

    props: {
      modelValue: [String, Number],
      name: { type: String, required: true },
      xname: { type: String, default: "select-field" },
      required: { type: [Boolean, String], default: false },
      options: { type: [Array, Object], default: null },
      disabled:  { type: Boolean, default: false },
      owner: {type:[String,Number],
        default:null,
      }

    },

    data() {
      return {
        internalValue: this.modelValue ?? "",
        fetchedOptions: [],
        UID: window?.Laravel?.userId || null,
      };
    },

    computed: {
        resolvedValue() {
            return this.internalValue < 1 ? "0" : this.internalValue;
        },
      optionsList() {
        let opts = [];
        if (Array.isArray(this.options)) {
        opts = this.options;
        } else if (Array.isArray(this.fetchedOptions)) {
        opts = this.fetchedOptions;
        } else if (typeof this.fetchedOptions === "object") {
        opts = Object.entries(this.fetchedOptions).map(([key, value]) => {
            if (typeof value === "object" && "id" in value && "name" in value) {
            return value;
            }
            return { id: key, name: value };
        });
        }

        // Normalisiere die Objekte
        opts = opts.map(opt => {
        if (typeof opt === "object" && "id" in opt && "name" in opt) {
            return opt;
        }
        return { id: opt.id || opt[0], name: opt.name || opt[1] };
        });

        // 👇 Hier filtern wir alle außer dem Owner heraus
       if (this.owner) {
        opts = opts.filter(o => String(o.id) !== String(this.owner));
        }

        return opts;
    },
    },
    watch: {
      modelValue(newVal) {
        this.internalValue = newVal;
      },
      internalValue(val) {
        this.$emit("update:modelValue", val);
      },
    },

    methods: {
      handleChange(event) {
        const value = event.target.value;
        this.$emit("update:modelValue", value);
        this.$emit("input-change", value, this.xname);
      },

      async getOptions() {
        try {
          const res = await axios.get(`/tables/sort-data/${this.name}`);
          const key = `${this.name}.sortedOptions`;

          // console.log(`[${this.name}] API-Rohdaten:`, res.data);

          if (Array.isArray(res.data)) {
            this.fetchedOptions = res.data;
          } else if (res.data && Array.isArray(res.data[key])) {
            this.fetchedOptions = res.data[key];
          } else if (res.data && typeof res.data[key] === "object") {
            this.fetchedOptions = res.data[key]; // wird später konvertiert
          } else {
            // console.log(`[${this.name}] Keine passenden Daten im Response:`, res.data);
          }
        } catch (error) {
          console.error(`[${this.name}] Fehler beim Abrufen der Daten:`, error);
        }
      },
    },

    mounted() {

       const isCreatePage = window.location.pathname.includes("create");

if (isCreatePage && !this.internalValue && this.currentUserId) {
  this.internalValue = this.currentUserId;
}

if (!Array.isArray(this.options) || this.options.length === 0) {
    console.log(`[${this.name}] Keine Props-Optionen – hole aus API`);
    this.getOptions();
  } else {
    console.log(`[${this.name}] Lokale Optionen erkannt:`, this.options);
  }
    }
  };
  </script>
<style scoped>
.w-fully{
    min-width:94%;
    max-width:94%;
}
</style>

