// .vitepress/theme/index.ts
import DefaultTheme from 'vitepress/theme'
import { useData, inBrowser } from 'vitepress'
import { watchEffect } from 'vue'

import './custom.css'
export default {
    ...DefaultTheme,
     setup () {
        const { lang } = useData()
        watchEffect(() => {
            if (inBrowser) {
                const date = new Date()
                date.setMinutes(date.getMinutes() + 6000)
                const expires = date.toUTCString()

                document.cookie = `nf_lang=${lang.value}; expires=${expires}; path=/`
            }
        })
    },
}
