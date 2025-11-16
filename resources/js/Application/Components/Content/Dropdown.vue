<template>
    <div class="relative w-full h-full">
        <div @click="open = !open">
            <slot name="trigger"></slot>
        </div>

        <!-- Full Screen Dropdown Overlay -->
        <div
            v-show="open"
            class="fixed inset-0 z-30"
            @click="open = false"
        ></div>

        <transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
        >
            <div
                v-show="open"
                class="absolute z-40 mt-2 rounded-md shadow-lg bg-layout-sun-0 dark:bg-layout-night-0 p-0.5"
                :class="[widthClass, alignmentClasses]"
                style="display: none"
                @click="closeDropdown"
            >
                <div
                    class="rounded-md ring-1 ring-black ring-opacity-5"
                    :class="contentClasses"
                >
                    <slot name="content"></slot>
                </div>
            </div>
        </transition>
    </div>
</template>
<script>
export default {
    name: "Components_Content_Dropdown",

    props: {
        align: {
            type: String,
            default: "right",
        },
        width: {
            type: String,
            default: "96",
        },
        autoClose: {
            type: Boolean,
            default: true,
        },
        contentClasses: {
            type: Array,
            default: function () {
                return [
                    "px-4",
                    "py-2",
                    "text-xs",
                    "bg-layout-sun-50",
                    "text-layout-sun-900",
                    "dark:bg-layout-night-50",
                    "dark:text-layout-night-900",
                ];
            },
        },
    },

    data() {
        return {
            open: false,
        };
    },

    methods: {
        closeDropdown() {
            if (this.autoClose) {
                this.open = false;
            }
        },

        toggleDropdown() {
            this.open = !this.open;
        },

        closeOnEscape(e) {
            if (this.open && e.keyCode === 27) {
                // KeyCode 27 entspricht der Esc-Taste
                this.closeDropdown();
            }
        },
    },

    computed: {
        widthClass() {
            const widths = {
                48: "w-48",
                64: "w-64",
                72: "w-72",
                96: "w-96",
            };
            return widths[this.width];
        },

        alignmentClasses() {
            if (this.align === "left") {
                return "origin-top-left left-0";
            } else if (this.align === "right") {
                return "origin-top-right right-0";
            } else {
                return "origin-top";
            }
        },
    },

    mounted() {
        document.addEventListener("keydown", this.closeOnEscape);
    },

    beforeDestroy() {
        document.removeEventListener("keydown", this.closeOnEscape);
    },
};
</script>

