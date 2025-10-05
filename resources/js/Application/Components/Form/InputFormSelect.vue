    <template>
        <div>
        <select
            v-bind="$attrs"
            :id="xname + '_' + (xid ?? id ?? 'sel')"
            class="w-fully wf_2 p-2.5 text-sm rounded-lg block border border-layout-sun-300
                text-layout-sun-900 bg-layout-sun-50 placeholder-layout-sun-400 focus:ring-primary-sun-500
                focus:border-primary-sun-500 dark:border-layout-night-300 dark:text-layout-night-900
                dark:bg-layout-night-50 dark:placeholder-layout-night-400 dark:focus:ring-primary-night-500
                dark:focus:border-primary-night-500"
            style="min-width:100%"
            v-model="internalValue"
            @change="onChange"
            :required="required"
            :disabled="disabled"
            ref="select"
        >
            <!-- Default-Option - WIRD AUTOMATISCH SELEKTIERT WENN KEIN WERT VORHANDEN -->
            <option :value="defaultValue">{{ defaultLabel }}</option>

            <!-- Array-Format: [[value,label], ...] -->
            <template v-if="Array.isArray(sortedOptions_sel)">
            <option
                v-for="(option, key) in sortedOptions_sel"
                :value="String(option[0])"
                :key="key"
            >
                {{ option[1] }}
            </option>
            </template>

            <!-- Objekt-Format: { value: label, ... } -->
            <template v-else-if="typeof sortedOptions_sel === 'object'">
            <option
                v-for="(value, key) in sortedOptions_sel"
                :value="String(key)"
                :key="key"
            >
                {{ value }}
            </option>
            </template>
        </select>

        <!-- Hidden field für klassische Form-Post -->
        <input type="hidden" :name="xname" id="hv" :value="internalValue" />
        </div>
    </template>

    <script>
    export default {
        name: "Contents_Form_InputSelect",
        inheritAttrs: false,

        props: {
        required: { type: [Number, String, Boolean], default: false },
        modelValue: { type: [String, Number], default: null }, // v-model extern
        options: { type: [Array, Object], required: true },
        sortColumn: { type: Number, default: 1 },
        // original API: allow prop xval (legacy) but don't collide with internal state
        xval: { type: [String, Number], default: null },
        xname: { type: String, default: "select-field" },
        id: { type: String, default: null },
        xid: { type: [String, Number], default: null },
        name: { type: String, default: null },
        disabled: { type: Boolean, default: false },
        // Neue Props für Default-Wert
        defaultValue: { type: [String, Number], default: "0" },
        defaultLabel: { type: String, default: "Bitte wählen" }
        },

        emits: ["update:modelValue", "input-change"],

        data() {
        return {
            // internalValue is the single source of truth inside the component
            internalValue: this._initialValue(),
        };
        },

        watch: {
        // if parent changes modelValue, reflect inside
        modelValue(newVal) {
            this.internalValue = this._normalizeValue(newVal);
        },
        // legacy: if prop xval changes externally, sync (rare)
        xval(newVal) {
            if ((this.modelValue === null || this.modelValue === undefined)) {
            this.internalValue = this._normalizeValue(newVal);
            }
        },
        },

        computed: {
        // Normalize options to either array or object as provided
        sortedOptions_sel() {
            if (Array.isArray(this.options)) {
            return [...this.options];
            } else if (typeof this.options === "object" && this.options !== null) {
            // return plain object to iterate key/value
            return Object.fromEntries(Object.entries(this.options));
            }
            return this.options;
        },
        },

        methods: {
        _normalizeValue(value) {
            // Prüfe auf undefined, null, leeren String oder ungültige Werte
            if (value === null || value === undefined || value === '' || value === '0') {
            return this.defaultValue;
            }
            return value;
        },

        _initialValue() {
            // priority: modelValue > xval > defaultValue
            const value = (this.modelValue !== null && this.modelValue !== undefined) ? this.modelValue : this.xval;
            return this._normalizeValue(value);
        },

        onChange(evt) {
            // internalValue already set by v-model
            // emit model update for v-model usage
            this.$emit("update:modelValue", this.internalValue);
            // legacy event your app uses
            this.$emit("input-change", this.internalValue, this.xname);
            // update hidden input `hv` already bound by :value
        },

        // legacy actsel (keeps old behavior if other code expects jQuery hook)
        actsel() {
            const el = this.$refs.select;
            if (!el) return;
            // native listener already added by @change -> but we keep this for optional legacy jQuery usage
            if (window && window.$ && typeof window.$ === "function") {
            // jQuery fallback (keeps compatibility with old code)
            const selId = this.xname + '_' + (this.xid ?? this.id ?? 'sel');
            const $el = window.$(`#${selId}`);
            if ($el && $el.length) {
                $el.off('change.contents_form_inputselect').on('change.contents_form_inputselect', () => {
                const val = $el.val();
                this.internalValue = this._normalizeValue(val);
                this.onChange();
                });
            }
            }
        },

        // helper to set programmatically
        setValue(val) {
            this.internalValue = this._normalizeValue(val);
            this.$emit("update:modelValue", this.internalValue);
        },

        // Methode um explizit auf Default zu setzen
        resetToDefault() {
            this.internalValue = this.defaultValue;
            this.$emit("update:modelValue", this.internalValue);
            this.$emit("input-change", this.internalValue, this.xname);
        },

        // Debug-Methode um den aktuellen Zustand zu prüfen
        debugState() {
            console.log('Internal Value:', this.internalValue);
            console.log('Model Value:', this.modelValue);
            console.log('XVal:', this.xval);
            console.log('Default Value:', this.defaultValue);
        }
        },

        mounted() {
            this.debugState();
            alert("MOUNTED");
        // ensure initial state is emitted (if parent needs it)
        this.$nextTick(() => {
            // Stelle sicher, dass der Default-Wert gesetzt wird
            this.internalValue = this._normalizeValue(this.internalValue);

            this.$emit("update:modelValue", this.internalValue);
            // init legacy handler if desired
            this.actsel();

            // Debug: Prüfe den initialen Zustand
            console.log('Select mounted - Value:', this.internalValue);
        });

        },
    };
    </script>

    <style scoped>
    .wf_2 {
        min-width: 100%;
    }
    .w-fully {
        min-width: 200%;
        max-width: 200%;
    }
    </style>
