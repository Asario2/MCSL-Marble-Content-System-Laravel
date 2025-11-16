<template>
    <label :for="name">{{ label }}</label>
    <select
      :id="name"
      :name="name"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      class="p-2.5 text-sm rounded-lg block border border-layout-sun-300 text-layout-sun-900 bg-layout-sun-50 placeholder-layout-sun-400 focus:ring-primary-sun-500 focus:border-primary-sun-500 dark:border-layout-night-300 dark:text-layout-night-900 dark:bg-layout-night-50 dark:placeholder-layout-night-400 dark:focus:ring-primary-night-500 dark:focus:border-primary-night-500"
    >
      <option disabled value="0">Bitte wählen</option>
      <option
        v-for="(option, index) in options"
        :key="index"
        :value="option.tag"
      >
        {{ option.name }}
      </option>
    </select>
  </template>

  <script>
  import axios from 'axios';

  export default {
    name: 'CopyleftSelect',
    props: {
      modelValue: String,
      label: String,
      name: String
    },
    emits: ['update:modelValue'],
    data() {
      return {
        options: [],
        user: [],
      };
    },
    async mounted() {
      const [copyleftResponse, userResponse] = await Promise.all([
        axios.get(route('images.copyleft')),
        axios.get(route('GetUserNull'))
      ]);

      this.options = copyleftResponse.data.copyleft;
      this.user = userResponse.data;

      // Wenn wir einen Usernamen haben, setzen wir den passenden Tag
      const firstUser = this.user[0];
      const match = this.options.find(o => o.name === firstUser);
      if (match) {
        this.$emit('update:modelValue', match.tag);
      }
    },
  };
  </script>

