<template>
    <template v-if="routeName">
        <a
            class="tw cursor-pointer inline-block rounded-lg px-2 py-1 text-sm text-layout-sun-700 hover:bg-primary-sun-300 hover:text-layout-sun-900 dark:text-layout-night-700 dark:hover:bg-primary-night-300 dark:hover:text-layout-night-900"
            :href="routeName"
        >
            <span>{{ name }}</span>
        </a>
    </template>
    <template v-else-if="href">
        <a
            class="tw cursor-pointer inline-block rounded-lg px-2 py-1 text-sm text-layout-sun-700 hover:bg-primary-sun-300 hover:text-layout-sun-900 dark:text-layout-night-700 dark:hover:bg-primary-night-300 dark:hover:text-layout-night-900"
            :href="href"
        >
            <span>{{ name }}</span>
        </a>
    </template>
    <template v-else>
        <div
            :style="{ color: linkColor }"
            class="ckoo myDiv    cursor-pointer inline-block rounded-lg px-2 py-1 text-sm text-layout-sun-700 hover:bg-primary-sun-300 hover:text-layout-sun-900 dark:text-layout-night-700 dark:hover:bg-primary-night-300 dark:hover:text-layout-night-900"
        >
            <slot></slot>
        </div>
    </template>
</template>

<script>
export default {
    name: "Shared_LinkFooter",
    //
    props: {
        routeName: {
            type: String,
            default: null,
        },
        name: {
            type: String,
            default: "Starter Eleven",
        },
        href:{
            type:String,
            default:'',
        },
    },
    daza(){
        return { linkColor: '' }
    },
    mounted() {
 // Erst prüfen, ob ein <a> auf der Seite existiert
    // const link = document.querySelector('a');
    // if (link) {
    //   const linkColor = getComputedStyle(link).color;
    //   this.$refs.myDiv.style.color = linkColor;
    // }

  }
};

  </script>

<style>
A.tw{
           color: rgb(209 213 219) !important;
    }
</style>
