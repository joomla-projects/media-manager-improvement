<template>
    <div>
        <p class="media-browser-icon">
            <i class="icon icon-image"></i>
        </p>
        <p class="text-center">
            {{ item.name }}
        </p>
    </div>
</template>

<script>
    export default {
        name: 'media-browser-item-image',
        props: ['item'],
    }
</script>