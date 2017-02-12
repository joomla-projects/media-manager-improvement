<template>
    <div class="media-browser-item-file">
        <div class="media-browser-item-icon">
            <i class="icon icon-file"></i>
        </div>
        <div class="media-browser-item-info">
            {{ item.name }}
        </div>
    </div>
</template>

<script>
    export default {
        name: 'media-browser-item-file',
        props: ['item'],
    }
</script>