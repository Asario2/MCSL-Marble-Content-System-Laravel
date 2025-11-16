<template>
    <div v-if="GetSd() === 'ab'">
        <Link :href="routeName" class="flex items-center">
            <div>
                <favicon class="h-10 w-10 mr-4"></favicon>
            </div>
            <div
                class="hidden lg:block flex-col items-start justify-center md:flex"
            >
                <div
                    class="font-logo text-xl lg:text-3xl leading-snug whitespace-nowrap tracking-widest font-bold"
                >
                    <span
                        v-if="brand_1"
                        class="text-primary-sun-500 dark:text-primary-night-500"
                        >{{ brand_1 }}</span
                    >
                    <span
                        v-if="brand_2"
                        class="text-layout-sun-500 dark:text-layout-night-500"
                        >{{ brand_2 }}</span
                    >
                </div>
                <div
                    class="font-title text-xs lg:text-sm leading-snug whitespace-nowrap tracking-wide text-layout-sun-600 dark:text-layout-night-900"
                    v-if="appName"
                >
                    <span v-html="appName"></span>
                </div>
            </div>
        </Link>
    </div>
    <div v-else-if="GetSd() == 'mfx'">
    <a href="/admin/dashboard"><mfxlogo :ab="'_mfx' + this.GetLogin()"></mfxlogo></a>
    </div>
</template>

<script>
import { Link } from "@inertiajs/vue3";

import Favicon from "@/Application/Components/Logo/Favicon.vue";
import mfxlogo from "@/Application/Shared/mfxlogo.vue";
export default {
    name: "Shared_BrandHeader",
    //
    components: {
        Link,
        Favicon,
        mfxlogo,
    },
    //
    props: {
        routeName: {
            type: String,
            default: "home",
        },
        brand_1: {
            type: String,
            default: "Asarios",
        },
        brand_2: {
            type: String,
            default: "Blog",
        },
        appName: {
            type: String,
            default: null,
        },
    },
    mounted() {

  },
    methods:{
    GetSd(){
        return window.subdomain;
    },
    GetLogin()
    {
        const url = location.href;
        if(!url.includes("/login") && !url.includes("/forgot-password") && !url.includes("/register") && !url.includes("/email/verify") && !url.includes("reset-password") && !url.includes("/confirm-password") && !url.includes("/verify-email"))
        {
            return "";
        }
        return "l";
    }

}
};

</script>

