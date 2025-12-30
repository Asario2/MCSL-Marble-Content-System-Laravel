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
                <brand-header :route-name="route('home.blog.index')" :brand_1="$page.props.applications.brand_name_1" :brand_2="$page.props.applications.brand_name_2" :app-name="$page.props.applications.app_name"></brand-header>
                    <div
                    id="arrow2"
                    class="absolute top-7 z-40 flex justify-center items-center cursor-pointer hover:scale-110 transition-transform duration-300" style="right:4.2rem;display:none;"
                    >
                    <JrightArrow />
                    </div>
                <!-- Mobile menu button -->
                <div class="flex lg:hidden">
                    <button v-on:click="toggleNavbar()" type="button" class="focus:outline-none text-primary-sun-1000 hover:text-primary-sun-800 focus:text-primary-sun-800 dark:text-primary-night-1000 dark:hover:text-primary-night-800 dark:focus:text-primary-night-800" aria-label="toggle menu">
                    <icon-menu class="w-6 h-6" v-if="!isOpen_Menu"></icon-menu>
                    <icon-close class="w-6 h-6" v-if="isOpen_Menu"></icon-close>
                    </button>
                </div>
                </div>

                <!-- Mobile Menu open: "block", Menu closed: "hidden" -->
                <div :class="[isOpen_Menu ? 'translate-x-0 opacity-100 ' : 'opacity-0 -translate-x-full']" style='z-index:10000000;' class="absolute inset-x-0 mt-6 w-full px-6 py-4 shadow-md transition-all duration-300 ease-in-out bg-primary-sun-0 dark:bg-primary-night-0 lg:relative lg:top-0 lg:mt-0 lg:flex lg:w-auto lg:translate-x-0 lg:items-center lg:bg-transparent lg:p-0 lg:opacity-100 lg:shadow-none lg:dark:bg-transparent">
                <div class="flex flex-col items-center space-y-4 lg:mt-0 lg:flex-row lg:space-y-0 lg:space-x-3" style='z-index:10000000;'>
                    <link-header :route-name="route('home.index')" name="Home"></link-header>
                    <link-header :route-name="route('home.blog.index')" name="Asarios Blog"></link-header>
                    <link-header :route-name="route('home.about')" name="About Me"></link-header>
                    <link-header :route-name="route('home.images.index')" name="Bilder"></link-header>
                    <!--<link-header :route-name="route('home.pricing')" name="Preise"></link-header>-->
                    <!-- <link-header :route-name="route('home.blog.index')" name="Blog"></link-header> -->
                    <link-header :route-name="route('home.shortpoems')" name="Shortpoems"></link-header>
                    <link-header :route-name="route('home.didyouknow')" name="DidYouKnow"></link-header>
                    <template v-if="!$page.props.userdata.user_id">
                    <link-header :route-name="route('login')" name="Login"></link-header>
                    <link-header :route-name="route('register')" name="Registrieren"></link-header>
                    </template>

                    <template v-if="$page.props.userdata.user_id && $page.props.userdata.is_admin">
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
                                                class="h-8 w-8 rounded-full object-cover"
                                                :src="
                                                GetProfileImagePath($page.props.auth.user?.profile_photo_url)"
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
                                            <span v-else><span class="inline-flex items-center gap-1">
                                                <IconDashboard class="w-4 h-4" color="#ffa500" />
                                                <span>Zum Dashboard</span>
                                            </span></span>
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
                                            <span class="inline-flex items-center gap-1">
                                                <IconProfile class="w-4 h-4" color="#ffa500" />
                                                <span>Profil</span>
                                            </span>
                                        </dropdown-link>
                                        <dropdown-link v-if="SD() == 'ab'"
                                            :with-icon="false"
                                            :with-route="true"
                                            :route-name="route('admin.mcslpoints')"
                                        >
                                            <span class="inline-flex items-center gap-1">
                                                <IconStar_thin class="w-4 h-4" color="#ffa500" />
                                                <span>{{ mcslpoints }} MCSL Points</span>
                                            </span>
                                        </dropdown-link>

                                             <dropdown-link v-if="rights?.delete == 1"
                                                :with-icon="false"
                                                :with-route="true"
                                                :route-name="
                                                    route('pm.index')
                                                ">
                                            <span class="inline-flex items-center gap-1">
                                                <IconPM class="w-4 h-4" color="#ffa500" />
                                                <span>Private Nachrichten</span>
                                            </span>

                                            </dropdown-link>
                                        <dropdown-link
                                                :with-icon="false"
                                                :with-route="true"
                                                :route-name="
                                                    route('admin.kontakte')
                                                ">
                                            <span class="inline-flex items-center gap-1">
                                                <IconContacts_alt class="w-4 h-4" color="#ffa500" />
                                                <span>Kontakte</span>
                                            </span>

                                            </dropdown-link>


                                        <div
                                            class="my-2 border-t border-layout-sun-200 dark:border-layout-night-200"
                                        />

                                        <!-- Authentication -->
                                        <form @submit.prevent="logoutUser">
                                            <button type="submit">
                                                <dropdown-link>
                                            <span class="inline-flex items-center gap-1">
                                                <IconLogout class="w-4 h-4" color="#ffa500" />
                                                <span>Abmelden</span>
                                            </span>
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
        <footer class="foot bg-layout-sun-50 text-layout-sun-900 dark:bg-layout-night-50 dark:text-layout-night-900 border-t border-layout-sun-200 dark:border-layout-night-200" aria-labelledby="footer-heading">
            <div class="container mx-auto max-w-6xl">
            <h2 id="footer-heading" class="sr-only">Footer</h2>
            <div class="px-1 md:px-4 lg:px-8 pb-8 pt-8">
                <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-4 xl:col-span-2 xl:mt-0">
                <div class="md:grid md:grid-cols-2 md:gap-4">
                    <div class="text-center md:text-left">
                    <h3 class="text-sm font-semibold leading-6 px-2">
                        <span> Webseite </span>
                    </h3>
                    <ul role="list" class="mt-6 space-y-4 list-none">
                        <li>
                        <link-footer name="Benutzer" :href="route('home.userlist')"></link-footer>
                        </li>
                        <li>
                        <link-footer name="Impressum" :route-name="route('home.imprint')"></link-footer>
                        </li>
                        <li>
                        <link-footer name="Datenschutzerklärung" :route-name="route('home.privacy')"></link-footer>
                        </li>
                        <li>
                            <!-- <link-footer @click="reopenCookieBanner"><b>Cookie-Einstellungen</b></link-footer> -->
