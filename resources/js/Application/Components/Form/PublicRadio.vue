<template>
    <fieldset :id="id" class="space-y-2">
      <legend v-if="legend" class="text-sm font-semibold text-layout-sun-1000 dark:text-layout-night-1000">
        {{ legend }}
      </legend>

      <div class="flex flex-col sm:flex-row gap-3">
        <!-- Öffentlich (1) -->
        <label :for="id + '-public'" class="group cursor-pointer select-none flex-1">
          <input
            type="radio"
            class="sr-only peer"
            :id="id + '-public'"
            :name="name"
            value="1"
            v-model="internalValue"
          />
          <div
            class="flex items-center gap-3 p-3 rounded-md border transition
                   border-layout-sun-400 dark:border-layout-night-400
                   peer-checked:border-primary-sun-500 dark:peer-checked:border-primary-night-500
                   peer-checked:bg-primary-sun-50 dark:peer-checked:bg-primary-night-50
                   hover:bg-layout-sun-100 dark:hover:bg-layout-night-100"
          >
            <slot name="icon-public">
              <!-- Globe Icon -->
              <svg viewBox="0 0 24 24" class="w-6 h-6 text-primary-sun-600 dark:text-primary-night-600" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10" />
                <line x1="2" y1="12" x2="22" y2="12" />
                <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
              </svg>
            </slot>
            <span class="text-layout-sun-1000 dark:text-layout-night-1000 font-medium">{{ publicLabel }}</span>
          </div>
        </label>

        <!-- Nicht öffentlich (0) -->
        <label :for="id + '-private'" class="group cursor-pointer select-none flex-1">
          <input
            type="radio"
            class="sr-only peer"
            :id="id + '-private'"
            :name="name"
            :value="0"
            v-model="internalValue"
          />
          <div
            class="flex items-center gap-3 p-3 rounded-md border transition
                   border-layout-sun-400 dark:border-layout-night-400
                   peer-checked:border-primary-sun-500 dark:peer-checked:border-primary-night-500
                   peer-checked:bg-primary-sun-50 dark:peer-checked:bg-primary-night-50
                   hover:bg-layout-sun-100 dark:hover:bg-layout-night-100"
          >
            <slot name="icon-private">
              <!-- Lock Icon -->
              <svg viewBox="0 0 24 24" class="w-6 h-6 text-primary-sun-600 dark:text-primary-night-600" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
              </svg>
            </slot>
            <span class="text-layout-sun-1000 dark:text-layout-night-1000 font-medium">{{ privateLabel }}</span>
          </div>
        </label>
      </div>
    </fieldset>

    <!-- Hidden Input für klassische Form-Submit-Kompatibilität -->
    <!-- <input type="hidden" name="pub" id="pub_alt" :value="internalValue" /> -->
    <input type="hidden" name="pub_alt" id="pub_alt" :value="internalValue" />
  </template>

  <script>
  export default {
    name: 'PublicRadio',

    props: {
      modelValue: {
        type: [Number, String, Boolean],
        default: null
      },
      publicLabel: {
        type: String,
        default: 'Öffentlich'
      },
      privateLabel: {
        type: String,
        default: 'Nicht öffentlich'
      },
      name: {
        type: String,
        default: 'pub'
      },
      legend: {
        type: String,
        default: ''
      },
      id: {
        type: String,
        default: () => 'pub'
      },
      is_created: {
        type: Boolean,
        default: false
      }
    },

    data() {
      return {
        internalValue: null


      };
    },
    created() {
  const normalized = this.normalizeValue(this.modelValue);

  if (this.is_created && (normalized === '' || normalized === undefined)) {
    this.internalValue = '1';
    this.emitChange('1');
  } else {
    this.internalValue = normalized;
  }
},
    watch: {
      internalValue(val) {
        this.emitChange(val);
      },
      modelValue(val) {
        this.internalValue = this.normalizeValue(val);
      }
    },

    is_created() {
      if (this.is_created && (this.modelValue === null || this.modelValue === undefined || this.modelValue === '')) {
        alert("created");
        this.internalValue = '1';
        this.emitChange('1');
      }
    },

    methods: {
      normalizeValue(val) {
        return (val === 1 || val === '1' || val === true) ? '1'
             : (val === 0 || val === '0' || val === false) ? '0'
             : '';
      },
      emitChange(val) {
        this.$emit('update:modelValue', val);
        this.$emit('change', val);
      }
    },
    mounted() {
        // const normalized = this.normalizeValue(this.modelValue);
        // this.internalValue = (normalized === undefined || normalized === "undefined") ? 1 : normalized;
    },
  };
  </script>
