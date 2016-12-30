<template>
    <div>
        <p class="media-browser-icon">
            <i class="icon icon-file"></i>
        </p>
        <p class="text-center">
            {{ item.name }}
        </p>
    </div>
</template>

<script>
    export default {
        name: 'media-browser-item-file',
        props: ['item'],
    }
</script>