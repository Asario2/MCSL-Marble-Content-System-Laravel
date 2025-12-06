<template>
    <div class="relative inline-block w-full">
    <input
        :value="modelValue"
        :id="name"
        :name="name"
        ref="input"
        :type="type"
        class="w-full p-2.5 text-sm rounded-lg block border focus:ring-3 focus:ring-opacity-75 bg-layout-sun-0 text-layout-sun-900 border-primary-sun-500 focus:border-primary-sun-500 focus:ring-primary-sun-500 placeholder:text-layout-sun-400 selection:bg-layout-sun-200 selection:text-layout-sun-1000 dark:bg-layout-night-0 dark:text-layout-night-900 dark:border-primary-night-500 dark:focus:border-primary-night-500 dark:focus:ring-primary-night-500 placeholder:dark:text-layout-night-400 dark:selection:bg-layout-night-200 dark:selection:text-layout-night-1000"
        :placeholder="placeholder"
        :required="required"
        @input="$emit('update:modelValue', $event.target.value)"
    />

    <!-- Sternchen rechts im Input -->
    <span
        v-if="required"
        class="absolute right-2 top-1/2 -translate-y-1/2 text-red-600 pointer-events-none"
    >
        *
    </span>
</div>

</template>
<script>
export default {
    name: "Contents_Form_InputElement",

    props: {
        name: {
            type: String,
            required: true,
        },
        modelValue: {
            type: [Number, String],
            default: "",
        },
        type:{
            default:'text',
        },
        placeholder: {
            type: String,
            default: "",
        },
        required: {
            type: Boolean,
            default: false,
        },
    },

    emits: ["update:modelValue"],

    methods: {
        focus() {
            this.$refs.input.focus();
        },
    },
};
</script>
<style scoped>
.input-with-asterisk {
  position: relative;
  display: inline-flex;
  align-items: center;
}

.input-field {
  padding-right: 1.2em; /* Platz für das * */
}

.asterisk {
  color: red;
  margin-left: -1.2em; /* direkt rechts am Input */
  pointer-events: none;
  font-weight: bold;
}
</style>

