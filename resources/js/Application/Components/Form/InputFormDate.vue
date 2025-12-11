<template>
    <div class="relative mb-4 z-0">
    <label :for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
    <slot name="label"></slot>
    </label>

    <input
        type="date"
        :id="name"
        :name="name"
        step="1"
        :disabled="disabled"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        :class="[
            'w-full w-fully p-2.5 pr-10 text-sm rounded-lg block border focus:ring-3 focus:ring-opacity-75',
            'bg-layout-sun-0 text-layout-sun-900 border-primary-sun-500 focus:border-primary-sun-500',
            'focus:ring-primary-sun-500 placeholder:text-layout-sun-400',
            'selection:bg-layout-sun-200 selection:text-layout-sun-1000',
            'dark:bg-layout-night-0 dark:text-layout-night-900 dark:border-primary-night-500',
            'dark:focus:border-primary-night-500 dark:focus:ring-primary-night-500',
            'placeholder:dark:text-layout-night-400 dark:selection:bg-layout-night-200',
            'dark:selection:text-layout-night-1000',
            $attrs.class
            ]"
            v-bind="$attrs"
        />

    <!-- Hidden Field für Formular -->
    <input type="hidden" :id="name + '_alt'" :value="modelValue" />

    <!-- Dark mode icon -->
    <IconCal v-if="!disabled" class="
        pointer-events-none absolute top-10 left right-3 h-5 w-5 hidden dark:block"
        alt="calendar icon (white)"
    ></IconCal>
    </div>
</template>

<script>
import IconCal from "@/Application/Components/Icons/IconCal.vue"; // Assuming this is the correct import
export default {
    components: { IconCal},
    name: "InputFormDateTime",
    props: {
        id: {
            type: String,
            required: true,
        },
        name: {
            type: String,
            required: true,
        },
        modelValue: { type: String, default: '' },
        placeholder: {
            type: String,
            default: "",
        },
        disabled: {
        type: [String, Array, Object],
        default: '',
        },
    },
    emits: ['update:modelValue'],
    watch: {
        modelValue(newVal) {
            const hidden = document.getElementById(this.name + "_alt");
            if (hidden) hidden.value = newVal;
        }
    }

};
</script>

<style scoped>
.w-fully{
    min-width:94%;
    max-width:94%;
}
.left{
top:30px;
right:74px;
color:white;

}
</style>
