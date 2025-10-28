<template>
  <Layout>
    <div class="flex flex-col items-center justify-center h-screen space-y-4 mt-[-80px]" v-if="i > 0">
      <VerifySVG></VerifySVG>
      <div class="text-center text-lg font-semibold">
        {{ i }} E-Mails erfolgreich versendet<br />
       <br />
        <br />
        <a href="/admin/email">Zurück zum Formular</a>
      </div>
    </div>
       <div class="flex flex-col items-center justify-center h-screen space-y-4 mt-[-80px]" v-else>
      <ErrorSVG></ErrorSVG>
      <div class="text-center text-lg font-semibold">
       Kein Empfänger gefunden<br />
       <br />
        <br />
        <a href="/admin/email">Zurück zum Formular</a>

      </div>
    </div>
  </Layout>
</template>

<script>
import VerifySVG from "@/Application/Components/Icons/verify.vue";
import ErrorSVG from "@/Application/Components/Icons/ErrorSVG.vue";
import Layout from "@/Application/Homepage/Shared/Layout.vue";
export default {
components:{
    VerifySVG,
    ErrorSVG,
    Layout,
},
props:{
    i:[String,Number],
}
};

</script>
