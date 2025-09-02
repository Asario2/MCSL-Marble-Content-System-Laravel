<template>
<MetaHeader :title="'Benutzer:  '+ data.author_name2" />
<back-btn url="/home/users">Benutzerliste</back-btn>
     <div id="teaser-img2" class="block max-w-sm gap-3 mx-auto mh_65 sm:max-w-full group hover:no-underline focus:no-underline lg:grid lg:grid-cols-12 bg-layout-sun-100 dark:bg-layout-night-100 overfl" style="z-index:0;margin-bottom:-0px;" :class="{ 'disable-link': isCommentActive }"
    >
        <!-- Das Bild des Blog-Posts -->
        <div class="blog-container mh_65 lg:col-span-7" style="">
            <Link
        :href="route('home.blog.show', blog.autoslug)" class="block">

        <img
            :src="`/images/blogs/${blog.url}`"
            :alt="blog.name"
            width="480"
            height="360"
            style="max-height:390px"
            :class="['object-cover w-full rounded lg:col-span-7 object-cover rounded bg-layout-sun-500 dark:bg-layout-night-500', blog.madewithai ? 'ai-image-corner' : '']"

        />

        </Link>
        <div class="relative">
        <!-- Der AI-Button wird hier angezeigt -->
        <div v-if="blog.madewithai">
            <AiButton :dma="dmaa" :big="true"></AiButton>
        </div>
    </div>
        </div>

        <div id="teaser-img" class="p-6 space-y-2 lg:col-span-5 overfl" style="">
            <Link
        :href="route('home.blog.show', blog.autoslug)">
            <!-- Blog-Kategorie -->
            <div class="flex justify-end items-start">
                <div
                    v-if="blog.category_name"
                    class="text-sm min-w-fit min-h-fit bg-primary-sun-500 text-primary-sun-900 dark:bg-primary-night-500 dark:text-primary-night-900 font-semibold px-2.5 py-0.5 rounded-lg whitespace-nowrap"
                >
                    {{ blog.category_name }}
                </div>
            </div>

            <!-- Blog-Titel -->
            <h2
                class="text-xl font-semibold sm:text-2xl font-title group-hover:underline group-focus:underline"
            >
                {{ blog.title }}
            </h2>

            <!-- Datum und Autor -->
            <div class="flex justify-between items-center">
                <div class="text-xs text-layout-sun-600 dark:text-layout-night-600 py-2">
                    <display-date :value="blog.blog_date" :time-on="false" />
                    von
                    <span>{{ blog.author_name }}</span>
                </div>
                <div class="rl">
                    <editbtns :id="blog.id" table="blogs"></editbtns>
                </div>
            </div>


            <!-- Blog-Zusammenfassung -->
            <div v-html="blog.summary" class="pb-6"></div>

            <!-- Lesezeit anzeigen -->
            <div>
                <display-number
                :value="blog.reading_time"
                    :after-digits="0"
                    value-unit="Minuten Lesezeit"
                    value-single-unit="Minute Lesezeit"
                ></display-number>
            </div>
        </Link>
            <socialButtons :postId="blog.id" :slug="blog.autoslug" :nostars="true" />
        </div>
    </div>
</template>

<script>
import { Link } from "@inertiajs/vue3";
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";

import DisplayDate from "@/Application/Components/Content/DisplayDate.vue";
import DisplayNumber from "@/Application/Components/Content/DisplayNumber.vue";
import AiButton from "@/Application/Components/Content/AiButton.vue";
import editbtns from "@/Application/Components/Form/editbtns.vue";
import SocialButtons from "@/Application/Components/Social/socialButtons.vue";
import BackBtn from "@/Application/Components/Form/BackBtn.vue";
export default {
    name: "Homepage_Shared_BlogPreviewBig",
    components: {
        Link,
        BackBtn,
        DisplayDate,
        DisplayNumber,
        AiButton,
        editbtns,
        SocialButtons, MetaHeader},
    props: {
        blog: {
            type: Object,
        },
        aiOverlayImage: {
            type: String,
        },
        tablename:{
            type: String,
        },
        // editRights:{
        //     type: Number,
        // },
        // deleteRights:{
        //     type: Number,
        // },
    },
};
</script>

<style scoped>
.rl{
    display:inline;
    margin-top:-5px;

}
.relative {
  position: relative;
}


@media screen and (min-width: 1024px) {
.overfl{
    overflow:hidden;
    max-height:385px;
}
.ai-button-image {
  position: fixed;  /* Fixiere das Bild auf dem Bildschirm */
  bottom: 16px;     /* Abstand von der unteren Kante */
  right: 16px;      /* Abstand von der rechten Kante */
  z-index: 39;
  margin-bottom :236px;
}
}
@media screen and (max-width: 1024px) {
.overfl{
    overflow:visible;
}
}
/* Hier kannst du zusätzliche Anpassungen vornehmen, falls nötig */
</style>
