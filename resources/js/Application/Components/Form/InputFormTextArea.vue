<template>
    <label
        :for="id"
        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
      >
        <slot name="label"></slot>
      </label>
      <InputHtml
      :id="name"
        :name="name"
        ></InputHtml>
    <!-- <textarea
        :id="id"
        :cols="20"
        :name="name"
        :rows="rows"
        class="textar block p-2.5 w-full text-sm rounded-lg border border-layout-sun-300 text-layout-sun-900 bg-layout-sun-50 placeholder-layout-sun-400 focus:ring-primary-sun-500 focus:border-primary-sun-500 dark:border-layout-night-300 dark:text-layout-night-900 dark:bg-layout-night-50 dark:placeholder-layout-night-400 dark:focus:ring-primary-night-500 dark:focus:border-primary-night-500"
        :placeholder="placeholder"
        :required="required"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
    >
    </textarea> -->
</template>


<script>
import InputHtml from "@/Application/Components/Form/InputHtml.vue";

export default {
    name: "InputFormTextArea",
    components: {

InputHtml,

},
    props: {
        id: { type: String, required: true },
        name: { type: String, required: true },
        modelValue: { type: [String,Number], default: "" }, // Use modelValue instead of value
        placeholder: { type: String, default: "" },
        required: { type: [String, Boolean], default: false },
        rows: {
            type: [String, Array, Object],
            default: 4,
        },
    },
};
</script>

