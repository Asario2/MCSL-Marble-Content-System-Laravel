<template>
<template v-if="routeName">
    <a
        class="pad cursor-pointer inline-flex items-center gap-2 rounded-lg px-2 py-1 text-sm text-layout-sun-700 hover:bg-layout-sun-1050 hover:text-layout-sun-900 dark:text-layout-night-1050 dark:hover:bg-layout-night-200 dark:hover:text-layout-night-1000"
        :href="routeName"
    >
        <slot></slot>
        <span>{{ name }}</span>
    </a>
</template>
    <template v-else>
        <div
            class="cursor-pointer ckoo font-bold inline-block rounded-lg px-2 py-1 text-sm text-layout-sun-700 hover:bg-layout-sun-200 hover:text-layout-sun-900 dark:text-layout-night-1050 dark:hover:bg-layout-night-200 dark:hover:text-layout-night-1000"
        >
            <slot></slot>
        </div>
    </template>
</template>

<script>
//
export default {
    name: "Shared_LinkHeader",
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
