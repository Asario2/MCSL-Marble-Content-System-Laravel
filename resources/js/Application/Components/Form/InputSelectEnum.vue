<template>
    <select
        v-bind="$attrs"
        class="w-fully wf_2 p-2.5 text-sm rounded-lg block border border-layout-sun-300 text-layout-sun-900 bg-layout-sun-50 placeholder-layout-sun-400 focus:ring-primary-sun-500 focus:border-primary-sun-500 dark:border-layout-night-300 dark:text-layout-night-900 dark:bg-layout-night-50 dark:placeholder-layout-night-400 dark:focus:ring-primary-night-500 dark:focus:border-primary-night-500"
        :name="name"
        @change="onChange($event)"
        ref="select"
        :id="name"
        :tablex="tablex"
        :required="required"
        :disabled="disabled || isLoading"
        v-model="internalValue"
    >
        <!-- Default Option -->
        <option value="0">Bitte wählen</option>

        <!-- ECHTE Options aus parsedOptions anzeigen -->
        <option v-for="option in parsedOptions" :key="option.id" :value="option.id">
            {{ option.name }}
        </option>

        <!-- Loading State -->
        <option v-if="isLoading" value="loading" disabled>Lade Optionen...</option>
    </select>

    <input type="hidden" :name="name" :id="name + '_alt'" :value="internalValue" />
</template>

