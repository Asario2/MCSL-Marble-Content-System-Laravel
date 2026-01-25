<template>
<template v-if="routeName">
    <a
        class="pad cursor-pointer inline-flex items-center gap-2 rounded-lg px-4 py-2 text-layout-sun-700 hover:bg-layout-sun-200 hover:text-layout-sun-900 dark:text-layout-night-1050 dark:hover:bg-layout-night-200 dark:hover:text-layout-night-1000"
        :href="routeName"
        style="font-family:Alfredo;"
    >
        <slot></slot>
        <span>{{ name }}</span>
    </a>
</template>
    <template v-else>
        <div
            class="cursor-pointer inline-flex items-center gap-2 rounded-lg px-3 py-3 text-layout-sun-700 hover:bg-layout-sun-200 hover:text-layout-sun-900 dark:text-layout-night-1050 dark:hover:bg-layout-night-200 dark:hover:text-layout-night-1000"
            style="font-family:Alfredo;"
        >
            <slot></slot>
        </div>
    </template>
</template>

<script>
//
export default {
    name: "LinkHeader_mfx",
    //
    props: {
        routeName: {
            type: String,
            default: "home",
        },
        name: {
            type: String,
            default: "Starter Eleven",
        },
        imgsrc:{
            type:String,
            default:'',
        },
        ialt:{
            type:String,
            default:'',
        },
        img:{
            type:String,
            default:'',
        },
    },
};
</script>

