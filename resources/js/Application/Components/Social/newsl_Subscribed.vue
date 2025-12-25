<template>
    <MetaHeader title="Newsletter Anmeldung erfolgreich" />
  <Layout>
    <div class="flex flex-col items-center justify-center h-screen space-y-4 mt-[-80px]">
      <VerifySVG></VerifySVG>
      <div class="text-center text-lg font-semibold">
        Ihr Newsletter wurde aktiviert
      </div>
    </div>
  </Layout>
</template>

<script>
import VerifySVG from "@/Application/Components/Icons/verify.vue";
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
import Layout from "@/Application/Homepage/Shared/Layout.vue";
export default {
components:{
    VerifySVG,
    Layout,
    MetaHeader,
}
};

</script>

