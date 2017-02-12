<template>
    <div class="media-browser-image">
        <div class="media-browser-item-icon">
            <div class="image-brackground">
                <div class="image-cropped" :style="{ backgroundImage: 'url(/images' + item.path + ')' }"></div>
            </div>
        </div>
        <div class="media-browser-item-info">
            {{ item.name }}
        </div>
    </div>
</template>

<style>
    .image-brackground {
        width: 100%;
        height: 100%;
        background-position: 0px 0px, 10px 10px;
        background-size: 20px 20px;
        background-color: white;
        background-image: linear-gradient(45deg, #eee 25%, transparent 25%, transparent 75%, #eee 75%, #eee 100%), linear-gradient(45deg, #eee 25%, transparent 25%, transparent 75%, #eee 75%, #eee 100%);
    }
    .image-cropped {
        width: 100%;
        height: 100%;
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