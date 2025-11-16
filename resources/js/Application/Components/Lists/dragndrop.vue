<script>
import Draggable from "vuedraggable";

export default {
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

