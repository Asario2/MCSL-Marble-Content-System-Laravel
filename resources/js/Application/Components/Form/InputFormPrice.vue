<template>
    <div>
      <label :for="id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        <slot name="label">Label</slot>
      </label>
      <input
        v-model="rawInput"
        @blur="formatAmount"
        type="text"
        :id="id"
        :name="name"
        maxlength="20"
        :placeholder="placeholder"
        :required="required"
        :disabled="disabled"
        class="txt
          p-2.5 text-sm rounded-lg inline-block border focus:ring-3 focus:ring-opacity-75
          bg-layout-sun-0 text-layout-sun-900 border-primary-sun-500 focus:border-primary-sun-500
          focus:ring-primary-sun-500 placeholder:text-layout-sun-400 selection:bg-layout-sun-200
          selection:text-layout-sun-1000 dark:bg-layout-night-0 dark:text-layout-night-900
          dark:border-primary-night-500 dark:focus:border-primary-night-500 dark:focus:ring-primary-night-500
          placeholder:dark:text-layout-night-400 dark:selection:bg-layout-night-200 dark:selection:text-layout-night-1000"
      />
      <b> €</b>
    </div>
  </template>

  <script>
  export default {
    name: "InputFormPrice",
    props: {
      id: { type: String, required: true },
      name: { type: String, required: true },
      modelValue: { type: [String, Number], default: "" },
      placeholder: { type: String, default: "" },
      required: { type: [String, Boolean], default: "" },
      disabled: Boolean,
    },
    data() {
      return {
        rawInput: this.formatNumber(this.modelValue),
      };
    },
    watch: {
      modelValue(newValue) {
        this.rawInput = this.formatNumber(newValue);
      },
    },
    methods: {
      formatNumber(value) {
        // console.log("FORMAT START:", value);
        if (value === "" || value === null || value === undefined) {
          return "";
        }

        // Konvertiere zu Float und ersetze Dezimaltrennzeichen
        let floatValue = parseFloat(value ? value.toString().replace(",", "."):'');
        // console.log("PARSE FLOAT:", floatValue);

        if (!isNaN(floatValue)) {
          // Stelle sicher, dass keine Tausendertrennzeichen entstehen
          let formatted = floatValue.toFixed(2).replace(".", ",");
        //   console.log("FORMAT OUTPUT:", formatted);
          return formatted;
        }

        return "";
      },
      parseNumber(value) {
        // console.log("PARSE START:", value);

        if (typeof value === "string") {
          // Entferne alle Leerzeichen und Tausendertrennzeichen (sicherheitshalber)
          value = value.replace(/\s/g, "").replace(",", ".");
        }

        let numericValue = parseFloat(value);
        // console.log("PARSE OUTPUT:", numericValue);

        return !isNaN(numericValue) ? numericValue : "";
      },
      formatAmount() {
        // console.log("FORMAT AMOUNT BEFORE:", this.rawInput);
        let parsed = this.parseNumber(this.rawInput);
        this.rawInput = this.formatNumber(parsed);
        this.$emit("update:modelValue", parsed);
        // console.log("FORMAT AMOUNT AFTER:", this.rawInput);
      },
    },
  };
  </script>
