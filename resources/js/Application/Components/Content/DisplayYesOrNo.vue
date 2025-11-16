<template>
    <span v-if="isNumber(value) && value > 1">Ja</span>
    <span v-else-if="value == true || value == 1 || value == '1'"> Ja</span>
    <span v-else>Nein</span>
</template>
<script>
export default {
    name: "Components_Content_DisplayYesOrNo",

    props: {
        value: {
            type: [Boolean, Number, String],
            default: false,
        },
    },

    methods: {
        isNumber(value) {
            return typeof value === "number";
        },
    },
};
</script>

