<template>
    <div v-if="xis_enabled" :class="['ai-button', big ? 'big' : 'small', bigger ? 'bigger' : '']" @click.stop>
        <a :href="AiRoute">
            <img :key="localDma" :src="'/images/icons/ai-' + (localDma || 'dark') + '.png'"
                 alt="Made with AI"
                 title="Made with AI"
                 class="ai-button-image"
                 @error="handleImageError" />
        </a>
    </div>
</template>

<script>
export default {
    name: "AiButton",
    props: {
        big: Boolean,
        bigger: Boolean,
        dma: String,
        nohome: Boolean,
        xis_enabled:{
            type:[Boolean,Number],
            default:true,
        },
    },
    computed: {
        localDma() {
            return this.dma || 'dark';
        },
        AiRoute(){
            if(this.nohome){
                return "/ai";
            }
            return "/home/ai";
        },
    },
    methods: {
        handleImageError(event) {
            console.error('Bild konnte nicht geladen werden:', event.target.src);
        }
    }
};
</script>

<style  >
.ai-button {
    position: absolute;
    bottom: 0px;
    right: 0px;
    z-index: 38;
    width:64px;
}

.ai-button-image {
    width: 64px;
    height: 64px;
    z-index:39;
    background-color: transparent;

}

.big .ai-button {
    bottom: 5px;
    right: 5px;
}

.small .ai-button {
    bottom:0px;
    right:  0px;
}
.bigger .ai-button{
bottom:0;
right:128px !important;
width:64px;
}
</style>

