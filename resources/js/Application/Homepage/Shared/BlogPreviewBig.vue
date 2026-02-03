<template>


     <div id="teaser-img2" class="block max-w-sm gap-3 mx-auto  sm:max-w-full group hover:no-underline focus:no-underline lg:grid lg:grid-cols-12 bg-layout-sun-100 dark:bg-layout-night-100 overfl" style="z-index:0;margin-bottom:-0px;" :class="{ 'disable-link': isCommentActive }"
    >
        <!-- Das Bild des Blog-Posts -->
        <div class="blog-container mh_64 lg:col-span-7" style="padding:0px;margin:0px;">
            <Link
        :href="route('home.blog.show', blog.autoslug)" class="block text-layout-sun-600 dark:text-layout-night-900 twi">
            <figure>
        <img
            :src="`/images/_ab/blogs/image_path/${blog.url}`"
            style='min-width:300px;max-height:365px;width:100%;min-height:auto;'
            :alt="blog.name"


            :class="['object-cover w-full rounded lg:col-span-7 object-cover rounded biggie bg-layout-sun-100 dark:bg-layout-night-100', blog.madewithai ? 'ai-image-corner' : '']"

        />
      <figcaption class="sr-only">
        {{ blog.title }} {{ blog.sumary }}
      </figcaption>
    </figure>

        </Link>
        <div class="relative my7">
        <!-- Der AI-Button wird hier angezeigt -->
        <div v-if="blog.madewithai" class="ai-icon-wrapper">
            <AiButton :dma="dmaa" :big="true"></AiButton>
        </div>
    </div>
        </div>

        <div id="teaser-img" class="p-6 space-y-1 lg:col-span-5 pb-0">
  <Link :href="route('home.blog.show', blog.autoslug)" class="text-layout-sun-600 dark:text-layout-night-900 twi">
    <div class="flex items-center justify-between w-full" aria-hidden="true">
      <!-- Blog-Titel -->
      <h2
        class="text-xl font-semibold sm:text-2xl font-title group-hover:underline group-focus:underline"
      >
        {{ blog.title }}
      </h2>

      <!-- Blog-Kategorie -->
      <div
        v-if="blog.category_name"
        class="text-sm min-w-fit min-h-fit bg-primary-sun-500 text-primary-sun-900
               dark:bg-primary-night-500 dark:text-primary-night-900
               font-semibold px-2.5 py-0.5 rounded-lg whitespace-nowrap ml-4"
      >
        {{ blog.category_name }}
        </div>

        <span v-if="blog.xis_anzeige" class="mt-[-5px] ml-[-72px]">
            <br /><br />
                            <img :src="'/images/_ab/web/anzeige.png'" alt="Anzeige"/>
                        </span>
    </div>

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
            <div v-html="smilies(blog.summary)" class="pb-2"></div>

            <!-- Lesezeit anzeigen -->
            <div>
                <display-number class="text-xs text-layout-sun-600 dark:text-layout-night-600 py-2"
                :value="blog.reading_time"
                    :after-digits="0"
                    value-unit="Minuten Lesezeit"
                    value-single-unit="Minute Lesezeit"
                ></display-number>
            </div>
        </Link>
        <div class="mt-4">
            <socialButtons :postId="blog.id" :slug="blog.autoslug" :nostars="true" :big="true"/>
        </div>
        </div>
    </div>
</template>

<script>
import { Link } from "@inertiajs/vue3";
import DisplayDate from "@/Application/Components/Content/DisplayDate.vue";
import DisplayNumber from "@/Application/Components/Content/DisplayNumber.vue";
import AiButton from "@/Application/Components/Content/AiButton.vue";
import editbtns from "@/Application/Components/Form/editbtns.vue";
import SocialButtons from "@/Application/Components/Social/socialButtons.vue";
import { replaceSmilies } from '@/helpers';
export default {
    name: "Homepage_Shared_BlogPreviewBig",
    components: {
        Link,
        DisplayDate,
        DisplayNumber,
        AiButton,
        editbtns,
        SocialButtons,
    },
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

methods:{
    smilies(text){
                    return replaceSmilies(this.nl2br(text));
                },
            nl2br(text){
                return text?.replace(/\n/g,"<br />");
            },
}
};
</script>

<style scoped>
.ai-icon {
  display: block;
  line-height: 0;   /* Verhindert Whitespace */
  margin: 0;
  padding: 0;
}
A.text-layout-sun-600 dark:text-layout-night-900{
    color:rgb(20,20,20) !important;
}
.rl{
    display:block;
    margin-top:5px;

}
.relative {
  position: relative;
}
.ai-icon-wrapper {
  display: block;
  line-height: 0; /* Verhindert Text-basierte Lücke */
}

@media screen and (min-width: 1024px) {
.overfl{

    max-height:420px;
}
.ai-button-image {
  position: fixed;  /* Fixiere das Bild auf dem Bildschirm */
  bottom: 16px;     /* Abstand von der unteren Kante */
  right: 16px;      /* Abstand von der rechten Kante */
  z-index: 39;
  margin-bottom :236px;
}
}
@media screen and (min-width: 1024px) {
.biggie{
min-height:365px !important
}
}
.object-cover{

    width:auto;
}
img {
  display: block;
  margin: 0;
  padding: 0;
}

/* img {
  outline: 2px solid red;
} */
.blog-container img {
  display: block;
  margin: 0;
  padding: 0;
  line-height: 0;
  border: none;

}
.opi{
    display: none;
}
/* Hier kannst du zusätzliche Anpassungen vornehmen, falls nötig */
</style>

