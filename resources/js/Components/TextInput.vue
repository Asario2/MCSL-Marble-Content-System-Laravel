<script setup>
import { onMounted, ref, computed } from "vue";

defineProps({
    modelValue: [String,Number],
    required: [Boolean, String], // Optional: `required` als Boolean oder String erlauben
});

defineEmits(["update:modelValue"]);

const input = ref(null);

onMounted(() => {
    if (input.value?.hasAttribute("autofocus")) {
        input.value.focus();
    }
});

const isRequired = computed(() => {
    return props.required === "required" || props.required === true;
});

defineExpose({
    focus: () => input.value?.focus()
});
</script>

<template>
    <input
        ref="input"
        class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
        :value="modelValue"
        :required="isRequired"
        @input="$emit('update:modelValue', $event.target.value)"

    />
</template>

