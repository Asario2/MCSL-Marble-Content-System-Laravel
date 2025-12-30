<template>
  <Layout>
    <MetaHeader title="Wunsch wurde Übermittelt" />
    <div class="flex flex-col items-center justify-center h-screen space-y-4 mt-[-80px]">
      <VerifySVG></VerifySVG>
      <div class="text-center text-lg font-semibold">
        Mail ist raus, ich kümmere mich drum.
      </div>
    </div>
  </Layout>
</template>

<script>
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
import VerifySVG from "@/Application/Components/Icons/verify.vue";
import Layout from "@/Application/Homepage/Shared/Layout.vue";
export default {
components:{
    VerifySVG,
    Layout,
    MetaHeader,
},
props:{
    i:[String,Number],
}
};

</script>

