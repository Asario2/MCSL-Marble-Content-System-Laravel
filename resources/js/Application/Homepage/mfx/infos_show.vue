    <template>
    <layout :header-url="$page.props.saas_url + '/'">
    <MetaHeader :title="'Infos: ' + data.headline" />
        <p v-if="!data">Kein Eintrag zu dieser ID vorhanden</p>

        <page-content v-else>
            <template #content>
                <div>
                <page-title>
                    <template #title>
                        <span class="dark:text-layout-night-1050 text-layout-sun-1000 inline">Infos - <span v-html="cleanHtml(data.headline)"></span> <editbtns :id="data?.id" table="infos" /></span>
                </template>
                </page-title>



                    <div class="min-h-[350px] pl-0 bg-layout-sun-50 dark:bg-layout-night-0 lg:rounded-lg p-4 border border-layout-sun-1000 dark:border-layout-night-1050 flex gap-4">

        <!-- Bild links (feste Breite 150px) -->
        <div class="w-[150px] shrink-0 items-center hidden md:flex">
        <img
            class="w-[150px] h-auto rounded"
            :src="'/images/_mfx/infos/img_big/' + data.img_big"
            :alt="data.headline"
            :title="data.headline"
        />
        </div>

        <!-- Text rechts -->
        <div class="flex-1 space-y-2">
        <!-- <div class="text-layout-sun-1000 dark:text-layout-night-1050 text-xl font-bold"></div> -->
        <div class="text-layout-sun-1000 dark:text-layout-night-1000 items-start p-2 mt-[-20px]" v-html="cleanHtml(data.message)"></div>

        </div>

    </div>
  </div>





            </template>
        </page-content>
    </layout>
</template>
<script>
import { defineComponent } from "vue";
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
import Layout from "@/Application/Homepage/Shared/mfx/Layout.vue";
import { marked } from 'marked';
import { selectionHelper, GetSettings, rumLaut, nl2br } from "@/helpers";
import PageContent from "@/Application/Components/Content/PageContent.vue";
import PageTitle from "@/Application/Components/Content/PageTitle.vue";
import PageParagraph from "@/Application/Components/Content/PageParagraph.vue";
import emailview from "@/Application/Components/Form/email.vue";
import editbtns from "@/Application/Components/Form/editbtns.vue";

