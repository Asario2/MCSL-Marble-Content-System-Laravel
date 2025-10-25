<template>
<Layout>
  <div itemscope itemtype="http://schema.org/Person">
    <img :src="'/images/_ab/web/about-me.jpg'" alt="Paul Rieser" /><br />

    Hi, ich bin <span itemprop="name">Paul Rieser</span>,
    arbeite seit 1998 als <span itemprop="jobTitle">Webdesigner</span> und liebe es,
    spannende Projekte umzusetzen. Mein größtes Abenteuer bisher war
    <a href="https://www.asario.de" itemprop="url">www.asario.de</a> bei
    <span itemprop="affiliation">MarbleFX</span>.<br><br />

    Neben dem Webdesign bin ich als <span itemprop="alternateName">Asario</span> auch
    künstlerisch unterwegs – Kunst, digitale Fotografie und Poesie gehören dazu.<br><br />

    <strong>Code & Chaos mit System:</strong> Ich tippe täglich HTML, CSS, JavaScript,
    Vue.js, PHP/Laravel und jongliere mit Datenbanken. TailwindCSS, Ziggy oder Inertia?
    Kein Problem – das ist mein Werkzeugkasten für schöne, funktionale Seiten.<br><br />

    Hier gibt’s fast alles von mir:
    <a href="home/pictures" itemprop="hasOccupation">Bilder</a> und
    <a href="home/shortpoems" itemprop="hasOccupation">Gedichte</a>.<br><br />

    <div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
      Ich lebe zurzeit in <span itemprop="addressLocality">Bimöhlen</span>,
      <span itemprop="addressCountry">SH</span>.<br><br />
    </div>

    Schreib mir: <span itemprop="email"><a href="mailto:paul.rieser@marblefx.net">paul.rieser[at]marblefx.net</a></span> oder
    ruf an: <span itemprop="telephone"><a href='tel:+49 162 7716325'>+49 162 7716325</a></span><br /><br />

    <!-- Persönliche Infos -->
    <div itemprop="url" itemscope itemtype="http://schema.org/WebPage">
      Mehr über mich bei <a href="/home/aboutme" itemprop="mainEntityOfPage">About Me</a>,<br /><br />
      dort erfährst du, welche Musik bei mir läuft, wenn ich im Flow bin,
      dass ich als Hobbys Zeichnen, Malen und digitale Fotografie habe und
      Außerdem bin ich ein Freund von klaren Strukturen im Code und einem guten Scherz zwischendurch.
    </div><br /><br />

    <div class="flex justify-center mt-4">
      <button
        class="flex items-center gap-2 px-3 py-2 rounded-lg font-semibold bg-blue-500 text-white
               hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 dark:text-white
               text-center tog-tab transition-all"
        @click="redirect"
      >
        Zu meinem Blog
      </button>
    </div>
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
