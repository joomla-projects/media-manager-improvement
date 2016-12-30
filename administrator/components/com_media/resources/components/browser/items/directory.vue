<template>
    <div>
        dir
        <small>
            {{ item.name }}
        </small>
    </div>
</template>

<script>
    export default {
        name: 'media-browser-item-directory',
        props: ['item'],
    }
</script>