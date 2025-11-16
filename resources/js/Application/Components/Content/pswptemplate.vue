<template>
    <div class="preview-img-list">
        <img class="preview-img-item" v-for="(item, index) in items"
            :src="'/images/images/thumbs/' + item.src" @click="$photoswipe.open(index, items)">
    </div>
</template>

<style scoped>
.preview-img-list {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
}
.preview-img-item {
    margin: 5px;
    max-width: 100px;
    max-height: 100px;
}
</style>

<script>
import photoawipe_new from "@/Application/Components/photoswipe_new.vue";   
export default {

        name: "PswpTemplate",
        components: {
            photoswipe_new,
        },
        data () {
            return {
                items: [{
                    src: 'Chrysanthemum.jpg',
                    w: 1024,
                    h: 768
                }, {
                    src: 'Desert.jpg',
                    w: 1024,
                    h: 768
                }, {
                    src: 'Hydrangeas.jpg',
                    w: 1024,
                    h: 768
                }, {
                    src: 'Jellyfish.jpg',
                    w: 1024,
                    h: 768
                }, {
                    src: 'Koala.jpg',
                    w: 1024,
                    h: 768
                }, {
                    src: 'Lighthouse.jpg',
                    w: 1024,
                    h: 768
                }, {
                    // src: 'Penguins.jpg',
                    w: 1024,
                    h: 768
                }, {
                    src: 'Tulips.jpg',
                    w: 1024,
                    h: 768
                }]
            }
        }
    }

</script>

