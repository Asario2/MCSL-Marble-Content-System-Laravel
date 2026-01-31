<template>
<div
    v-if="state.isLoading"
    id="loader"
    class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-opacity"
    style="z-index:999999999"
>
    <div class="text-center">
    <svg
        class="animate-spin h-10 w-10 text-primary-sun-500 mx-auto"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
    >
        <circle
        class="opacity-25"
        cx="12"
        cy="12"
        r="10"
        stroke="currentColor"
        stroke-width="4"
        />
        <path
        class="opacity-75"
        fill="currentColor"
        d="M4 12a8 8 0 018-8v8H4z"
        />
    </svg>

    <p class="mt-4 text-primary-sun-100 text-sm">
        Bitte warten...
    </p>
    </div>
</div>
</template>

<script>
console.log("🔥 LOADER FILE LOADED succ");
import axios from "axios";
import { Inertia } from "@inertiajs/inertia";
import { loaderState } from "@/utils/globalLoader";

export default {
name: "Loader",

data() {
    return {
    state: loaderState,
    showDelay: 150,
    minVisible: 400,
    };
},

mounted() {
    console.log("Loader mounted", this._uid);
    if (!window.__LOADER_INITIALIZED__) {
        window.__LOADER_INITIALIZED__ = true;
        console.log("Initializing Axios & Inertia interceptors");
        this.installAxios();
        this.installInertia();
    }
},

methods: {
    update() {
        const shouldShow = this.state.inertiaLoading || this.state.pendingRequests > 0;

        console.log("update() called | shouldShow:", shouldShow, "state:", JSON.stringify(this.state));

        if (shouldShow) {
            if (this.state.hideTimer) {
                clearTimeout(this.state.hideTimer);
                console.log("Cleared hideTimer");
                this.state.hideTimer = null;
            }

            if (this.state.isLoading) {
                console.log("Loader already visible, skipping show");
                return;
            }

            if (!this.state.showTimer) {
                console.log("Starting showTimer for", this.showDelay, "ms");
                this.state.showTimer = setTimeout(() => {
                    this.state.isLoading = true;
                    console.log("Loader is now VISIBLE");
                    this.state.showTimer = null;
                }, this.showDelay);
            }
            return;
        }

        // Hide logic
        if (this.state.showTimer) {
            clearTimeout(this.state.showTimer);
            console.log("Cleared showTimer because no longer should show");
            this.state.showTimer = null;
        }

        if (!this.state.isLoading) {
            console.log("Loader already hidden, skipping hide");
            return;
        }

        if (!this.state.hideTimer) {
            console.log("Starting hideTimer for", this.minVisible, "ms");
            this.state.hideTimer = setTimeout(() => {
                this.state.isLoading = false;
                console.log("Loader is now HIDDEN");
                this.state.hideTimer = null;
            }, this.minVisible);
        }
    },

    inc() {
        this.state.pendingRequests++;
        console.log("inc() called, pendingRequests:", this.state.pendingRequests);
        this.update();
    },

    dec() {
        this.state.pendingRequests--;
        if (this.state.pendingRequests < 0) this.state.pendingRequests = 0;
        console.log("dec() called, pendingRequests:", this.state.pendingRequests);
        this.update();
    },

    installAxios() {
        axios.interceptors.request.use((config) => {
            if (config.skipLoading) return config;
            console.log("Axios request -> inc");
            this.inc();
            return config;
        });

        axios.interceptors.response.use(
            (res) => {
                if (!res.config?.skipLoading) {
                    console.log("Axios response -> dec");
                    this.dec();
                }
                return res;
            },
            (err) => {
                if (!err.config?.skipLoading) {
                    console.log("Axios error -> dec");
                    this.dec();
                }
                return Promise.reject(err);
            }
        );
    },

    installInertia() {
        Inertia.on("start", (e) => {
            if (e.detail?.visit?.skipLoading) return;
            console.log("Inertia start -> setting inertiaLoading = true");
            this.state.inertiaLoading = true;
            this.update();
        });

        Inertia.on("finish", (e) => {
            if (e.detail?.visit?.skipLoading) return;
            console.log("Inertia finish -> setting inertiaLoading = false");
            this.state.inertiaLoading = false;
            this.update();
        });
    },
},

};
</script>
