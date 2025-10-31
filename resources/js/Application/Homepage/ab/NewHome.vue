    <template>
    <Layout>
        <MetaHeader title="Willkommen auf Asarios Blog" />
    <section class="bg-layout-sun-0 text-layout-sun-800 dark:bg-layout-night-0 dark:text-layout-night-800 p-4">
    <div
    id="arrow" style="display:none;"    
    class="absolute right-5 top-[calc(var(--nav-height,5.7rem)+0.5rem)] z-40 flex justify-center items-center hover:scale-110 transition-transform duration-300"
    >
    <JArrow />
    </div>

    <div class="block max-w-sm gap-3 p-5 lg:rounded gap-3 rounded mx-auto sm:max-w-full group hover:no-underline focus:no-underline bg-layout-sun-100 dark:bg-layout-night-100" itemscope itemtype="http://schema.org/Person">
        <img :src="'/images/_ab/web/about-me.jpg'" alt="Paul Rieser" class="w-[calc(100%+4rem)] mx-auto" />

    <h3 itemprop="headline">
        Willkommen auf Asarios Blog – wo Kunst, Fotografie und Webdesign verschmelzen.
    </h3>

    <p itemprop="description">
        Dieser Ort ist meine kreative Spielwiese: eine Sammlung von Bildern, Ideen und
        Designkonzepten, die zeigen, wie unterschiedlich Schönheit entstehen kann.<br /><br />
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
    </p><br/><br />
        Ich <span itemprop="name">Paul Rieser</span>,
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
    </section>

    </Layout>
    </template>

    <script>
    import Layout from "@/Application/Homepage/Shared/Layout.vue";
    import JArrow from "@/Application/Components/Icons/JArrow.vue";
    export default {
        name: 'NewHome',
        components:{ Layout,JArrow },
        methods: {
        redirect() {
            this.$inertia.visit('/blogs');
        }
    }

    };
    </script>
    <style>
    @media screen and (max-width: 1024px) {
    #arrow{
        display:inline-block !important;
    }
    }
    </style>
