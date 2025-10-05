<template>
    <div>
      <label :for="id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        <slot name="label">Label</slot>
      </label>

      <!-- Name anzeigen, readonly -->
      <input
        type="text"
        id="uss_p"
        :name="name"
        maxlength="50"
        :placeholder="placeholder"
        :required="required"
        :disabled="disabled"
        :value="localValue"
        readonly


        class="txt bg-gray-400 px-4 py-2 rounded text-gray-900 dark:text-gray-100 text-opacity-50 dark:text-opacity-75
          disabled:opacity-50 disabled:cursor-not-allowed
          p-2.5 text-sm rounded-lg inline-block  focus:ring-opacity-75
          bg-layout-sun-0 text-layout-sun-900 focus:ring-primary-sun-500 placeholder:text-layout-sun-400 selection:bg-layout-sun-200
          selection:text-layout-sun-1000 dark:bg-layout-night-50 dark:text-layout-night-900
          dark:focus:ring-primary-night-500 bg
          placeholder:dark:text-layout-night-400 dark:selection:bg-layout-night-200 dark:selection:text-layout-night-1000"
      />

      <!-- Verstecktes Feld für die User-ID -->
      <input type="hidden" id="us_poster" name="us_poster" :value="uval" />
    </div>
  </template>

<script>
export default {
  name: "InputFormPoster",
  props: {
    id: { type: String, required: true },
    name: { type: String, required: true },
    modelValue: { type: [String, Number], default: "" }, // initial User-ID
    placeholder: { type: String, default: "" },
    required: { type: [String, Boolean], default: false },
    disabled: { type: Boolean, default: false },
  },
  data() {
    return {
      localValue: "", // Name anzeigen
      uval: this.modelValue, // User-ID
    };
  },
  watch: {
    modelValue(newVal) {
      // Wenn die ID von außen geändert wird, Name neu laden
      this.uval = newVal;
      this.fetchUserName(newVal);
    },
  },
  methods: {
    async ParseUser() {
      if (!this.uval) {
        try {
          const res = await axios.get("/api/user-id");
          this.uval = res.data.id;
          this.localValue = res.data.name;
          this.$emit("update:modelValue", this.uval); // <-- ModelValue zurückgeben
        } catch (err) {
          console.error("Fehler beim Laden des Users:", err);
        }
      } else {
        this.fetchUserName(this.uval);
      }
    },
    async fetchUserName(userId) {
      if (!userId) return;
      try {
        const res = await axios.get("/api/getuname", { params: { id: userId } });
        this.localValue = res.data.name;
        this.$emit("update:modelValue", userId); // <-- ModelValue zurückgeben
      } catch (err) {
        console.error("Fehler beim Laden des Users:", err);
      }
    },
  },
  mounted() {
    this.ParseUser();
  },
};
</script>
