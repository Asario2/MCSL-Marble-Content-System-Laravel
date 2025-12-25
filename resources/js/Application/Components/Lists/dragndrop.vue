<template>
    <draggable></draggable>
</template>
<script>
import Draggable from "vuedraggable";

export default {
    name:"DragNDrop",
        components: {
        Draggable,
    },
    props: {
        datbrows: {
            type: Array,
            required: true,
        },
    },
    methods: {
        updateOrder(event) {
            // Beispiel-Handler, der ausgelöst wird, wenn ein Element verschoben wurde
            //console.log("Neue Reihenfolge:", this.datarows);
            alert(event);
        },
    },
};
</script>

