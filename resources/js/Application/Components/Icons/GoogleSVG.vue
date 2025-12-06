<template>
<img :src="'/images/icons/google.png'" :width="wi" :height="he"/>
</template>
<script>
export default {
    name: "GoogleSVG",
    props:{
        he:{
            type:[String,Number],
        },
        wi:{
            type:[String,Number],
        }
    }
};
</script>
