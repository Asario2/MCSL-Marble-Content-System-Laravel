<template>
    <!-- Box nur so groß wie Inhalt -->
    <div class="border-[2px] border-black p-4 rounded self-start bg-layout-sun-2000 dark:bg-layout-night-200 fixed w-[230px]">
        <div v-if="quotes.length" class="flex flex-col gap-3">
            <h2 class="leading-[32px] -mt-[0px] -mb-[0px] pl-[8px] font-semibold mb-2" style="font-family:'blackchancery';color:#000;">
                Spruch des Monats
            </h2>
            <div v-for="(quoteItem, index) in quotes"
                :key="index"
                class="p-2 font-bold rounded bg-layout-sun-1000 dark:bg-layout-night-200">
                <span v-html="rumLaut(quoteItem.text)"></span>
                <p class="text-gray-400" v-html="rumLaut(quoteItem.author)"></p>
            </div>
        </div>
        <div v-else>
            <p>Die Sprüche werden geladen...</p>
        </div>
    </div>
</template>

<script>
export default {
    name: "poems",
    props: {
        quotes: {
            type: Array,
            required: true
        },
        rumLaut: {
            type: Function,
            required: true
        }
    }
};
</script>
