<template>
    <div :class="mode" id="app-layout-start">
        <Head :title="title"></Head>

        <div
            class="min-h-screen bg-layout-sun-100 text-layout-sun-800 dark:bg-layout-night-100 dark:text-layout-night-800 transition duration-2000 ease-in-out"
        >
            <!-- Header Content -->
            <nav
                class="py-2 bg-layout-sun-0 text-layout-sun-800 dark:bg-layout-night-0 dark:text-layout-night-800 border-b border-layout-sun-200 dark:border-layout-night-200"
            >
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <brand-header
                                    :route-name="route('admin.dashboard')"
                                    :brand_1="
                                        $page.props.applications.brand_name_1
                                    "
                                    :brand_2="
                                        $page.props.applications.brand_name_2
                                    "
                                    :app-name="
                                        $page.props.applications.app_admin_name
                                    "
                                ></brand-header>
                            </div>

                            <!-- Navigation Links -->
                            <div
                                class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"
                            >
                                <NavLink
                                    :routeName="route('admin.dashboard')"
                                    :active="route().current('admin.dashboard')"
                                    label="Dashboard"
                                >
                                </NavLink>
                                <NavLink
                                    :routeName="route('admin.handbook')"
                                    label="Handbuch"
                                    target="_blank"
                                >
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <div class="mr-1">
                                <button-change-mode
                                    :mode="mode"
                                    @changeMode="changeMode"
                                ></button-change-mode>
                            </div>
                            <!-- Settings Dropdown -->
                            <div class="ms-3 relative">
                                <Dropdown align="right" width="72">
                                    <template #trigger>
                                        <button
                                            v-if="
                                                $page.props.jetstream
                                                    .managesProfilePhotos
                                            "
                                            class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-layout-sun-300 dark:focus:border-layout-night-300 transition"
                                        >
                                            <img
                                                class="h-8 w-8 rounded-full object-cover"
                                                :src="
                                                    $page.props.userdata
                                                        .profile_photo_url
                                                "
                                                :alt="
                                                    $page.props.userdata
                                                        .full_name
                                                "
                                            />
                                        </button>

                                        <span
                                            v-else
                                            class="inline-flex rounded-md"
                                        >
                                            <button
                                                type="button"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-layout-sun-500 dark:text-layout-night-500 bg-layout-sun-0 dark:bg-layout-night-0 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150"
                                            >
                                                {{
                                                    $page.props.userdata
                                                        .full_name
                                                }}

                                                <svg
                                                    class="ms-2 -me-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke-width="1.5"
                                                    stroke="currentColor"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M19.5 8.25l-7.5 7.5-7.5-7.5"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <!-- Anwendung wechseln bzw. zur Startseite -->
                                        <div
                                            class="block px-4 py-2 text-xs text-layout-sun-500 dark:text-layout-night-500"
                                        >
                                            <span
                                                v-if="
                                                    $page.props.userdata
                                                        .application_count > 1
                                                "
                                                >Anwendung wechseln</span
                                            >
                                            <span v-else>Startseite</span>
                                        </div>
                                        <dropdown-link
                                            :with-icon="false"
                                            :with-route="true"
                                            :route-name="
                                                route('central.dashboard')
                                            "
                                        >
                                            <span
                                                v-if="
                                                    $page.props.userdata
                                                        .application_count > 1
                                                "
                                                >Anwendung wechseln</span
                                            >
                                            <span v-else>zur Startseite</span>
                                        </dropdown-link>

                                        <!-- Account Management -->
                                        <div
                                            class="block px-4 py-2 text-xs text-layout-sun-500 dark:text-layout-night-500"
                                        >
                                            Dein Konto
                                        </div>

                                        <dropdown-link
                                            :with-icon="false"
                                            :with-route="true"
                                            :route-name="route('admin.profile')"
                                        >
                                            Profil
                                        </dropdown-link>

                                        <dropdown-link
                                            v-if="
                                                $page.props.jetstream
                                                    .hasApiFeatures
                                            "
                                            :with-icon="false"
                                            :with-route="true"
                                            :route-name="
                                                route('admin.api_tokens.index')
                                            "
                                        >
                                            API-Token
                                        </dropdown-link>

                                        <div
                                            class="my-2 border-t border-layout-sun-200 dark:border-layout-night-200"
                                        />

                                        <!-- Authentication -->
                                        <form @submit.prevent="logoutUser">
                                            <button type="submit">
                                                <dropdown-link>
                                                    Abmelden
                                                </dropdown-link>
                                            </button>
                                        </form>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                class="button_menu"
                                v-on:click="toggleNavbar()"
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: isOpen,
                                            'inline-flex': !isOpen,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !isOpen,
                                            'inline-flex': isOpen,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{
                        block: isOpen,
                        hidden: !isOpen,
                    }"
                    class="sm:hidden"
                >
                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink
                            :href="route('admin.dashboard')"
                            :active="route().current('admin.dashboard')"
                        >
                            Dashboard
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('admin.handbook')" target="_blank">
                            Handbuch
                        </ResponsiveNavLink>
                        <ResponsiveNavLink as="button">
                            <button-change-mode
                                :mode="mode"
                                @changeMode="changeMode"
                            ></button-change-mode>
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div
                        class="pt-4 pb-1 border-t border-layout-sun-200 dark:border-layout-night-200"
                    >
                        <div class="flex items-center px-4">
                            <div
                                v-if="
                                    $page.props.jetstream.managesProfilePhotos
                                "
                                class="shrink-0 me-3"
                            >
                                <img
                                    class="h-10 w-10 rounded-full object-cover"
                                    :src="
                                        $page.props.auth.user.profile_photo_url
                                    "
                                    :alt="$page.props.userdata.full_name"
                                />
                            </div>

                            <div>
                                <div
                                    class="font-medium text-base text-gray-800 dark:text-gray-200"
                                >
                                    {{ $page.props.auth.user.first_name }}
                                    {{ $page.props.auth.user.last_name }}
                                </div>
                                <div class="font-medium text-sm text-gray-500">
                                    {{ $page.props.auth.user.email }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink
                                :href="route('admin.profile')"
                                :active="route().current('admin.profile')"
                            >
                                Profil
                            </ResponsiveNavLink>

                            <ResponsiveNavLink
                                v-if="$page.props.jetstream.hasApiFeatures"
                                :href="route('admin.api_tokens.index')"
                                :active="
                                    route().current('admin.api_tokens.index')
                                "
                            >
                                API-Token
                            </ResponsiveNavLink>

                            <!-- Authentication -->
                            <form method="POST" @submit.prevent="logoutUser">
                                <ResponsiveNavLink as="button">
                                    Abmelden
                                </ResponsiveNavLink>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header
                v-if="$slots.header"
                class="bg-layout-sun-0 text-layout-sun-800 dark:bg-layout-night-0 dark:text-layout-night-800 mb-4 border-b border-layout-sun-100 dark:border-layout-night-100"
            >
                <div class="max-w-7xl mx-auto py-0 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <div class="min-h-screen">
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        <!-- Toast -->
                        <div>
                            <toast></toast>
                        </div>

                        <!-- Slot für Content -->
                        <div class="mt-4">
                            <slot></slot>
                        </div>
                    </div>
                </div>
            </main>

            <!-- Page Footer-->
            <footer
                class="mt-12 pt-12 border border-layout-sun-200 dark:border-layout-night-200 bg-layout-sun-0 text-layout-sun-800 dark:bg-layout-night-0 dark:text-layout-night-800 shadow"
            >
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <!-- Grid -->
                    <FooterGrid />
                    <!-- End Grid -->
                </div>
            </footer>
        </div>
    </div>
</template>

<script>
import { Head } from "@inertiajs/vue3";

import BrandHeader from "@/Application/Shared/BrandHeader.vue";

import Toast from "@/Application/Components/Content/Toast.vue";
import ButtonChangeMode from "@/Application/Components/ButtonChangeMode.vue";

import Dropdown from "@/Application/Components/Content/Dropdown.vue";
import DropdownLink from "@/Application/Components/Content/DropdownLink.vue";
import NavLink from "@/Application/Components/Content/NavLink.vue";
import ResponsiveNavLink from "@/Application/Components/Content/ResponsiveNavLink.vue";

import FooterGrid from "@/Application/Components/Content/FooterGrid.vue";

export default {
    name: "Admin_Shared_Layout",

    components: {
        Head,
        BrandHeader,
        Toast,
        ButtonChangeMode,
        Dropdown,
        DropdownLink,
        NavLink,
        ResponsiveNavLink,
        FooterGrid,
    },

    props: {
        title: {
            type: String,
            default: "Administrator-Anwendung",
        },
    },

    data() {
        return {
            mode: localStorage.theme ? localStorage.theme : "",
            isOpen: false,
            year: new Date().getFullYear(),
        };
    },

    methods: {
        changeMode(value) {
            this.mode = value;
            this.isOpen = false;
            localStorage.theme = this.mode;
        },

        toggleNavbar() {
            this.isOpen = !this.isOpen;
        },

        logoutUser() {
            let routeLogout = "logout";
            //
            this.$inertia.post(this.route(routeLogout));
        },
    },
};
</script>
