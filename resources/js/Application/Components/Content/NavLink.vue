<template>
    <div v-if="routeName" class="flex items-center">
        <a
            :href="routeName"
            class="inline-block px-2 pt-1 text-sm cursor-pointer text-layout-sun-700 hover:bg-layout-sun-200 hover:text-layout-sun-900 dark:text-layout-night-700 dark:hover:bg-layout-night-200 dark:hover:text-layout-night-900"
            :class="classes"
            :target="target"
        >
        <span class="inline-flex items-center gap-1 text-layout-sun-600 dark:text-layout-night-900">
    <component
        v-if="icon"
        :is="icon"
        class="w-4 h-4"
    />
    <span> {{ label }}</span>
</span></a>

    </div>
    <div v-else class="flex items-center">
        <div
            class="inline-block px-2 pt-1 text-sm text-layout-sun-700 dark:text-layout-night-700"
            :class="classes"
        >
            <slot></slot>
        </div>
    </div>
</template>
<script>
import  IconStar  from '@/Application/Components/Icons/IconStar.vue';

export default {
    name: "Components_Content_NavLink",
    //
    props: {
        label: {
            type: String,
            default: null,
        },
        routeName: {
            type: String,
            default: null,
        },
        target: {
            type: String,
            default: null,
        },
        active: {
            type: Boolean,
            default: false,
        },
        icon:{
            type:String,
            default: '',
        }
    },
    components:{
        IconStar,
    },
    //
    computed: {
        classes() {
            if (this.active) {
                return "border-b-2 border-primary-sun-400 dark:border-primary-night-400 transition duration-150 ease-in-out";
            }
            return "border-b-2 border-transparent transition duration-150 ease-in-out rounded-lg ";
        },
    },
};
</script>
<style>
A.text-layout-sun-600 dark:text-layout-night-900{
    color:rgb(20,20,20) !important;
}
</style>

