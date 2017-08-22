<template>
    <div class="media-disk" @click="selectDisk">
        <div class="media-disk-name">{{ disk.displayName }}</div>
    </div>
</template>

<script>
    export default {
        name: 'media-disk',
        props: ['disk'],
        computed: {

        }
    }
</script>
