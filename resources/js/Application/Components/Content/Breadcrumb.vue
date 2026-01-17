<template>
    <nav
        class="flex py-1 px-0.5 text-sm text-layout-sun-800 dark:text-layout-night-800"
        aria-label="Breadcrumb"
    >
        <ol
            class="inline-flex flex-col lg:flex-row items-start lg:items-center justify-start space-x-1 lg:space-x-1"
        >
            <li v-if="!startPage">
                <div class="flex items-center py-1.5">
                    <Link
                        :href="route(routeDashboard)"
                        class="ml-1 lg:ml-2 px-2 py-1.5 rounded-lg font-medium hover:bg-layout-sun-200 dark:hover:bg-layout-night-200"
                    >
                        <div class="flex items-center">
                            <icon-home
                                class="hidden lg:block -mt-0.5 w-3 h-3"
                            />
                            <div class="ml-1 font-medium lg:ml-2">
                                <b  class="text-white">{{ home }}</b>
                            </div>
                        </div>
                    </Link>
                </div>
            </li>
            <li v-if="startPage">
                <div class="flex items-center py-1.5">
                    <div class="ml-1 lg:ml-2 px-2 py-1.5 font-medium">
                        <div class="flex items-center">
                            <icon-home
                                class="hidden lg:block -mt-0.5 w-3 h-3"
                            />
                            <div class="ml-1 font-medium lg:ml-2">
                                <b  class="text-white">{{ home }}</b>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            <li v-for="(value, key, index) in breadcrumbs" :key="index">
                <div class="flex items-center py-1.5">
                    <icon-chevron-right
                        class="hidden lg:block -mt-0.5 w-3 h-3"
                    ></icon-chevron-right>
                    <Link
                        :href="value"
                        class="ml-1 lg:ml-2 px-2 py-1.5 rounded-lg font-medium hover:bg-layout-sun-200 dark:hover:bg-layout-night-200"
                        ><span class="!text-white text-layout-sun-600 dark:text-layout-night-900">{{ ucf(key) }}</span></Link
                    >
                </div>
            </li>
            <li aria-current="page" v-if="current">
                <div class="flex items-center py-1.5">
                    <icon-chevron-right
                        class="hidden lg:block -mt-0.5 w-3 h-3"
                    ></icon-chevron-right>
                    <div
                        class="px-2 font-medium text-layout-sun-600  text-white"
                    >
                        {{ ucf(current) }}
                    </div>
                </div>
            </li>
        </ol>
    </nav>
</template>
<script>
import { Link } from "@inertiajs/vue3";

import IconHome from "@/Application/Components/Icons/Home.vue";
import IconChevronRight from "@/Application/Components/Icons/ChevronRight.vue";

export default {
    name: "Components_Content_Breadcrumb",

    components: {
        Link,
        IconHome,
        IconChevronRight,
    },
    props: {
        home: {
            type: String,
            default: "Dashboard",
        },
        applicationName: {
            type: String,
            default: "Administrator-Anwendung",
        },
        current: {
            type: String,
            default: null,
        },
        breadcrumbs: {
            type: Object,
            default: () => ({}),
        },
        startPage: {
            type: Boolean,
            default: false,
        },
    },
    mounted() {
        console.log(this.breadcrumbs);
    },
    methods:{
        ucf(str) {
            if (!str || typeof str !== "string") {
                return "";
            }

            // Teilt den String an den Unterstrichen
            const arr = str.split("_");

            // Wandelt jedes Element des Arrays um, falls es mehr als ein Wort gibt
            const na = arr.map(
                (val) => val.charAt(0).toUpperCase() + val.slice(1)
            );

            // Setzt die Wörter mit einem Leerzeichen zusammen
            return na.join(" ");
        },
    },
    computed: {
        routeDashboard() {
            const appName = this.applicationName;
            const apps = this.$page.props.applications;
            //console.log(apps);
            if (appName === apps.app_central_name) {
                return "central.dashboard";
            } else if (appName === apps.app_admin_name) {
                return "admin.dashboard";
            } else if (appName === apps.app_employee_name) {
                return "employee.dashboard";
            } else {
                return "customer.dashboard";
            }
        },
    },
};
</script>

