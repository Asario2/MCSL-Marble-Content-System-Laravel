<template>
  <div class="flex space-x-3 flex-wrap mt-[5px]">
    <label
      v-for="option in options"
      :key="option.value"
      class="flex items-center cursor-pointer"
    >
      <!-- HIDDEN RADIO (peer) -->
        <input
        type="radio"
        class="peer hidden"
        :name="name"
        :value="option.value"
        :checked="String(modelValue) === String(option.value)"
        @change="$emit('update:modelValue', option.value)"
        />

      <!-- VISIBLE RADIO -->
   <span
  class="w-5 h-5 flex items-center justify-center rounded-full border border-primary-sun-300
         bg-primary-sun-50
         transition-all
         peer-checked:bg-primary-sun-500 peer-checked:ring-2 peer-checked:ring-white
         dark:bg-primary-night-50 dark:border-primary-night-300 dark:peer-checked:bg-primary-night-500"
>
  <span class="radio-dot"></span>
</span>



      <!-- LABEL -->
      <span class="ml-2 text-sm text-gray-700 dark:text-gray-300 mt-[5px] mx-4">
        {{ option.label }}
      </span>
    </label>
  </div>
</template>

<script>
export default {
  name: "RadioSet",

  props: {
    name: { type: String, required: true },
    options: { type: Array, required: true },
    modelValue: { type: [String, Number, Boolean], default: null },
  },

  emits: ["update:modelValue"],

  methods: {
    isChecked(value) {
      const result = String(this.modelValue) === String(value);

      console.log(
        `%c[RadioSet] checked?`,
        "color: #00c4ff",
        {
          name: this.name,
          modelValue: this.modelValue,
          optionValue: value,
          result,
        }
      );

      return result;
    },

    onChange(newValue) {
      console.log(
        `%c[RadioSet] onChange event`,
        "color: #22ff55",
        {
          name: this.name,
          newValue,
        }
      );

      this.$emit("update:modelValue", newValue);

      console.log(
        `%c[RadioSet] emitted update:modelValue`,
        "color: #ffaa00",
        { newValue }
      );
    },
  },

  mounted() {
    console.log(
      `%c[RadioSet] mounted – initial modelValue`,
      "color: #ff44cc",
      {
        name: this.name,
        modelValue: this.modelValue,
      }
    );
  },
};
</script>

<style scoped>
span {
  transition: all 0.15s ease;
}
.radio-dot {
  width: 7px;
  height: 7px;
  display: block;
  border-radius: 9999px;
  background: white;
  opacity: 0;
  transition: opacity .15s ease;
}

.peer:checked + span .radio-dot {
  opacity: 1;
}
</style>
