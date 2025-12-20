<template>
    <Layout>
        <MetaHeader title="Willkommen auf Asarios Blog"></MetaHeader>

        <section class="bg-layout-sun-0 text-layout-sun-800 dark:bg-layout-night-0 dark:text-layout-night-800 p-4">
            <div
                id="arrow"
                style="display:none;"
                class="absolute right-5 top-[calc(var(--nav-height,5.7rem)+0.5rem)] z-40 flex justify-center items-center hover:scale-110 transition-transform duration-300"
            >
                <JArrow />
            </div>

            <div class="block max-w-sm gap-3 p-5 lg:rounded mx-auto sm:max-w-full group hover:no-underline focus:no-underline bg-layout-sun-100 dark:bg-layout-night-100"
                 itemscope itemtype="http://schema.org/Person">

                <img :src="'/images/_ab/web/about-me.jpg'" alt="Paul Rieser" class="w-[calc(100%+4rem)] mx-auto" /><br />

                <h3 itemprop="headline">
                    Hallo! Willkommen auf Asarios Blog – mein kleiner Spielplatz für Kunst, Fotografie und Webdesign. 🎨💻
                </h3><br />

                <p itemprop="description">
                    Hier sammle ich alles, was mich inspiriert: Bilder, Ideen und Designkonzepte, die zeigen, dass Schönheit gerne mal anders aussieht, als man denkt. ✨<br />
                </p><br />

                <p>
                    Ich bin <span itemprop="jobTitle">Künstler</span> und <span itemprop="jobTitle">Webdeveloper</span> – ich liebe es, mit <span itemprop="knowsAbout">Licht</span>, <span itemprop="knowsAbout">Farbe</span> und <span itemprop="knowsAbout">Form</span> zu spielen, egal ob auf Leinwand, durch die Kamera oder mitten im Code.<br />
                </p><br />

                <p>
                    Schnapp dir einen Kaffee ☕, stöbere durch meine Werke und entdecke die <span itemprop="knowsAbout">Kunst hinter den Pixeln</span>.<br />
                </p><br />

                <p>
                    Ich, <span itemprop="name">Paul Rieser</span>, baue seit 1998 an Webseiten herum und liebe es, spannende Projekte umzusetzen.<br />
                    Mein bisher größtes Abenteuer: <a class='as' href="https://www.asario.de" itemprop="url">www.asario.de</a> bei <span itemprop="affiliation"><a href='https://www.marblefx.net'>MarbleFX</a></span>.<br />
                </p><br />

                <p>
                    Neben dem Webdesign bin ich als <span itemprop="alternateName">Asario</span> auch künstlerisch unterwegs – Kunst, digitale Fotografie und Poesie gehören dazu. ✏️📷<br />
                </p><br />

                <p>
                    <strong>Code & Chaos mit System:</strong> HTML, CSS, JS, Vue.js, PHP/Laravel – ich tippe, jongliere mit Datenbanken und habe TailwindCSS, Ziggy und Inertia stets im Werkzeugkasten.<br />
                    Alles für funktionale, aber schöne Seiten – und mit einem Augenzwinkern 😉<br />
                </p><br />

                <p>
                    Hier gibt’s fast alles von mir: <a class='as' href="home/pictures" itemprop="hasOccupation">Bilder</a> & <a class='as' href="home/shortpoems" itemprop="hasOccupation">Gedichte</a>.<br />
                </p><br />

                <div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
                    Ich lebe aktuell in <span itemprop="addressLocality">Bimöhlen</span>, <span itemprop="addressCountry">SH</span>.<br />
                </div><br />

                <p>
                    Schreib mir: <span itemprop="email"><a class='as' href="mailto:paul.rieser@marblefx.net">paul.rieser[at]marblefx.net</a></span>
                    oder ruf an: <span itemprop="telephone"><a class='as' href='tel:+49 162 7716325'>+49 162 7716325</a></span> 📞<br />
                </p><br />

                <div itemprop="url" itemscope itemtype="http://schema.org/WebPage">
                    Mehr über mich bei <a class='as' href="/home/aboutme" itemprop="mainEntityOfPage">About Me</a>.<br />
                    Dort erfährst du, welche Musik mich in den Flow bringt, welche Hobbys ich habe (Zeichnen, Malen, Fotografie) und dass ich klare Strukturen im Code genauso liebe wie einen guten Scherz zwischendurch. 😎<br />
                </div><br />

                <div class="flex justify-center mt-4">
                    <button
                        class="flex items-center gap-2 px-3 py-2 rounded-lg font-semibold bg-blue-500 text-white hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 dark:text-white text-center tog-tab transition-all"
                        @click="redirect"
                    >
                        Zu meinem Blog
                    </button><br />
                </div>
            </div>
        </section>
    </Layout>
</template>

<script>
import Layout from "@/Application/Homepage/Shared/Layout.vue";
import JArrow from "@/Application/Components/Icons/JArrow.vue";
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";

export default {
    name: 'NewHome',
    components: { Layout, JArrow, MetaHeader },

    methods: {
         redirect() {
            this.$inertia.visit('/blogs');
        },

    // andere Methoden hier...
  }
};
</script>

<style>
@media screen and (max-width: 1024px) {
    #arrow {
        display:inline-block !important;
    }
}
</style>

