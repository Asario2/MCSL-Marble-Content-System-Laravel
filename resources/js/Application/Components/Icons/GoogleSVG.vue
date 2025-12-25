<template>
<img :src="'/images/icons/google.png'" :width="wi" :height="he"/>
</template>
<script>
export default {
    name: "GoogleSVG",
    props:{
        he:{
            type:[String,Number],
            default:24,
        },
        wi:{
            type:[String,Number],
            default:24,
        }
    }
};
</script>