<a class="ToggleCookieLink text-layout-sun-600 dark:text-layout-night-900 cursor-pointer inline-block rounded-lg px-2 py-1 text-sm text-layout-sun-700 hover:bg-primary-sun-300 hover:text-layout-sun-900 dark:text-layout-night-700 dark:hover:bg-primary-night-300 dark:hover:text-layout-night-900" onclick="showHideToggleCookiePreferencesModal()"><span>Cookie Einstellungen</span></a>

                        </li>
                        <li>
                            <link-footer name="Kontakt" :route-name="route('home.contacts')"></link-footer>
                        </li>
                    </ul>
                    <br />
                    <NewsletterSubscribe class="ml-[4px] mb-[-2px]" />
                    </div>
                    <div class="text-center md:text-left">
                    <h3 class="text-sm font-semibold leading-6 px-2">
                        <span> Authentifizierung </span>
                    </h3>
                    <ul role="list" class="mt-6 space-y-4 list-none">
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
                        <!-- <link-footer>
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
                        </link-footer> -->


                    </div>
                    </div>

                    <div class="w-full flex flex-col md:flex-row flex-1 items-center justify-between gap-4">
                    <div class="text-xs leading-6">
                        &copy; {{ year }} Starter Eleven/MCSL. Ein Template von Oliver Reinking / Asario.
                    </div>

                    <div class="text-xs leading-6">
                        <span><IconMCSL></IconMCSL> Version: </span>
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
    import { useLoadingStore } from "@/loading";
    import IconMCSL from "@/Application/Components/Icons/IconMCSL.vue";
    import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
    import BrandHeader from "@/Application/Shared/BrandHeader.vue";
    import Dropdown from "@/Application/Components/Content/Dropdown.vue";
    import DropdownLink from "@/Application/Components/Content/DropdownLink.vue";
    import LinkHeader from "@/Application/Shared/LinkHeader.vue";
    import BrandFooter from "@/Application/Shared/BrandFooter.vue";
    import LinkFooter from "@/Application/Shared/LinkFooter.vue";
    import IconMenu from "@/Application/Components/Icons/Menu.vue";
    import Toast from "@/Application/Components/Content/Toast.vue";
    import ButtonChangeMode from "@/Application/Components/ButtonChangeMode.vue";
    import { SD,GetProfileImagePath,CheckTRights } from "@/helpers";
    import NewsletterSubscribe from "@/Application/Components/Social/NewsletterSubscribe.vue";
    import JrightArrow from "@/Application/Components/Icons/JrightArrow.vue";
    import IconStar_thin from "@/Application/Components/Icons/IconStar_thin.vue";
