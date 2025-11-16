<template>
    <div><pre>{{ formData.recipients }}</pre></div>
</template>
<script>
export default {
  name: "EmailPreview",
    props: {
    formData: [Array,Object],
  },
  async mounted(){
    // const startRec = this.formData.recipients.split(", ")[0];
    // alert(startRec);
  }
};
</script>

