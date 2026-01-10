<template>
  <Layout>
    <MetaHeader title="8 MCSL Points wurden deinem Konto gutgeschrieben" />
    <div class="flex flex-col items-center justify-center h-screen space-y-4 mt-[-80px]">
      <VerifySVG color="#FFC900"></VerifySVG>
      <div class="text-center text-lg font-semibold">
        Du hast 8 MCSL Points gesammelt
      </div>
    </div>
  </Layout>
</template>

<script>
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
import VerifySVG from "@/Application/Components/Icons/verify.vue";
import Layout from "@/Application/Homepage/Shared/Layout.vue";
import { toastBus } from "@/utils/toastBus";
export default {
components:{
    VerifySVG,
    Layout,
    MetaHeader,
},
props:{
    i:[String,Number],
},
mounted()
{
    window.toastBus.emit( { status: 'points', message: 'Du hast 8 MCSL Points gesammelt'});
}
};

</script>

