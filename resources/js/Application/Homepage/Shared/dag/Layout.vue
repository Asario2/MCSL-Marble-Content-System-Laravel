<template>
    <div>
      <!-- Meta Header (Slots sauber innerhalb der Komponente) -->
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

      <main class="dark" id="app-layout-start">
        <section
          class="relative bg-layout-sun-50 text-layout-sun-900 dark:bg-transparent dark:bg-layout-night-50 dark:text-layout-night-900 transition-colors duration-1000"
          style="z-index:50;"
        >
          <!-- Header -->
          <nav
            class="fixed top-0 left-0 right-0 z-30 bg-layout-night-50 text-layout-sun-900 dark:bg-layout-night-50 dark:text-layout-night-900"
            style="z-index:50;"
          >
            <div class="container mx-auto max-w-6xl p-6 pb-0 lg:flex lg:items-center lg:justify-between" style="z-index:50;">
              <!-- left: mobile logo + hamburger -->
              <div class="flex items-center justify-between w-full lg:w-auto">
                <!-- Mobile logo (visible only < lg) -->
                <a href="/" class="block lg:hidden">
                  <daglogo />
                </a>

                <!-- Mobile hamburger -->
                <div class="flex lg:hidden">
                  <button
                    @click="toggleNavbar()"
                    type="button"
                    class="focus:outline-none text-primary-sun-1000 hover:text-primary-sun-800 dark:text-primary-night-1000 dark:hover:text-primary-night-800"
                    aria-label="toggle menu"
                  >
                    <icon-menu class="w-6 h-6" v-if="!isOpen_Menu" />
                    <icon-close class="w-6 h-6" v-else />
                  </button>
                </div>
              </div>

              <!-- Spacer on large to keep layout consistent -->
              <div class="hidden lg:block"></div>
            </div>

            <!-- --- MOBILE DROPDOWN (nur < lg) --- -->
            <transition name="slide-fade">
              <div
                v-if="isOpen_Menu"
                class="lg:hidden absolute inset-x-0 mt-6 w-full px-6 py-4 shadow-md bg-layout-trans dark:bg-primary-night-200"
                style="z-index:10000000;"
              ><div class="m-4 mt-[-20px] bg-layout-sun-200 dark:bg-layout-night-200t lg:rounded-lg pt-10 border-2 border-black">
                <div class="flex flex-col items-center space-y-3">

                  <a class="ddl " href="/"><div>Startseite</div></a><br />
                  <a class="ddl " href="/lostnfound"><div>Lost &amp; Found</div></a><br />
                  <a class="ddl " href="https://www.monikadargies.de/webtrees" target="_pgv"><div>Ahnenforschungstool</div></a><br />ysad
                  <a class="ddl " href="https://www.ancestry.de/family-tree/tree/69588419/family/pedigree?cfpid=48202672474&selnode=1" target="_anc"><div>Ancestry.de</div></a><br />
                  <a class="ddl " href="/links"><div>Links</div></a><br />
                </div>
                </div>
              </div>
            </transition>

            <!-- --- DESKTOP TOPLOG (nur >= lg) --- -->
            <div class="toplog  hidden lg:flex justify-center relative left-[34px]">
              <nav2>
                <div class="navi">
                  <ul class="navv flex space-x-6">
                    <li><a href="/" class="clo cl1"><div class="hd">Startseite</div></a></li>
                    <li><a href="/lostnfound" class="clo cl2"><div class="hd">Lost &amp; Found</div></a></li>
                    <li><a href="https://www.monikadargies.de/webtrees" class="clo cl3" target="_pgv"><div class="hd">Ahnenforschungstool</div></a></li>
                    <li><a href="https://www.ancestry.de/family-tree/tree/69588419/family/pedigree?cfpid=48202672474&selnode=1" class="clo cl4" target="_anc"><div class="hd">Ancestry.de</div></a></li>
                    <li><a href="/links" class="clo cl5"><div class="hd">Links</div></a></li>
                  </ul>
                </div>
              </nav2>
            </div>
          </nav>

          <!-- Loading -->
          <div v-if="isLoading || loadingStore.isLoading" id="loader" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-all" style="z-index:999999999">
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
            <div><toast /></div>

            <!-- Slot für Content -->
            <div class="lg:mt-[165px] dark mwe" style="max-width:1024px;">
              <slot></slot>
            </div>
          </div>
        </section>

        <!-- Footer -->
        <footer class="dark:bg-[rgb(218,83,14)] text-layout-sun-900 dark:text-layout-night-900 border-t border-layout-sun-200 dark:border-layout-night-200" aria-labelledby="footer-heading">
          <div class="container mx-auto max-w-6xl">
            <h2 id="footer-heading" class="sr-only">Footer</h2>
            <div class="px-1 md:px-4 lg:px-8 pb-8 pt-8">
              <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-4 xl:col-span-2 xl:mt-0">
                <div class="md:grid md:grid-cols-2 md:gap-4">
                  <!-- Webseite -->
                  <div class="text-center md:text-left">
                    <h3 class="text-sm font-semibold leading-6 px-2">Webseite</h3>
                    <ul role="list" class="mt-6 space-y-4 list-none">
                      <li><link-footer name="Impressum" :route-name="route('home.imprint.mfx')" /></li>
                      <li><link-footer name="Datenschutzerklärung" :route-name="route('home.privacy')" /></li>
                      <li><link-footer name="Kontakt" :route-name="route('home.contacts.dag')" /></li>
                      <li><link-footer @click="reopenCookieBanner">Cookie-Einstellungen</link-footer></li>
                    </ul>
                  </div>

                  <!-- Authentifizierung -->
                  <div class="text-center md:text-left">
                    <h3 class="text-sm font-semibold leading-6 px-2">Authentifizierung</h3>
                    <ul role="list" class="mt-6 space-y-4 list-none">
                      <li><link-footer name="Login" :route-name="route('login')" /></li>
                      <li v-if="SD() == 'ab'"><link-footer name="Registrierung" :route-name="route('register')" /></li>
                    </ul>
                  </div>
                </div>
              </div>

              <!-- Branding + Social -->
              <div class="pt-8 text-layout-sun-700 dark:text-layout-night-700">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4 text-xs">
                  <brand-footer />
                  <div class="flex gap-2">
                    <a href="https://www.facebook.de" target="_blank"><icon-facebook class="w-6 h-6" /></a>
                    <a href="https://www.xing.de" target="_blank"><icon-xing class="w-6 h-6" /></a>
                    <a href="https://whatsapp.com" target="_blank"><icon-whatsapp class="w-6 h-6" /></a>
                  </div>
                </div>

                <div class="flex flex-col md:flex-row items-center justify-between gap-4 text-xs mt-2">
                  <div>{{ year }} Starter Eleven/MCSL. Ein Template von Oliver Reinking / Asario.</div>
                  <div><IconMCSL /> Version: {{ $page.props.version.versionnr }}</div>
                </div>
              </div>
            </div>
          </div>
        </footer>
      </main>
    </div>
  </template>

  <script>
  import axios from "axios";
  import IconMCSL from "@/Application/Components/Icons/IconMCSL.vue";
  import { useLoadingStore } from '@/loading';
  import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
  import BrandHeader from "@/Application/Shared/BrandHeader.vue";
  import daglogo from "@/Application/Shared/daglogo.vue";
  import Dropdown from "@/Application/Components/Content/Dropdown.vue";
  import DropdownLink from "@/Application/Components/Content/DropdownLink.vue";
  import LinkHeader from "@/Application/Shared/LinkHeader_mfx.vue";
  import BrandFooter from "@/Application/Shared/BrandFooter.vue";
  import LinkFooter from "@/Application/Shared/LinkFooter.vue";
  import IconMenu from "@/Application/Components/Icons/Menu.vue";
  import IconClose from "@/Application/Components/Icons/Close.vue";
  import Toast from "@/Application/Components/Content/Toast.vue";
  import ButtonChangeMode from "@/Application/Components/ButtonChangeMode.vue";

  export default {
    name: "Homepage_Shared_Layout",

    components: {
      MetaHeader,
      BrandHeader,
      LinkHeader,
      BrandFooter,
      LinkFooter,
      Toast,
      IconMCSL,
      daglogo,
      IconMenu,
      IconClose,
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
        headerTitle: this.$page?.props?.title ?? "",
        headerDescription: this.$page?.props?.description ?? "",
        headerUrl: this.$page?.props?.url ?? null,
        headerImage: this.$page?.props?.image ?? null,
        noIndexNoFollow: false,

        mode: localStorage.theme ? localStorage.theme : "light",
        isOpen_Menu: false,
        year: new Date().getFullYear(),
        isLoading: true,
        pendingRequests: 0,
        imagesLoaded: false,
        search: '',
        searchval: false,
        searchTimeout: null
      };
    },

    mounted() {
//       console.log("mounted läuft");

      // Den 'search' Parameter prüfen
      const urlParams = new URLSearchParams(window.location.search);
      const searchParam = urlParams.get('search');
      this.search = searchParam ?? '';

      if (searchParam === '' || searchParam === null) {
        this.setLoadingState(true);
        this.searchval = true;
        this.startSearchTimeout();
      } else {
        this.setLoadingState(false);
        this.searchval = false;
      }

      // Axios Interceptoren (Pending-Requests zählen)
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

      if (this.$page.props.flash?.needsReload && !sessionStorage.getItem('needsReload')) {
        sessionStorage.setItem('needsReload', '1');
        //alert("RELAODED");
        //window.location.reload();
      } else {
        sessionStorage.removeItem('needsReload');
      }
    },

    methods: {
      SD() {
        return window.subdomain;
      },

      reopenCookieBanner() {
        if (window.LaravelCookieConsent && typeof window.LaravelCookieConsent.reset === 'function') {
          window.LaravelCookieConsent.reset();
        }
      },

      setLoadingState(state) {
        this.isLoading = state;
        localStorage.setItem('loading',  state ? state.toString():'');
      },

      checkLoadingState() {
        if (this.pendingRequests === 0 && this.imagesLoaded) {
          this.setLoadingState(false);
          localStorage.removeItem('loading');
        }
      },

      waitForImagesToLoad() {
        const images = document.querySelectorAll('img');
        const totalImages = images.length;
        let imagesProcessedCount = 0;

        if (totalImages === 0) {
          this.imagesLoaded = true;
          this.checkLoadingState();
          return;
        }

        const imageProcessed = () => {
          imagesProcessedCount++;
          if (imagesProcessedCount === totalImages) {
            this.imagesLoaded = true;
            this.checkLoadingState();
          }
        };

        images.forEach((img) => {
          if (img.complete) {
            imageProcessed();
          } else {
            img.addEventListener('load', imageProcessed, { once: true });
            img.addEventListener('error', imageProcessed, { once: true });
          }
        });
      },

      toggleNavbar() {
        this.isOpen_Menu = !this.isOpen_Menu;
      },

      changeMode() {
        this.mode = this.mode === "dark" ? "light" : "dark";
        localStorage.theme = this.mode;
      },

      async logoutUser() {
        try {
          await this.$inertia.post(this.route('logout'));
        } catch (e) {
          console.error(e);
        }
      },

      startSearchTimeout() {
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(() => {
          if (this.search.trim() !== '') {
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

  <style>
  /* Basis / kleine Korrekturen aus deinem Original CSS */
  /* Profilbild */
  #prof_pic {
    width: 32px !important;
    height: 32px !important;
    object-fit: cover;
  }

  /* background grad (original) */
  @media (min-width: 1023px) {
    .bggrad {
      background-image: url("/images/logos/grad_bg_9000x1.jpg");
      background-position: center center;
      background-repeat: repeat-y;
    }
  }

  @media (min-width: 1200px) {
    .mwe {
      margin-left: 92px;
    }
  }

  /* Desktop toplog (zentriert) */
  .toplog {
    background-position: top left;
    background-image: url('/images/_dag/web/banner.png') !important;
    background-repeat: no-repeat;
    height: 245px;
    padding-top: 154px;
    width: 1024px;
    margin: 10px auto 0 auto;
    border-radius: 10px;
    border: 2px solid #000 !important;
    display: flex;
    justify-content: center;
    align-items: flex-end;
  }
  @media (max-width: 1023px) {
    .toplog {
      display:none !important;
    }
  }
  /* Navigation */
  .navi {
    /* reset */
  }
DIV.hd:hover{
    color:#CCC;
}
.cl3{
    margin-left: -7px;
}
  /* navv als flex-Zeile */
  .navv {
    list-style-type: none;
    margin: 0;
    padding: 0;
    display: flex;
    gap: 18px;
    align-items: center;
    justify-content: center;
  }

  /* clo / clo1..clo5 für Styling und evtl. spezielle Abstände */
  .clo {
    color: #000;
    font-size: 22px !important;
    text-shadow: 0px 0px 0px #000;
    font-family: "blackchancery";
    font-display: swap;
    padding-top: 12px !important;
    text-decoration: none;
    display: inline-block;
    padding-bottom: 10px;
    float:left;
  }

  /* individuelle Klassen, falls du spezielle offsets brauchst */
  /* .cl1 { padding-left: 155px !important; }
  .cl2 { margin-left: -5px !important; }
  .cl3 { margin-left: -40px !important; }
  .cl4 { padding-left: 5px !important; }
  .cl5 { padding-left: 5px !important; } */
  .cl1,.cl2,.cl3,.cl4,.cl5{
    width:162px;
    text-align: center;
  }

  /* mobile dropdown link */
  .ddl {
    color: #fff;
    font-weight: bold;
    line-height: 20px;
    font-family: Arial, Helvetica, sans-serif;
    display: inline-block;
  }

  /* hd styling (Titel im Link) */
  .hd {
    color: #000;
    line-height: 30px;
    margin-top: 32px;
    min-width: 150px !important;
    text-align: center;
  }

  /* Schriftart einbinden (wie vorher) */
  @font-face {
    font-family: 'blackchancery';
    src: url(/css/webfonts/blackchancery.woff2) format('woff2'),
         url(/css/webfonts/blackchancery.woff) format('woff'),
         url(/css/webfonts/blackchancery.ttf) format('truetype');
  }

  /* Mobile dropdown transition */
  .slide-fade-enter-active,
  .slide-fade-leave-active {
    transition: all 0.25s ease;
  }
  .slide-fade-enter-from,
  .slide-fade-leave-to {
    opacity: 0;
    transform: translateY(-8px);
  }
  </style>
