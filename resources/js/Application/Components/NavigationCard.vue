<template>
    <div>
        <div class="flex items-center h-12">
            <span
                v-if="withIcon"
                class="bg-primary-sun-500 dark:bg-primary-night-500 p-2 rounded-full"
            >
                <component
                    :is="dynamicIcon"
                    class="w-6 h-6"
                    color="#000"
                />
            </span>
            <h2
                class="ms-3 text-xl font-semibold text-layout-sun-900 dark:text-layout-night-900"
            >
                <a :href="routeName">{{ title }}</a>
            </h2>
        </div>
        <p
            class="px-2 mt-4 text-layout-sun-700 dark:text-layout-night-700 text-sm leading-relaxed h-16"
        >
            <slot name="description" />
        </p>
        <p class="mt-4 text-sm flex justify-between h-8">
            <a
                v-if="routeName && linkName"
                :href="routeName"
                :target="target"
                class="py-1.5 px-2 rounded-lg inline-flex items-center font-semibold text-primary-sun-700 dark:text-primary-night-700 hover:bg-layout-sun-200 dark:hover:bg-layout-night-200"
            >
                {{ linkName }}
                <icon-arrow-long-right
                    class="ms-1 w-5 h-5 fill-primary-sun-700 dark:fill-primary-night-700"
                ></icon-arrow-long-right>
            </a>
            <a
                v-if="routeName2 && linkName2"
                :href="routeName2"
                class="py-1.5 px-2 rounded-lg inline-flex items-center font-semibold text-primary-sun-700 dark:text-primary-night-700 hover:bg-layout-sun-200 dark:hover:bg-layout-night-200"
            >
                {{ linkName2 }}
                <icon-arrow-long-right
                    class="ms-1 w-5 h-5 fill-primary-sun-700 dark:fill-primary-night-700"
                ></icon-arrow-long-right>
            </a>
        </p>
    </div>
</template>

<script>
import IconArrowLongRight from "@/Application/Components/Icons/ArrowLongRight.vue";
import IconBook from "@/Application/Components/Icons/Book.vue";
import IconNews from "@/Application/Components/Icons/iconNews.vue";
import IconContacts from "@/Application/Components/Icons/IconContacts.vue";
import IconLost from "@/Application/Components/Icons/IconLost.vue";

import IconProjects from "@/Application/Components/Icons/IconProjects.vue";
import IconProjectsSheets from "@/Application/Components/Icons/IconProjectsSheets.vue";
import IconStar from "@/Application/Components/Icons/IconStar.vue";
import IconTexts from "@/Application/Components/Icons/IconTexts.vue";
import IconInfos from "@/Application/Components/Icons/IconInfos.vue";
import IconLock from "@/Application/Components/Icons/IconLock.vue";
import IconMail from "@/Application/Components/Icons/IconMail.vue";
import IconHyperlink from "@/Application/Components/Icons/IconHyperLink.vue";
import IconPictures from "@/Application/Components/Icons/IconPictures.vue";
import IconDB from "@/Application/Components/Icons/db.vue";
import IconLogs from "@/Application/Components/Icons/IconLogs.vue";
import IconStory from "@/Application/Components/Icons/IconStory.vue";
import IconDidyou from "@/Application/Components/Icons/IconDidyou.vue";
import IconComment_bl from "@/Application/Components/Icons/IconComment_bl.vue";

export default {
    name: "Components_NavigationCard",

    components: {
        IconArrowLongRight,
        IconBook,
        IconProjects,
        IconProjectsSheets,
        IconDB,
        IconMail,
        IconLost,
        IconLock,
        IconPictures,
        IconLogs,
        IconDidyou,
        IconStory,
        IconHyperlink,
        IconComment_bl,
        IconNews,
        IconTexts,
        IconInfos,
        IconStar,
        IconContacts,
    },

    props: {
        withIcon: {
            type: Boolean,
            default: false,
        },
        icon: {
            type: String,
            default: "IconBook",
        },
        title: {
            type: String,
            default: "Navigations-Box",
        },
        routeName: {
            type: String,
            default: "",
        },
        linkName: {
            type: String,
            default: "",
        },
        target: {
            type: String,
            default: "",
        },
        routeName2: {
            type: String,
            default: "",
        },
        linkName2: {
            type: String,
            default: "",
        },
    },
    //
    computed: {
        dynamicIcon() {
            const icons = {
                IconBook,
                IconDB,
                IconLock,
                IconPictures,
                IconLogs,
                IconDidyou,
                IconInfos,
                IconProjects,
                IconProjectsSheets,
                IconStory,
                IconComment_bl,
                IconNews,
                IconTexts,
                IconContacts,
                IconHyperlink,
                IconLost,
                IconStar,
                IconMail,
            };
            //
            return icons[this.icon];
        },
    },
};
</script>
