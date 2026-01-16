import { defineConfig } from 'vitepress'

// https://vitepress.dev/reference/site-config
export default defineConfig({
    head: [['link', { rel: 'icon', href: '/favicon.ico' }]],
    title: "Starter Eleven",
    description: "Kickstarte Deine SaaS-Anwendungen",
    lang: 'de',
    outDir: './../public/handbook_admin/',
    base: '/handbook_admin/',
    themeConfig: {
        // https://vitepress.dev/reference/default-theme-config
        logo: '/ReinkingLogo.png',
        //
        search: {
            provider: 'local'
        },
        //
        nav: [
            { text: 'Home', link: '/' },
            { text: 'Einführung', link: '/introduction' }
        ],
        //
        sidebar: [
            {
                text: 'Übersicht',
                items: [
                    { text: 'Einführung', link: '/introduction' },
                    { text: 'Buch', link: '/book' }
                ]
            }
        ],
        //
        outline: [2, 4],
        //
        outlineTitle: 'Auf dieser Seite',
        //
        socialLinks: [
            { icon: 'github', link: 'https://oliverreinking.github.io' }
        ],
        //
        docFooter: {
            prev: 'Zurück',
            next: 'Weiter'
        },
    }
})
