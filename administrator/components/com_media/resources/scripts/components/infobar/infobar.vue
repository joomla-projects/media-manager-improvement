<template>
    <div class="media-infobar">
        <div class="card">
            <div class="card-header">
                Item name
            </div>
            <div class="card-block">
                Some content
            </div>
        </div>
    </div>
</template>
<style>
    .media-infobar {
        background-color: #fafafa;
        height: 100%;
        overflow: hidden;
        position: absolute;
        right: 0;
        top: 0;
        width: 365px;
        z-index: 4;
    }
    .media-infobar .card {
        height: 100%;
        border-top: 0;
        border-right: 0;
        border-bottom: 0;
        border-radius: 0;
    }

</style>
<script>
    export default {
        name: 'media-infobar',
    }
</script>