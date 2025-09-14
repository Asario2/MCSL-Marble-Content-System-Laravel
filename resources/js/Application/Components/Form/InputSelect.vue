<template>
    <div>
      <select
      v-bind="$attrs"
      :disabled="disabled"
        class="w-fully wf_2 wff p-2.5 text-sm rounded-lg block border border-layout-sun-300 text-layout-sun-900 bg-layout-sun-50 placeholder-layout-sun-400 focus:ring-primary-sun-500 focus:border-primary-sun-500 dark:border-layout-night-300 dark:text-layout-night-900 dark:bg-layout-night-50 dark:placeholder-layout-night-400 dark:focus:ring-primary-night-500 dark:focus:border-primary-night-500"
        v-model="internalValue"
        @change="handleChange"
        :name="xname"
        :id="name"
        :required="required"
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
    <input type="hidden" :name="name" :id="name + '_alt'" :value="internalValue">
 </template>

  <script>
  import axios from "axios";

  export default {
    name: "Contents_Form_InputSelect",
    inheritAttrs: false,
    emits: ["update:modelValue", "input-change"],

    props: {
      modelValue: [String, Number],
      name: { type: String, required: true },
      xname: { type: String, default: "select-field" },
      required: { type: [Boolean, String], default: false },
      options: { type: [Array, Object], default: null },
    //   VAXX:[String,Number],
    disabled:  { type: Boolean, default: false },
    },

    data() {
      return {
        internalValue: this.modelValue !== undefined ? this.modelValue : 0,
        fetchedOptions: [],
        VAXX: '0',
      };
    },

    computed: {
        resolvedValue() {
            return this.internalValue < 1 ? "0" : this.internalValue;
        },
      optionsList() {
        let opts = [];

        if (Array.isArray(this.options)) {
          // console.log(`[${this.name}] Props-Optionen erkannt:`, this.options);
          opts = this.options;
        } else if (Array.isArray(this.fetchedOptions)) {
          // console.log(`[${this.name}] FetchedOptions als Array:`, this.fetchedOptions);
          opts = this.fetchedOptions;
        } else if (typeof this.fetchedOptions === "object") {
          console.warn(`[${this.name}] FetchedOptions ist Objekt – versuche in Array umzuwandeln`, this.fetchedOptions);

          // Objekt → Array umwandeln
          opts = Object.entries(this.fetchedOptions).map(([key, value]) => {
            if (typeof value === "object" && "id" in value && "name" in value) {
              return value;
            }
            return { id: key, name: value };
          });

          // console.log(`[${this.name}] Umgewandelte Objekt-Optionen:`, opts);
        }

        return opts.map(opt => {
          if (typeof opt === "object" && "id" in opt && "name" in opt) {
            return opt;
          }
          return { id: opt.id || opt[0], name: opt.name || opt[1] };
        });
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
        let value = event.target.value;

        // Zahl forcieren, wenn deine IDs numerisch sind:
        if (!isNaN(value)) {
            value = Number(value);
        }

        this.internalValue = value;
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
      if (!(Array.isArray(this.options) && this.options.length < 1)) {
        // console.log(`[${this.name}] Hole Optionen per API`);
        this.getOptions();
      } else {
        // console.log(`[${this.name}] Lokale Optionen vorhanden`);
      }
    },
  };
  </script>
<style scoped>
.w-fully{
    min-width:94%;
    max-width:94%;
}
</style>
