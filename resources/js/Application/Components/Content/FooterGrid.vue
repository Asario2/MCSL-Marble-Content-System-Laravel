<template>
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6 mb-10">
        <!-- Start Col -->
        <div class="px-2 col-span-full lg:col-span-2">
            <a
                class="flex-none text-xl font-semibold"
                href="#"
                aria-label="Brand"
                >{{ $page.props.version.brandname }}</a
            >
            <p
                class="mt-3 text-xs sm:text-sm text-layout-sun-600 dark:text-layout-night-600"
            >
                {{ $page.props.version.copyrightname }}
                <br />
                Version {{ $page.props.version.versionnr }} vom
                {{ $page.props.version.versionsdatum }}
            </p>
        </div>
        <!-- End Col -->

        <!-- Start Col -->
        <div>
            <h4
                class="px-2 text-sm font-semibold text-layout-sun-800 dark:text-layout-night-800"
            >
                Starter Eleven
            </h4>

            <div class="mt-3 grid space-y-3 text-sm">
                <p>
                    <a class="footer_link" :href="route('home.index')">Home</a>
                </p>
                <p>
                    <a class="footer_link" :href="route('home.imprint')"
                        >Impressum</a
                    >
                </p>
                <p>
                    <a class="footer_link" :href="route('home.privacy')"
                        >Datenschutzerklärung</a
                    >
                </p>
                <p>
                    <a class="footer_link" :href="route('home.terms')">AGB</a>
                </p>
            </div>
        </div>
        <!-- End Col -->

        <!-- Start Col -->
        <div>
            <h4
                class="px-2 text-sm font-semibold text-layout-sun-800 dark:text-layout-night-800"
            >
                Anwendungen
            </h4>

            <div class="mt-3 grid space-y-3 text-sm">
                <p v-if="$page.props.userdata.is_admin">
                    <a class="footer_link" :href="route('admin.dashboard')"
                        >Administrator</a
                    >
                </p>
                <p v-if="$page.props.userdata.is_employee">
                    <a class="footer_link" :href="route('employee.dashboard')"
                        >Mitarbeiter</a
                    >
                </p>
                <p v-if="$page.props.userdata.is_customer">
                    <a class="footer_link" :href="route('customer.dashboard')"
                        >Kunde</a
                    >
                </p>
            </div>
        </div>
        <!-- End Col -->
    </div>
</template>
<script>
export default {
    name: "Components_Content_FooterGrid",
};
</script>

