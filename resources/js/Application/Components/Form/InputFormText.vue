<template>
    <div class="mt-4">
      <label
        :for="id"
        class="w-full text-sm font-medium text-gray-700 dark:text-gray-300 !mt-4"
      >
        <slot name="label">Label</slot>
      </label>
      <input
        type="text"
        :id="id"
        :name="name"
        :placeholder="placeholder"
        :required="required"
        :disabled="disabled"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        :class="[
          'w-full p-2.5 mt-4 mb-4 text-sm rounded-lg block border focus:ring-3 focus:ring-opacity-75',
          'bg-layout-sun-0 text-layout-sun-900 border-primary-sun-500 focus:border-primary-sun-500 focus:ring-primary-sun-500 placeholder:text-layout-sun-400',
          'selection:bg-layout-sun-200 selection:text-layout-sun-1000',
          'dark:bg-layout-night-0 dark:text-layout-night-900 dark:border-primary-night-500 dark:focus:border-primary-night-500 dark:focus:ring-primary-night-500 placeholder:dark:text-layout-night-400',
          'dark:selection:bg-layout-night-200 dark:selection:text-layout-night-1000',
          $attrs.class
        ]"
        v-bind="$attrs"
      />
    </div>
    <input type="hidden" :name="name" :id="name + '_alt'" :value="modelValue" />
    </template>

  <script>
  export default {
    name: 'InputFormText',
    inheritAttrs: false, // <-- Damit $attrs.class manuell verarbeitet werden kann
    props: {
      id: { type: String, required: true },
      name: { type: String, required: true },
      modelValue: { type: [String, Number], default: '' },
      placeholder: { type: String, default: '' },
      required: { type: [String, Boolean], default: '' },
      disabled: Boolean,
    },
  };
  </script>

  <style scoped>
  /* Optional: Weitere Styles */
  </style>
