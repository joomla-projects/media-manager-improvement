<template>
    <div>
        <p class="media-browser-icon">
            <i class="icon icon-folder-close"></i>
        </p>
        <p class="text-center">
            {{ item.name }}
        </p>
    </div>
</template>

<script>
    export default {
        name: 'media-browser-item-directory',
        props: ['item'],
    }
</script>