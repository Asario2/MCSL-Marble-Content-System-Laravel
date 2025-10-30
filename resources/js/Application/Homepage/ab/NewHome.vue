<template>
    <Layout>
<div itemscope itemtype="http://schema.org/Person">
  <img :src="'/images/_ab/web/about-me.jpg'" alt="Paul Rieser" itemprop="image" /><br />

  <h3 itemprop="headline">
    Willkommen auf Asarios Blog – wo Kunst, Fotografie und Webdesign verschmelzen.
  </h3>

  <p itemprop="description">
    Dieser Ort ist meine kreative Spielwiese: eine Sammlung von Bildern, Ideen und
    Designkonzepten, die zeigen, wie unterschiedlich Schönheit entstehen kann.
  </p>

  <p>
    Als <span itemprop="jobTitle">Künstler</span> und
    <span itemprop="jobTitle">Webdesigner</span> liebe ich es, mit
    <span itemprop="knowsAbout">Licht</span>,
    <span itemprop="knowsAbout">Farbe</span> und
    <span itemprop="knowsAbout">Form</span> zu arbeiten – egal, ob auf Leinwand,
    in der Kamera oder im Code.
  </p>

  <p>
    Nimm dir Zeit, durch meine Werke zu stöbern und die
    <span itemprop="knowsAbout">Kunst hinter den Pixeln</span> zu entdecken.
  </p>
</div>
</Layout>
</template>

<script>
import Layout from "@/Application/Homepage/Shared/Layout.vue";
export default {
     name: 'NewHome',
    components:{ Layout },
    methods: {
    redirect() {
        this.$inertia.visit('/blogs');
    }
}

};
</script>
