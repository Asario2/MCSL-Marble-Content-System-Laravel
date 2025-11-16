<template>
    <div class="mt-4">
      <label :for="name" class="block text-sm font-medium">{{ label }}</label>
      <select
        :name="name"
        :id="name"
        class="mt-1 block w-full rounded border-gray-300 dark:bg-gray-700"
        :value="modelValue"
        @change="updateValue"
      >
        <option value="">Bitte wählen…</option>
        <option
          v-for="option in options"
          :key="option.tag"
          :value="option.tag"
        >
          {{ option.name }}
        </option>
      </select>
    </div>
  </template>

<script>
import axios from 'axios';

export default {
  name: 'CopyleftSelect',
  props: {
    modelValue: String,
    label: String,
    name: {
      type: String,
      default: 'copyleft',
    },
  },
  data() {
    return {
      options: [],
    };
  },
  mounted() {
    axios.get(route('images.copyleft')).then(response => {
      this.options = response.data;
    });
  },
  methods: {
    updateValue(event) {
      this.$emit('update:modelValue', event.target.value);
    },
  },
};
</script>