import IconContacts_alt from "@/Application/Components/Icons/IconContacts_alt.vue";
import IconLogout from "@/Application/Components/Icons/IconLogout.vue";
import IconProfile from "@/Application/Components/Icons/IconProfile.vue";
import IconPM from "@/Application/Components/Icons/IconPM.vue";
import IconDashboard from "@/Application/Components/Icons/IconDashboard.vue";



    export default {
      name: "Homepage_Shared_Layout",

      components: {
        MetaHeader,
        BrandHeader,
        LinkHeader,
        BrandFooter,
        IconLogout,
        IconProfile,
        IconPM,
        IconContacts_alt,
        IconStar_thin,
        IconDashboard,
        JrightArrow,
        LinkFooter,
        Toast,
        IconMenu,
        IconMCSL,
        Dropdown,
        DropdownLink,
        ButtonChangeMode,
        NewsletterSubscribe,

      },

      props: {
        sd: {
          type: String,
          required: true,
        },
      },

      setup() {
        const loadingStore = useLoadingStore();
        return { loadingStore };
      },

      data() {
        return {
          mode: localStorage.theme ? localStorage.theme : "dark",
          isOpen_Menu: false,
          year: new Date().getFullYear(),
          pendingRequests: 0,
          isLoading: localStorage.getItem("loading") === "true",
          search: "",
          searchval: false,
          imagesLoaded: false,
          searchTimeout: null,
          rights: {
            edit: null,
            delete: null,
            },
            mcslpoints: 0,
        };
      },

      async mounted() {
        const params = new URLSearchParams(window.location.search);
    const search = params.get("search");

    // MCS POINTS
    this.loadmcslpoints(); // initial

    // Wenn search gesetzt ist, verstecke das Loading-Div
    if (search && search.trim() !== "") {

      this.isLoading = false;
    }
    else{
        this.isLoading = true;
    }
    this.rights.edit = await CheckTRights("edit", 'private_messages');
    this.rights.delete = await CheckTRights("delete", 'private_messages');
        const shouldReload = localStorage.getItem("reload_dashboard");
        if (shouldReload) {
          localStorage.removeItem("reload_dashboard");
        }
        this.waitForConsent();
        // URL-Parameter auslesen
        const urlParams = new URLSearchParams(window.location.search);
        const searchParam = urlParams.get("search");
        this.search = searchParam ?? "";

        if (searchParam === "" || searchParam === null) {
          this.setLoadingState(true);
          this.searchval = true;
          this.startSearchTimeout();
        } else {
          this.setLoadingState(false);
          this.searchval = false;
        }

        // Axios Interceptor
        axios.interceptors.request.use((config) => {
          this.pendingRequests += 1;
//           console.log("⬆️ Request gestartet", this.pendingRequests);
          this.setLoadingState(this.searchval);
          return config;
        });

        axios.interceptors.response.use(
          (response) => {
            this.pendingRequests -= 1;
//             console.log("⬇️ Response erhalten", this.pendingRequests);
            this.checkLoadingState();
            return response;
          },
          (error) => {
            this.pendingRequests -= 1;
//             console.log("⚠️ Response Fehler", this.pendingRequests);
            this.checkLoadingState();
            return Promise.reject(error);
          }
        );

        // Bilder beobachten
        this.waitForImagesToLoad();

        if (this.isLoading) {
          localStorage.setItem("loading", "true");
        }
        this.$nextTick(() => {
        if (window.LaravelCookieConsent) {
//             console.log("CookieConsent ready");
        }
        });

        },

      methods: {
        GetProfileImagePath,
        SD,
        CheckTRights,
        async loadmcslpoints() {
        try {
                const { data } = await axios.get('/api/mcslpoints/1');
                this.mcslpoints = data; // automatisch reaktiv
            } catch (err) {
                console.error('Fehler beim Laden der MCSL Points:', err);
            }
        },
        setLoadingState(state) {
//           console.log("🔄 setLoadingState:", state);
          this.isLoading = state;
          localStorage.setItem("loading", state ? state.toString() : '');
        },

        waitForConsent(callback) {
            const interval = setInterval(() => {
            if (window.LaravelCookieConsent) {
                clearInterval(interval);
//                 console.log("Consent ready");
                if (callback) callback();
            }
            }, 50);
        },
        openCookieBanner() {
            // Sicherstellen, dass das Script geladen ist
            this.$nextTick(() => {
                if (window.LaravelCookieConsent && typeof window.LaravelCookieConsent.show === 'function') {
                window.LaravelCookieConsent.reset();
                } else {
                console.warn("CookieConsent noch nicht geladen");
                }
            });

        },
        methods: {
    // ... andere Methoden
        reopenCookieBanner() {
            console.log("reopenCookieBanner aufgerufen");

            // Prüft alle 50ms, ob CookieConsent verfügbar ist
            const interval = setInterval(() => {
                if (window.LaravelCookieConsent) {
                    clearInterval(interval); // Stoppt die Schleife, sobald verfügbar

                    // Reset nur, wenn Funktion existiert
                    if (typeof window.LaravelCookieConsent.reset === 'function') {
                        window.LaravelCookieConsent.reset();
                    }

                    // Banner anzeigen, nur wenn show() existiert
                    if (typeof window.LaravelCookieConsent.show === 'function') {
                        window.LaravelCookieConsent.show();
                        console.log("Cookie-Banner angezeigt");
                    } else {
                        console.error("window.LaravelCookieConsent.show() nicht gefunden");
                    }
                }
            }, 50);
        },
    },



        checkLoadingState() {
          console.log("🔍 checkLoadingState()", {
            pending: this.pendingRequests,
            imagesLoaded: this.imagesLoaded,
            isLoading: this.isLoading,
          });

          if (this.pendingRequests === 0 && this.imagesLoaded) {
//             console.log("✅ Ladezustand beendet");
            this.setLoadingState(false);
          }
        },

        waitForImagesToLoad() {
          const images = document.querySelectorAll("img");
          const totalImages = images.length;
          let imagesLoadedCount = 0;

//           console.log("📸 Images gefunden:", totalImages);

          if (totalImages === 0) {
            this.imagesLoaded = true;
            this.checkLoadingState();
            return;
          }

          const markImageDone = () => {
            imagesLoadedCount++;
//             console.log(`📸 Bild ${type}:`, src, `${imagesLoadedCount}/${totalImages}`);
            if (imagesLoadedCount === totalImages) {
              this.imagesLoaded = true;
              this.checkLoadingState();
            }
          };

          images.forEach((img) => {
            if (img.complete) {
              markImageDone(img.src, "complete");
            } else {
              img.addEventListener("load", () => markImageDone(img.src, "load"));
              img.addEventListener("error", () => markImageDone(img.src, "error"));
            }
          });
        },

        toggleNavbar() {
          this.isOpen_Menu = !this.isOpen_Menu;
        },

        changeMode() {
          this.mode = this.mode === "dark" ? "light" : "dark";
          localStorage.theme = this.mode;
          this.isOpen_Menu = false;
        },

        logoutUser() {
          this.$inertia.post(this.route("logout"));
        },

        startSearchTimeout() {
          clearTimeout(this.searchTimeout);
          this.searchTimeout = setTimeout(() => {
            if (this.search.trim() !== "") {
              this.setLoadingState(true);
            }
          }, 3000);
        },

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
    .foot {
    position: relative;
    z-index: 10;
    }
    A#em{
        color:yellow;
    }
    @media screen and (max-width: 1024px) {
    #arrow2{
        display:inline-block !important;
    }
    }
    /* Deine Styles hier */
    </style>

