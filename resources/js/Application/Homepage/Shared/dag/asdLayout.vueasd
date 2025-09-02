<template>
    <meta-header :title="headerTitle">
      <template #robots>
        <meta head-key="robots" name="robots" content="noindex, nofollow" v-if="noIndexNoFollow" />
        <meta head-key="robots" name="robots" content="index, follow" v-else />
      </template>

      <template #description>
        <meta head-key="description" name="description" :content="headerDescription" />
        <link rel="canonical" v-if="headerUrl" :href="headerUrl" />
      </template>

      <template #opengraph>
        <meta head-key="og:title" property="og:title" :content="headerTitle" />
        <meta property="og:description" head-key="og:description" :content="headerDescription" />
        <meta v-if="headerUrl" head-key="og:url" property="og:url" :content="headerUrl" />
        <meta v-if="headerImage" head-key="og:image" property="og:image" :content="headerImage" />
        <meta head-key="og:type" property="og:type" content="website" />
      </template>
    </meta-header>

    <main :class="mode" id="app-layout-start">
      <section class="relative bg-layout-sun-50 text-layout-sun-900 dark:bg-layout-night-50 dark:text-layout-night-900 transition-colors duration-1000"  style='z-index:50;'>
        <!-- Header -->
        <nav class="fixed top-0 left-0 right-0 z-30 bg-layout-sun-50 text-layout-sun-900 dark:bg-layout-night-50 dark:text-layout-night-900 border-b border-layout-sun-200 dark:border-layout-night-200"  style='z-index:50;'>
          <div class="container mx-auto max-w-6xl p-6 lg:flex lg:items-center lg:justify-between" style='z-index:50;'>
            <div class="flex items-center justify-between">
              <!-- <brand-header :route-name="route('home.index')" :brand_1="$page.props.applications.brand_name_1" :brand_2="$page.props.applications.brand_name_2" :app-name="$page.props.applications.app_name"></brand-header> -->
                <a href="/"><mfxlogo></mfxlogo></a>
              <!-- Mobile menu button -->
              <div class="flex lg:hidden">
                <button v-on:click="toggleNavbar()" type="button" class="focus:outline-none text-primary-sun-1000 hover:text-primary-sun-800 focus:text-primary-sun-800 dark:text-primary-night-1000 dark:hover:text-primary-night-800 dark:focus:text-primary-night-800" aria-label="toggle menu">
                  <icon-menu class="w-6 h-6" v-if="!isOpen_Menu"></icon-menu>
                  <icon-close class="w-6 h-6" v-if="isOpen_Menu"></icon-close>
                </button>
              </div>
            </div>

            <!-- Mobile Menu open: "block", Menu closed: "hidden" -->
            <div :class="[isOpen_Menu ? 'translate-x-0 opacity-100 ' : 'opacity-0 -translate-x-full']" style='z-index:10000000;' class="absolute inset-x-0 mt-6 w-full px-6 py-4 shadow-md transition-all duration-300 ease-in-out bg-primary-sun-200 dark:bg-primary-night-200 lg:relative lg:top-0 lg:mt-0 lg:flex lg:w-auto lg:translate-x-0 lg:items-center lg:bg-transparent lg:p-0 lg:opacity-100 lg:shadow-none lg:dark:bg-transparent">
              <div class="flex flex-col items-center space-y-4 lg:mt-0 lg:flex-row lg:space-y-0 lg:space-x-8" style='z-index:10000000;'>
                <link-header :route-name="route('home.index')" name="Asarios Blog"></link-header>
                <link-header :route-name="route('home.images.index')" name="Bilder"></link-header>
                <!--<link-header :route-name="route('home.pricing')" name="Preise"></link-header>-->
                <!-- <link-header :route-name="route('home.blog.index')" name="Blog"></link-header> -->
                <link-header :route-name="route('home.shortpoems')" name="Shortpoems"></link-header>
                <link-header :route-name="route('home.didyouknow')" name="DidYouKnow"></link-header>

                <template v-if="!$page.props.userdata.user_id">
                  <link-header :route-name="route('login')" name="Login"></link-header>
                  <link-header :route-name="route('register')" name="Registrieren"></link-header>
                </template>

                <template v-if="$page.props.userdata.user_id">
                  <link-header :route-name="route('applicationswitch')" name="Dashboard"></link-header>
                </template>

                <button-change-mode :mode="mode" @changeMode="changeMode"></button-change-mode>
                <div class="ms-3 relative">
                                <Dropdown align="right" width="72" v-if="$page.props.auth.user">
                                    <template #trigger>
                                        <button
                                            v-if="
                                                $page.props.jetstream
                                                    .managesProfilePhotos
                                            "
                                            class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-layout-sun-300 dark:focus:border-layout-night-300 transition"
                                        >
                                            <img
                                            id="prof_pic"
                                            class="h-8 w-8 rounded-full object-cover"
                                                :src="
                                               $page.props.auth.user?.profile_photo_url.replace('public','').replace('images/images/','images/') || '/images/profile-photos/008.jpg'
                                                "
                                                :alt="
                                                    $page.props.userdata
                                                        .full_name
                                                "

                                            />
                                            {{  }}
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
                                                    viewBox="0 0 24 24"
                                                    stroke-width="1.5"
                                                    stroke="currentColor" fill="none"
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
                                                        .application_count > 100
                                                "
                                                >Anwendung wechseln</span
                                            >
                                            <span v-else>Startseite</span>
                                        </div>
                                        <dropdown-link
                                            :with-icon="false"
                                            :with-route="true"
                                            :route-name="
                                                route('admin.dashboard')
                                            "
                                        >
                                            <span
                                                v-if="
                                                    $page.props.userdata
                                                        .application_count > 100
                                                "
                                                >Anwendung wechseln</span
                                            >
                                            <span v-else>zum Dashboard</span>
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
            </div>
          </div>
        </nav>

        <!-- Loading -->
        <div v-if="isLoading || loadingStore.isLoading" id="loader" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-all" style='z-index:999999999'>
        <div class="text-center">
            <svg class="animate-spin h-10 w-10 text-primary-sun-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <p class="mt-4 text-primary-sun-100 text-sm">Bitte warten...</p>
        </div>
        </div>

        <!-- Content -->
        <div class="container mx-auto max-w-6xl min-h-screen py-32 px-2">
          <!-- Toast -->
          <div>
            <toast></toast>
          </div>

          <!-- Slot für Content -->
          <div class="mt-4">
            <slot></slot>
          </div>
        </div>
      </section>

      <!-- Footer -->
      <footer class="bg-layout-sun-50 text-layout-sun-900 dark:bg-layout-night-50 dark:text-layout-night-900 border-t border-layout-sun-200 dark:border-layout-night-200" aria-labelledby="footer-heading">
        <div class="container mx-auto max-w-6xl">
          <h2 id="footer-heading" class="sr-only">Footer</h2>
          <div class="px-1 md:px-4 lg:px-8 pb-8 pt-8">
            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-4 xl:col-span-2 xl:mt-0">
              <div class="md:grid md:grid-cols-2 md:gap-4">
                <div class="text-center md:text-left">
                  <h3 class="text-sm font-semibold leading-6 px-2">
                    <span> Webseite </span>
                  </h3>
                  <ul role="list" class="mt-6 space-y-4">
                    <li>
                      <link-footer name="Impressum" :route-name="route('home.imprint')"></link-footer>
                    </li>
                    <li>
                      <link-footer name="Datenschutzerklärung" :route-name="route('home.privacy')"></link-footer>
                    </li>
                    <li>
                      <link-footer @click="reopenCookieBanner">Cookie-Einstellungen</link-footer>
                    </li>
                  </ul>
                </div>
                <div class="text-center md:text-left">
                  <h3 class="text-sm font-semibold leading-6 px-2">
                    <span> Authentifizierung </span>
                  </h3>
                  <ul role="list" class="mt-6 space-y-4">
                    <li>
                      <link-footer name="Login" :route-name="route('login')"></link-footer>
                    </li>
                    <li>
                      <link-footer name="Registrierung" :route-name="route('register')"></link-footer>
                    </li>
                  </ul>
                </div>
              </div>
            </div>

            <div class="pt-8 text-layout-sun-700 dark:text-layout-night-700">
              <div class="flex flex-col items-center justify-between text-xs leading-5 gap-4">
                <div class="w-full flex flex-col md:flex-row flex-1 items-center justify-between gap-4">
                  <div>
                    <brand-footer></brand-footer>
                  </div>
                  <div>
                    <link-footer>
                      <a href="https://www.facebook.com" target="_blank" class="bg-layout-sun-0 dark:bg-layout-night-0">
                        <icon-facebook class="flex-shrink-0 w-6 h-6"></icon-facebook>
                      </a>
                    </link-footer>
                    <link-footer>
                      <a href="https://www.linkedin.com" target="_blank" class="bg-layout-sun-0 dark:bg-layout-night-0">
                        <icon-linked-in class="flex-shrink-0 w-6 h-6"></icon-linked-in>
                      </a>
                    </link-footer>
                    <link-footer>
                      <a href="https://youtube" target="_blank" class="bg-layout-sun-0 dark:bg-layout-night-0">
                        <icon-youtube class="flex-shrink-0 w-6 h-6"></icon-youtube>
                      </a>
                    </link-footer>
                  </div>
                </div>

                <div class="w-full flex flex-col md:flex-row flex-1 items-center justify-between gap-4">
                  <div class="text-xs leading-6">
                      {{ year }} Starter Eleven/MCSL. Ein Template von Oliver Reinking / Asario.
                  </div>

                  <div class="text-xs leading-6">
                    <span> Version: </span>
                    {{ $page.props.version.versionnr }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </footer>
    </main>
  </template>
<script>
import axios from "axios";
import { router } from '@inertiajs/vue3';
import { useLoadingStore } from '@/loading';
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
import BrandHeader from "@/Application/Shared/BrandHeader.vue";
import mfxlogo from "@/Application/Shared/mfxlogo.vue";
import Dropdown from "@/Application/Components/Content/Dropdown.vue";
import DropdownLink from "@/Application/Components/Content/DropdownLink.vue";
import LinkHeader from "@/Application/Shared/LinkHeader.vue";
import BrandFooter from "@/Application/Shared/BrandFooter.vue";
import LinkFooter from "@/Application/Shared/LinkFooter.vue";
import IconMenu from "@/Application/Components/Icons/Menu.vue"
import Toast from "@/Application/Components/Content/Toast.vue";
import ButtonChangeMode from "@/Application/Components/ButtonChangeMode.vue";
import { ref } from "vue";

export default {
  name: "Homepage_Shared_Layout",

  components: {
    MetaHeader,
    BrandHeader,
    LinkHeader,
    BrandFooter,
    LinkFooter,
    Toast,
    mfxlogo,
    IconMenu,
    Dropdown,
    DropdownLink,
    ButtonChangeMode
  },

  setup() {
    const loadingStore = useLoadingStore();
    return { loadingStore };
  },

  data() {
    return {
      mode: localStorage.theme ? localStorage.theme : "light",
      isOpen_Menu: false,
      year: new Date().getFullYear(),
      pendingRequests: 0,
      isLoading: localStorage.getItem('loading') === 'true',
      search: '',
      searchval: false,
      imagesLoaded: false,
      searchTimeout: null, // Timeout für Inaktivitätsprüfung
    };
  },

  mounted() {
    const  = localStorage.getItem('reload_dashboard');

    if (shouldReload) {
      localStorage.removeItem('reload_dashboard');
    }

    // Den 'search' Parameter prüfen
    const urlParams = new URLSearchParams(window.location.search);
    const searchParam = urlParams.get('search');

    this.search = searchParam ?? '';

    if (searchParam === '' || searchParam === null) {
      this.setLoadingState(true);
      this.searchval = true;

      // Startet Timeout für spätere leere Suche
      this.startSearchTimeout();
    } else {
      this.setLoadingState(false);
      this.searchval = false;
    }

    // Axios Interceptoren
    axios.interceptors.request.use((config) => {
      this.pendingRequests += 1;
      this.setLoadingState(this.searchval);
      return config;
    });

    axios.interceptors.response.use(
      (response) => {
        this.pendingRequests -= 1;
        this.checkLoadingState();
        return response;
      },
      (error) => {
        this.pendingRequests -= 1;
        this.checkLoadingState();
        return Promise.reject(error);
      }
    );

    // Bilder laden überwachen
    this.waitForImagesToLoad();

    if (this.isLoading) {
      localStorage.setItem('loading', 'true');
    }
  },

  methods: {


    setLoadingState(state) {
      this.isLoading = state;
      localStorage.setItem('loading', state.toString());
    },

    checkLoadingState() {
      if (this.pendingRequests === 0 && this.imagesLoaded) {
        this.setLoadingState(false);
      }
    },

    waitForImagesToLoad() {
      const images = document.querySelectorAll('img');
      const totalImages = images.length;
      let imagesLoadedCount = 0;

      if (totalImages === 0) {
        this.imagesLoaded = true;
        this.checkLoadingState();
        return;
      }

      images.forEach((img, index) => {
        if (img.complete) {
          imagesLoadedCount++;
        } else {
          img.addEventListener('load', () => {
            imagesLoadedCount++;
            if (imagesLoadedCount === totalImages) {
              this.imagesLoaded = true;
              this.checkLoadingState();
            }
          });
        }
      });

      if (imagesLoadedCount === totalImages) {
        this.imagesLoaded = true;
        this.checkLoadingState();
      }
    },

    toggleNavbar() {
      this.isOpen_Menu = !this.isOpen_Menu;
    },

    changeMode() {
      this.mode = this.mode === "dark" ? "light" : "dark";
      localStorage.theme = this.mode;
    },

    logoutUser() {
      this.$inertia.post(this.route("logout"));
    },

    // Startet 3-Sekunden-Timeout, wenn der Nutzer mit Tippen aufhört
    startSearchTimeout() {
      clearTimeout(this.searchTimeout);
      this.searchTimeout = setTimeout(() => {
        if (this.search.trim() !== '') {
          this.setLoadingState(true);
        }
      }, 3000);
    },

    // Bei Eingabe im Suchfeld
    onSearchInput(event) {
      this.search = event.target.value;
      this.startSearchTimeout();
    },
  },
};
</script>

<style  >
#prof_pic{
    width:32px !important;
    height:32px !important;
    object-fit: cover;
}
/* Deine Styles hier */
</style>
