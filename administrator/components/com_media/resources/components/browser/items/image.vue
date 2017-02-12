<template>
    <div class="media-browser-image">
        <div class="image-background">
            <div class="image-cropped" :style="{ backgroundImage: 'url(/images' + item.path + ')' }"></div>
        </div>
        <p class="text-center">
            {{ item.name }}
        </p>
    </div>
</template>

<style>
    .image-background {
        background-position: 0px 0px, 10px 10px;
        background-size: 20px 20px;
        background-color: white;
        background-image: linear-gradient(45deg, #eee 25%, transparent 25%, transparent 75%, #eee 75%, #eee 100%), linear-gradient(45deg, #eee 25%, transparent 25%, transparent 75%, #eee 75%, #eee 100%);
    }

    .image-cropped {
        width: 100px;
        height: 100px;
        margin: 0 auto;
        background-position: center;
        background-size: cover;
    }
</style>

<script>
    // TODO DN get the base path and make the path dynamic
    export default {
        name: 'media-browser-item-image',
        props: ['item'],
    }
</script>