<script>
    import $ from "jquery";
    window.$ = window.jQuery = $;

    export default {
        name: "InputSelectEnum",
        inheritAttrs: false,

        data() {
            return {
                rawOptions: [],
                internalValue: "0",
                isLoading: true,
                isInitialized: false,
                pendingDbValue: null
            };
        },

        props: {
            required: { type: [String, Boolean], default: false },
            modelValue: {
                type: [String, Number],
            },
            uname: { type: String },
            xid: { type: [String,Number] },
            name: { type: String },
            options: {
                type: [Array, Object],
                required: false,
            },
            sortColumn: {
                type: Number,
                default: 1,
            },
            xval: {
                type: [String, Number],
                default: 0,
            },
            xname: {
                type: String,
                default: "select-field",
            },
            id: {
                type: String,
            },
            tablex: {
                type: String,
            },
            existingEntryId: String,
            disabled: { type: Boolean, default: false },
        },

        emits: ["input-change", "update:modelValue", "update:xval"],

        watch: {
            xval: {
                immediate: true,
                handler(newValue) {
//                     console.log('🔹 DB Value received:', newValue);
                    this.pendingDbValue = newValue;
                    this.applyPendingValue();
                }
            },

            internalValue(newValue) {
                if (this.isInitialized && newValue !== "0") {
//                     console.log('🔹 Internal value changed to:', newValue);
                    this.$emit('update:xval', newValue);
                    this.$emit('update:modelValue', newValue);

                    $('#status_undefined').val(newValue).trigger('change');
                    $('#itemscope_undefined').val(newValue).trigger('change');
                }
            },

            // Wenn Options geladen sind, Wert setzen
            rawOptions: {
                handler(newOptions) {
//                     console.log('🔹 Raw options loaded, count:', newOptions.length);
                    this.isLoading = false;
                    this.applyPendingValue();
                },
                deep: true
            }
        },

        computed: {
            // Parse die JSON Options in ein verwendbares Format
            parsedOptions() {
                if (!this.rawOptions || this.rawOptions.length === 0) return [];

                const options = [];

                this.rawOptions.forEach(item => {
                    // Wenn es ein String ist, versuche JSON zu parsen
                    if (typeof item === 'string') {
                        try {
                            // Entferne mögliche überflüssige Zeichen und parse JSON
                            const cleanJson = item.replace(/^\[|\]$/g, '').trim();
                            const parsed = JSON.parse(`[${cleanJson}]`);
                            options.push(...parsed);
                        } catch (error) {
                            console.warn('❌ Failed to parse options JSON:', error);
//                             console.log('Raw string:', item);
                        }
                    }
                    // Wenn es bereits ein Objekt ist, direkt verwenden
                    else if (typeof item === 'object') {
                        options.push(item);
                    }
                });

//                 console.log('🔹 Parsed options:', options);
                return options;
            }
        },

        methods: {
            applyPendingValue() {
                if (this.pendingDbValue && this.parsedOptions.length > 0 && !this.isInitialized) {
                    this.$nextTick(() => {
                        const dbValue = this.pendingDbValue;
//                         console.log('🔹 Applying DB value:', dbValue, 'to parsed options:', this.parsedOptions);

                        // Prüfe ob der Wert in Options existiert
                        const valueExists = this.parsedOptions.some(option =>
                            String(option.id) === String(dbValue)
                        );

                        if (valueExists) {
//                             console.log('✅ DB value found in options, setting to:', dbValue);
                            this.internalValue = String(dbValue);
                            this.isInitialized = true;
                        } else {
                            console.warn('❌ DB value not found in options:', dbValue);
//                             console.log('Available options:', this.parsedOptions.map(opt => opt.id));
                            this.internalValue = "0";
                            this.isInitialized = true;
                        }

                        this.pendingDbValue = null;
                    });
                }
            },

            normalizeValue(value) {
                if (value === null || value === undefined || value === '' || value === '0') {
                    return "0";
                }
                return String(value);
            },

            onChange(event) {
                const value = event.target.value;
//                 console.log('🔹 User selected:', value);
                this.internalValue = this.normalizeValue(value);

                this.$emit('input-change', this.internalValue, this.xname);
                this.$emit('update:modelValue', this.internalValue);
                this.$emit('update:xval', this.internalValue);
            },

            getOptions() {
//                 console.log('🔹 Loading options for:', this.tablex, this.name);
                this.isLoading = true;

                const table = this.tablex || 'default';
                const url = `/tables/sort-enum/${table}/${this.name}`;
//                 console.log('🔹 API URL:', url);

                axios.get(url)
                .then(response => {
//                     console.log('✅ API Response:', response.data);

                    // Die Response kommt als Array mit JSON-Strings zurück
                    // Wir müssen diese parsen
                    this.rawOptions = this.processApiResponse(response.data);
//                     console.log('✅ Processed raw options:', this.rawOptions);

                })
                .catch(error => {
                    console.error('❌ Error loading options:', error);
                    // Fallback
                    this.rawOptions = this.options || [];
                });
            },

            // Verarbeitet die API Response die JSON-Strings enthält
            processApiResponse(data) {
                if (!data) return [];

                let processed = [];

                // Wenn data ein Array ist
                if (Array.isArray(data)) {
                    data.forEach(item => {
                        if (typeof item === 'string' && item.includes('[{')) {
                            // Versuche JSON Array zu parsen
                            try {
                                // Extrahiere den JSON Teil
                                const jsonMatch = item.match(/\[.*\]/);
                                if (jsonMatch) {
                                    const parsed = JSON.parse(jsonMatch[0]);
                                    processed.push(...parsed);
                                } else {
                                    processed.push(item);
                                }
                            } catch (error) {
                                console.warn('JSON parse error:', error);
                                processed.push(item);
                            }
                        } else {
                            processed.push(item);
                        }
                    });
                }
                // Wenn data ein Objekt ist
                else if (typeof data === 'object') {
                    // Prüfe ob es den expected key hat
                    const expectedKey = `${this.name}.sortedOptions_sel`;
                    if (data[expectedKey] && Array.isArray(data[expectedKey])) {
                        processed = data[expectedKey];
                    } else {
                        // Fallback: verwende alle Werte
                        processed = Object.values(data);
                    }
                }

                return processed;
            },

            debugState() {
//                 console.log('=== SELECT DEBUG ===');
//                 console.log('Pending DB Value:', this.pendingDbValue);
//                 console.log('Internal Value:', this.internalValue);
//                 console.log('Raw Options:', this.rawOptions);
//                 console.log('Parsed Options:', this.parsedOptions);
//                 console.log('isInitialized:', this.isInitialized);
//                 console.log('isLoading:', this.isLoading);

                const select = this.$refs.select;
                if (select) {
//                     console.log('DOM Select value:', select.value);
//                     console.log('Selected option text:', select.options[select.selectedIndex]?.text);
                }
            }
        },

        mounted() {
//             console.log("✅ Component mounted");
//             console.log("DB value (xval):", this.xval);

            this.getOptions();

            setTimeout(() => {
                this.debugState();
            }, 2000);
        }
    };
</script>

<style scoped>
.w50 {
    color: aquamarine;
}

select:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.w-fully{
    width:100%;
}
</style>
