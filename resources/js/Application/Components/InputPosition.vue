<template>
    <div>
      <label for="positionSelect" class="block mb-2 font-medium">Neue Position wählen:</label>
      <select
        class="w-full p-2.5 text-sm rounded-lg block border border-layout-sun-300 text-layout-sun-900 bg-layout-sun-50 placeholder-layout-sun-400 focus:ring-primary-sun-500
               focus:border-primary-sun-500 dark:border-layout-night-300
               dark:text-layout-night-900 dark:bg-layout-night-50 dark:placeholder-layout-night-400 dark:focus:ring-primary-night-500 dark:focus:border-primary-night-500"
        id="positionSelect"
        v-model.number="selectedPosition"
        @change="emitPosition"
      >
        <option :value="1">Ganz Oben</option>

<option
  v-for="item in filteredEntries"
  :key="item.id"
  :value="Number(item.position + 1)"
>
  Nach "{{ item.title }}"
</option>
      </select>
    </div>
    <input type="hidden" :name="name" id="position" :value="currentPosition">
  </template>

  <script>
  export default {
    name: "InputPosition",
    props: {
      entries: { type: Array, required: true }, // [{id, title, position}, ...]
      currentPosition: { type: Number, default: 1 },
      currentId: { type: Number, required: true },
      name: { type: String, default: "position" },
    },
    data() {
      return {
        selectedPosition: this.currentPosition,
      };
    },
    computed: {
      filteredEntries() {
        return this.entries.filter(e => e.id !== this.currentId);
      },
    },
    watch: {

    currentPosition(newVal) {
        this.selectedPosition = newVal;
    },
    entries: {

    handler() {
      this.selectedPosition = this.currentPosition;
    },
    immediate: true,
  }

    },
    methods: {
      emitPosition() {
        this.$emit("position-changed", this.selectedPosition);
      },
    },
  };
  </script>
