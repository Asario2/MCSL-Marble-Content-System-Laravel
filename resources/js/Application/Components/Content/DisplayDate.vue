<template>
    <span>{{ displaydate }}</span>
</template>
<script>
export default {
    name: "Components_Content_Date",

    props: {
        value: {
            type: [String, Number],
            default: "-",
        },
        timeOn: {
            type: Boolean,
            default: false,
        },
    },
    //
    computed: {
        displaydate() {
            if (
                this.value == null ||
                this.value == undefined ||
                this.value == 0
            ) {
                return "-";
            }
            var convertDate = new Date(this.value);
            var year = convertDate.getFullYear();
            //
            if (year <= 1900) {
                return "-";
            }
            let minutes = String(convertDate.getMinutes()).padStart(2, "0");
            let hours = String(convertDate.getHours()).padStart(2, "0");
            let dd = String(convertDate.getDate()).padStart(2, "0");
            let mm = String(convertDate.getMonth() + 1).padStart(2, "0"); //Januar is 0!
            let yyyy = convertDate.getFullYear();
            //
            let formatDate = dd + "." + mm + "." + yyyy;
            if (this.timeOn) {
                formatDate =
                    dd + "." + mm + "." + yyyy + " " + hours + ":" + minutes;
            }
            //
            return formatDate;
        },
    },
};
</script>

