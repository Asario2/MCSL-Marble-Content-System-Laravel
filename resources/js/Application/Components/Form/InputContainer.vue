<template>
    <div class="mb-1" :class="[fullWidth ? 'lg:col-span-2 ' : '']">
        <slot></slot>
    </div>
</template>
<script>
export default {
    name: "Contents_Form_InputContainer",

    props: {
        fullWidth: {
            type: Boolean,
            required: false,
        },
    },
};
</script>