export default defineComponent({
    name: "Homepage_Home",

    components: {
        Layout,
        PageContent,
        PageTitle,
        PageParagraph,
        emailview,
        editbtns, MetaHeader},
    props: {
        news: [Array, Object],
        text: [Array, Object],
        data: [Array, Object],
        privacy: String,
    },

    data() {
        return {
            form: {
                name: '',
                email: '',
                subject: '',
                message: '',
                captcha: '',
                accepted: false
            },
            voteHtml: '',
            changelogText: '',
            todolist: [],
        }
    },

    async mounted() {
        try {
            this.loadVotez();
            this.loadChangelog();
    if (this.privacy) {
        this.processedPrivacy = await this.processHtmlWithVcard(this.privacy);
    }

    if (this.data?.message) {
        this.processedHtml = await this.processHtmlWithVcard(this.data.message);
    }

    const url = 'https://api.github.com/search/issues?q=repo:Asario2/MCSL-based-on-Starter-Eleven+is:issue+state:open';
  const resp = await fetch(url);
  const data = await resp.json();

//     console.log('GitHub Issues:', data);
    this.todolist = data.items;
  } catch (error) {
    console.error('Fehler beim Laden der Issues:', error);
    this.todolist = [];
  }
  this.scrollToHashAnchor();
        window.addEventListener("hashchange", this.scrollToHashAnchor);
        },
        beforeUnmount() {
        window.removeEventListener("hashchange", this.scrollToHashAnchor);
        },
    methods: {
        ch(txt) {
            return txt
        // //    .replace("/<br />\n<br />\n<br />/gi","\n\n")
        //     .replace(/<br\s*\/?>/gi, "\n")     // alle Varianten von <br>, inkl. <br>, <br/> und <br />
        //     .replace(/<\/li>\s*(<br\s*\/?>)+/gi, '</li>')

            .replace(/\n/g, '<br />')       // alle übrigen \n in <br />
        // .replace(/\n/g, '<br />')               // \n in <br />
        // .replace(/(<br\s*\/?>\s*){3,}/gi, '<br />');
        .replace(/(<br\s*\/?>\s*){3,}/gi, '<br /><br />')

        },
     async processHtmlWithVcard(html) {
//         console.log("%c[processHtmlWithVcard] INPUT:", "color:cyan", html);

        let processed = this.ch(html);
//         console.log("%c[ch] →", "color:gray", processed);

        processed = this.decodeHtml(rumLaut(nl2br(processed)));
//         console.log("%c[decodeHtml+rumLaut] →", "color:gray", processed);

        if (processed.includes("{{ vcard }}")) {
//             console.log("%c[FOUND {{ vcard }}] ✔", "color:green;font-weight:bold");
        } else {
            console.warn("[WARN] {{ vcard }} wurde nicht gefunden in:", processed);
        }

        processed = processed.replace(/{{\s*vcard\s*}}/g, "__VCARD__");
//         console.log("%c[replace → __VCARD__]:", "color:orange", processed);

        processed = await this.replaceAsyncPlaceholders(processed);
//         console.log("%c[replaceAsyncPlaceholders] →", "color:purple", processed);

        return processed;
    },
        showtodo() {
        if (!Array.isArray(this.todolist) || this.todolist.length === 0) {
            return '<p>Keine offenen Issues gefunden.</p>';
        }

        let html = '';
        for (const issue of this.todolist) {
            html += `

                <a href="${issue.html_url}" target="_blank" class="text-blue-600 hover:underline">
                #${issue.number} - ${issue.title}
                </a>
                <span class="text-sm text-gray-500">(${issue.state})</span><br />
            `;
        }


        return html;
        },
        showprivacy(){
            // return "test";
            return this.ch(this.privacy);

        },
        repimg(txt) {
            txt = txt.replace('<img src="https://www.asario.de/_images/mcsl_changelog.jpg" alt="MCSL Changelog">', '');
            txt = txt.replace(/green/g, 'orange');
            txt = txt.replace(/<img\s+src=/g, "<img style='display:inline' src=");
            txt = this.linkit(txt);

            return txt;
        },

        linkit(str) {
            return str.replace(/(?<!&)#(\d+)/g, "<a href='https://github.com/Asario2/MCSL-based-on-Starter-Eleven/issues/$1'>#$1</a>");
        },

        async loadVotez() {
            try {
                const response = await axios.get('/api/getVotez');
                this.votes = response.data;
                let text = '';

                for (const [key, value] of Object.entries(this.votes)) {
                    text += `<b>${value.user}</b> schreibt:<br />${value.details}<br /><a href='https://${value.url}' target='_blank'>${value.url}</a><br /><br />`;
                }

                this.voteHtml = text;
            } catch (err) {
                console.error('Fehler beim Laden der Votes:', err);
            }
        },

        async loadChangelog() {
            try {
                const response = await fetch('https://raw.githubusercontent.com/Asario2/MCSL-based-on-Starter-Eleven/main/CHANGELOG.md');
                const markdown = await response.text();
                this.changelogText = this.repimg(marked(markdown)); // ✅ Markdown in HTML umwandeln
            } catch (err) {
                console.error('Fehler beim Laden des Changelogs:', err);
                this.changelogText = 'Changelog konnte nicht geladen werden.';
            }
        },

        cleanHtml(html) {
            if (!html) return '';

            let result = this.ch(html);
            // 1. HTML-Entities wie &lt; &gt; wieder zu < und > machen
            result = this.decodeHtml(rumLaut(nl2br(html)));

            // 2. Zeilenumbrüche (\n oder \r\n) in <br> konvertieren
            // result = result.replace(/(\r\n|\n|\r)/g, '<br />');


            // 3. Eigene Platzhalter-Funktionen {{...}} ersetzen
            result = this.parseMessage(result);

            return result;
        },

        replacements() {
            return {
                'votez()': () => rumLaut(nl2br(this.voteHtml)),
                'changelog()': () => this.changelogText,
                'showtodo()': () => this.showtodo(),
                'showprivacy()': () => this.showprivacy(),
                'vcard': () => this.showVCard(),
            };
        },
        async showVCard() {
            if (!this.vcardData) return '';

            const vnode = h(ContactCard, {
                data: this.vcardData,
                lang: {
                    phone: 'Telefon',
                    mobil: 'Mobil',
                    fax: 'Fax',
                    email: 'E-Mail'
                }
            });

            const html = await renderToString(vnode);

            return html;
        },
        async replaceAsyncPlaceholders(text) {
            if (text.includes('__VCARD__')) {
                const html = await this.showVCard();
                text = text.replace(/__VCARD__/g, html);
            }
            return text;
        },
        parseMessage(text) {
            return text.replace(/{{\s*(.*?)\s*}}/g, (match, key) => {
                const func = this.replacements()[key];
                return func ? func() : match;
            });
        },

        decodeHtml(html) {

    return html
        .replace(/&lt;/g, '<')
        .replace(/&gt;/g, '>')
        .replace(/&amp;/g, '&')
        .replace(/%5B/g, '[')
        .replace(/%5D/g, ']');

            },
  cleanListHtml(html) {
    return html
        // <br> vor <li> entfernen

    //   .replace(/<br\s*>\s*<li>/gi, '<li>')
    //   .replace(/<ul>\s*<br\s*>/gi, '<ul>')
    //   .replace(/<br\s*>\s*<\/ul>/gi, '</ul>');
        // doppelte <br><br> zu einem <br> reduzieren
        // .replace(/(<br\s*>\s*){2,}/gi, '<br>');
},


        async submitForm() {
            try {
                const response = await axios.post('/contact/send', this.form)
                alert('Nachricht erfolgreich gesendet!')
                this.resetForm()
            } catch (error) {
                alert('Fehler beim Senden der Nachricht.')
            }
        },

        resetForm() {
            this.form = {
                name: '',
                email: '',
                subject: '',
                message: '',
                captcha: '',
                accepted: false
            }
        },
        scrollToHashAnchor() {
    const hash = window.location.hash;
    if (hash && hash.startsWith("#")) {
      setTimeout(() => {
        const el = document.getElementById(hash.substring(1));
        if (el) {
          const y = el.getBoundingClientRect().top + window.pageYOffset - 134;
          window.scrollTo({ top: y, behavior: "smooth" });
        }
      }, 50); // etwas Delay, bis DOM fertig gerendert ist
    }
  }
    },
});
</script>

